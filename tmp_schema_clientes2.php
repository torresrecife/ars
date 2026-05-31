<?php
include __DIR__ . '/inc/bootstrap.php';
foreach (array('dados', 'carteira') as $table) {
    echo 'TABLE=' . $table . PHP_EOL;
    $result = mysqli_query($conexao4, 'SHOW COLUMNS FROM ' . $table);
    while ($row = mysqli_fetch_assoc($result)) {
        echo $row['Field'] . '|' . $row['Type'] . PHP_EOL;
    }
    echo PHP_EOL;
}
