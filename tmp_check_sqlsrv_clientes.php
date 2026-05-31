<?php
include __DIR__ . '/inc/bootstrap.php';
var_dump(is_resource($conexao1), $conexao1);
if (function_exists('sqlsrv_query') && $conexao1) {
    $q = sqlsrv_query($conexao1, "SELECT TOP 3 p.Carteira FROM Processo AS p WITH (NOLOCK) GROUP BY p.Carteira ORDER BY p.Carteira");
    var_dump($q);
    if ($q === false) {
        var_dump(sqlsrv_errors());
    } else {
        while ($row = sqlsrv_fetch_array($q, SQLSRV_FETCH_ASSOC)) {
            var_export($row);
            echo PHP_EOL;
        }
    }
} else {
    echo "sqlsrv indisponivel";
}
