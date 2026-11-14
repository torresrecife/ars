<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * Passa as tabelas de latin1 para utf8mb4 sem estragar os acentos.
 *
 * Algumas colunas latin1 podem guardar os bytes do UTF-8 (o legado gravava
 * com a conexao em latin1; o "Ê" vira C3 8A). Um ALTER TABLE ...
 * CONVERT TO CHARACTER SET converteria esses bytes de novo e estragaria o texto.
 * No banco local do ARS (copia de 10/2026) os acentos estavam em latin1 de
 * verdade (andamentos.nome); o comando trata os dois casos, valor a valor.
 *
 * Por tabela: as colunas de texto passam para o tipo binario equivalente (os
 * bytes ficam como estao), as linhas que nao sao UTF-8 valido sao convertidas
 * de latin1 e as colunas voltam ao tipo de texto, agora em utf8mb4.
 *
 * Por seguranca o padrao e somente relatorio; use --apply para converter.
 */
class ConvertDatabaseToUtf8mb4 extends Command
{
    protected $signature = 'ars:converter-utf8mb4
        {--apply : Converte as tabelas (sem esta flag apenas relata)}
        {--connection= : Conexao do banco (padrao: a conexao padrao)}
        {--table=* : Limita a uma ou mais tabelas}
        {--collation=utf8mb4_unicode_ci : Collation final das tabelas}';

    protected $description = 'Converte as tabelas de latin1 para utf8mb4, preservando o texto ja gravado em UTF-8.';

    const BINARY_TYPES = [
        'char' => 'varbinary',
        'varchar' => 'varbinary',
        'tinytext' => 'tinyblob',
        'text' => 'blob',
        'mediumtext' => 'mediumblob',
        'longtext' => 'longblob',
    ];

    protected $db;

    protected $engines = [];

    public function handle()
    {
        $this->db = DB::connection($this->option('connection') ?: null);
        $schema = $this->db->getDatabaseName();
        $charset = 'utf8mb4';
        $collation = $this->option('collation');

        if (!preg_match('/^utf8mb4_\w+$/', $collation)) {
            $this->error("Collation invalida: {$collation} (use uma utf8mb4_*).");
            return 1;
        }

        $tables = $this->tablesToConvert($schema);
        if (!$tables) {
            $this->info("Nada a converter: todas as tabelas de {$schema} ja estao em {$charset}.");
            return 0;
        }

        $rows = [];
        $accented = 0;
        foreach ($tables as $table => $columns) {
            foreach ($columns as $column) {
                $column->stats = $this->columnStats($table, $column);
                $accented += $column->stats['nao_ascii'];
                if ($column->stats['latin1']) {
                    $rows[] = [$table . '.' . $column->COLUMN_NAME, $column->stats['nao_ascii'], $column->stats['latin1']];
                }
            }
        }

        $this->info(count($tables) . " tabela(s) de {$schema} a converter para {$charset} ({$collation}).");
        $this->line("{$accented} valor(es) com acento ja em UTF-8 (os bytes ficam como estao).");
        if ($rows) {
            $this->line('Colunas com valores em latin1 de verdade (serao convertidos):');
            $this->table(['Coluna', 'Com acento em UTF-8', 'Em latin1 de verdade'], $rows);
        }

        foreach ($this->rowSizeProblems($schema, $tables) as $table => $size) {
            $this->warn("{$table} fica em latin1: em utf8mb4 a linha teria ~{$size} bytes (limite 65535).");
            unset($tables[$table], $this->engines[$table]);
        }

        $problems = $this->indexProblems($schema, $tables);
        if ($problems) {
            $this->error('Indices que passariam do limite do InnoDB (3072 bytes) em utf8mb4; nada foi alterado:');
            foreach ($problems as $problem) {
                $this->line('  - ' . $problem);
            }
            return 1;
        }

        $myisam = array_keys(array_filter($this->engines, function ($engine) {
            return strcasecmp((string) $engine, 'InnoDB') !== 0;
        }));
        if ($myisam) {
            $this->line('Passam para InnoDB (limite de indice maior): ' . implode(', ', $myisam));
        }

        if (!$this->option('apply')) {
            $this->warn('Somente relatorio. Rode com --apply para converter (faca uma copia do banco antes).');
            return 0;
        }

        // Tabelas do legado tem datas com padrao 0000-00-00, que o ALTER recusa no modo estrito.
        $modes = array_diff(explode(',', $this->db->selectOne('SELECT @@SESSION.sql_mode AS m')->m), ['NO_ZERO_DATE', 'NO_ZERO_IN_DATE']);
        $this->db->statement('SET SESSION sql_mode = ' . $this->db->getPdo()->quote(implode(',', $modes)));

        foreach ($tables as $table => $columns) {
            $this->convertTable($table, $columns, $charset, $collation);
            $this->line("  {$table}: ok");
        }

        $this->db->statement('ALTER DATABASE ' . $this->wrap($schema) . " CHARACTER SET {$charset} COLLATE {$collation}");
        $this->info('Conversao concluida.');

        return 0;
    }

    /**
     * Tabelas com colunas de texto fora do utf8mb4 ou com charset padrao antigo.
     */
    protected function tablesToConvert($schema)
    {
        $filter = array_filter((array) $this->option('table'));

        $tableRows = $this->db->select(
            "SELECT TABLE_NAME, TABLE_COLLATION, ENGINE FROM information_schema.TABLES
             WHERE TABLE_SCHEMA = ? AND TABLE_TYPE = 'BASE TABLE' ORDER BY TABLE_NAME",
            [$schema]
        );

        $columnRows = $this->db->select(
            "SELECT TABLE_NAME, COLUMN_NAME, DATA_TYPE, COLUMN_TYPE, IS_NULLABLE, COLUMN_DEFAULT,
                    CHARACTER_SET_NAME, COLUMN_COMMENT, EXTRA
             FROM information_schema.COLUMNS
             WHERE TABLE_SCHEMA = ? AND CHARACTER_SET_NAME IS NOT NULL AND CHARACTER_SET_NAME <> 'utf8mb4'
               AND EXTRA NOT LIKE '%GENERATED%'
             ORDER BY TABLE_NAME, ORDINAL_POSITION",
            [$schema]
        );

        $columns = [];
        foreach ($columnRows as $column) {
            $columns[$column->TABLE_NAME][] = $column;
        }

        $tables = [];
        $this->engines = [];
        foreach ($tableRows as $row) {
            if ($filter && !in_array($row->TABLE_NAME, $filter, true)) {
                continue;
            }
            if (isset($columns[$row->TABLE_NAME]) || strpos((string) $row->TABLE_COLLATION, 'utf8mb4_') !== 0) {
                $tables[$row->TABLE_NAME] = $columns[$row->TABLE_NAME] ?? [];
                $this->engines[$row->TABLE_NAME] = $row->ENGINE;
            }
        }

        return $tables;
    }

    protected function columnStats($table, $column)
    {
        $name = $this->wrap($column->COLUMN_NAME);
        $binary = "CAST({$name} AS BINARY)";
        // Bytes fora do conjunto viram NULL no CONVERT: assim se separa o que nao e ASCII/UTF-8.
        $isLatin1 = "{$name} IS NOT NULL AND CONVERT({$binary} USING utf8mb4) IS NULL";
        $overflow = '0';

        if (in_array($column->DATA_TYPE, ['char', 'varchar'], true)) {
            preg_match('/\((\d+)\)/', $column->COLUMN_TYPE, $match);
            $limit = (int) ($match[1] ?? 0);
            // Na fase binaria a coluna guarda ate N bytes; o UTF-8 do latin1 pode ser maior.
            $overflow = "SUM({$isLatin1} AND LENGTH(CONVERT(CONVERT({$binary} USING latin1) USING utf8mb4)) > {$limit})";
        }

        $row = $this->db->selectOne(
            "SELECT
                COALESCE(SUM(LENGTH({$binary}) <> CHAR_LENGTH(CONVERT({$binary} USING utf8mb4))), 0) AS nao_ascii,
                COALESCE(SUM({$isLatin1}), 0) AS latin1,
                COALESCE({$overflow}, 0) AS estoura
             FROM " . $this->wrap($table)
        );

        return ['nao_ascii' => (int) $row->nao_ascii, 'latin1' => (int) $row->latin1, 'estoura' => (int) $row->estoura];
    }

    protected function convertTable($table, array $columns, $charset, $collation)
    {
        $wrappedTable = $this->wrap($table);
        $toBinary = [];
        $toText = [];

        foreach ($columns as $column) {
            $name = $this->wrap($column->COLUMN_NAME);
            $textType = preg_replace('/\s+(CHARACTER SET|COLLATE)\s+\w+/i', '', $column->COLUMN_TYPE);

            if (isset(self::BINARY_TYPES[$column->DATA_TYPE])) {
                $binaryType = preg_replace('/^' . $column->DATA_TYPE . '\b/i', self::BINARY_TYPES[$column->DATA_TYPE], $textType);
                if (!empty($column->stats['estoura'])) {
                    // O latin1 convertido ocupa mais bytes que o tamanho em caracteres: folga so na fase binaria.
                    $binaryType = preg_replace_callback('/\((\d+)\)/', function ($match) {
                        return '(' . ((int) $match[1] * 4) . ')';
                    }, $binaryType);
                }
                $toBinary[] = "MODIFY {$name} {$binaryType}" . $this->columnAttributes($column, false);
            }

            $toText[] = "MODIFY {$name} {$textType} CHARACTER SET {$charset} COLLATE {$collation}" . $this->columnAttributes($column, true);
        }

        if (strcasecmp((string) ($this->engines[$table] ?? 'InnoDB'), 'InnoDB') !== 0) {
            array_unshift($toBinary, 'ENGINE=InnoDB');
        }

        if ($toBinary) {
            $this->db->statement("ALTER TABLE {$wrappedTable} " . implode(', ', $toBinary));

            foreach ($columns as $column) {
                if (!isset(self::BINARY_TYPES[$column->DATA_TYPE])) {
                    continue;
                }
                $name = $this->wrap($column->COLUMN_NAME);
                $this->db->statement(
                    "UPDATE {$wrappedTable}
                     SET {$name} = CAST(CONVERT(CONVERT({$name} USING latin1) USING utf8mb4) AS BINARY)
                     WHERE {$name} IS NOT NULL AND CONVERT({$name} USING utf8mb4) IS NULL"
                );
            }
        }

        $this->db->statement(
            "ALTER TABLE {$wrappedTable} DEFAULT CHARACTER SET {$charset} COLLATE {$collation}"
            . ($toText ? ', ' . implode(', ', $toText) : '')
        );
    }

    /**
     * Tabelas cuja linha, com 4 bytes por caractere, passaria do limite do MySQL.
     * BLOB/TEXT nao contam (ficam fora da linha).
     */
    protected function rowSizeProblems($schema, array $tables)
    {
        if (!$tables) {
            return [];
        }

        $placeholders = implode(',', array_fill(0, count($tables), '?'));
        $columns = $this->db->select(
            "SELECT TABLE_NAME, DATA_TYPE, CHARACTER_MAXIMUM_LENGTH, CHARACTER_SET_NAME
             FROM information_schema.COLUMNS
             WHERE TABLE_SCHEMA = ? AND TABLE_NAME IN ({$placeholders})",
            array_merge([$schema], array_keys($tables))
        );

        $sizes = [];
        foreach ($columns as $column) {
            if (in_array($column->DATA_TYPE, ['char', 'varchar'], true)) {
                $bytes = (int) $column->CHARACTER_MAXIMUM_LENGTH * 4;
                $size = $bytes + ($bytes > 255 ? 2 : 1);
            } elseif (preg_match('/(text|blob)$/', $column->DATA_TYPE)) {
                $size = 12;
            } else {
                // Inteiros, datas e decimais: 8 bytes cobre com folga.
                $size = 8;
            }
            $sizes[$column->TABLE_NAME] = ($sizes[$column->TABLE_NAME] ?? 0) + $size;
        }

        return array_filter($sizes, function ($size) {
            return $size > 65535;
        });
    }

    /**
     * Indices que, com 4 bytes por caractere, passariam de 3072 bytes.
     */
    protected function indexProblems($schema, array $tables)
    {
        if (!$tables) {
            return [];
        }

        $placeholders = implode(',', array_fill(0, count($tables), '?'));
        $parts = $this->db->select(
            "SELECT s.TABLE_NAME, s.INDEX_NAME, s.SUB_PART, c.CHARACTER_MAXIMUM_LENGTH, c.CHARACTER_SET_NAME
             FROM information_schema.STATISTICS s
             JOIN information_schema.COLUMNS c
               ON c.TABLE_SCHEMA = s.TABLE_SCHEMA AND c.TABLE_NAME = s.TABLE_NAME AND c.COLUMN_NAME = s.COLUMN_NAME
             WHERE s.TABLE_SCHEMA = ? AND s.TABLE_NAME IN ({$placeholders})",
            array_merge([$schema], array_keys($tables))
        );

        $bytes = [];
        foreach ($parts as $part) {
            $key = $part->TABLE_NAME . '.' . $part->INDEX_NAME;
            $chars = $part->SUB_PART ?: $part->CHARACTER_MAXIMUM_LENGTH;
            // Colunas que nao sao texto contam pouco; 8 bytes cobre inteiros e datas.
            $bytes[$key] = ($bytes[$key] ?? 0) + ($part->CHARACTER_SET_NAME !== null ? (int) $chars * 4 : 8);
        }

        $problems = [];
        foreach ($bytes as $key => $size) {
            if ($size > 3072) {
                $problems[] = "{$key}: {$size} bytes";
            }
        }

        return $problems;
    }

    /**
     * NULL/NOT NULL, DEFAULT e comentario da coluna, para nao perder nada no MODIFY.
     */
    protected function columnAttributes($column, $isText)
    {
        $sql = $column->IS_NULLABLE === 'YES' ? ' NULL' : ' NOT NULL';
        $isBlobLike = in_array($column->DATA_TYPE, ['tinytext', 'text', 'mediumtext', 'longtext'], true);

        if ($column->COLUMN_DEFAULT !== null && !$isBlobLike) {
            $sql .= ' DEFAULT ' . $this->db->getPdo()->quote($column->COLUMN_DEFAULT);
        } elseif ($column->COLUMN_DEFAULT === null && $column->IS_NULLABLE === 'YES' && !$isBlobLike) {
            $sql .= ' DEFAULT NULL';
        }

        if ($column->COLUMN_COMMENT !== '') {
            $sql .= ' COMMENT ' . $this->db->getPdo()->quote($column->COLUMN_COMMENT);
        }

        return $sql;
    }

    protected function wrap($identifier)
    {
        return '`' . str_replace('`', '``', $identifier) . '`';
    }
}
