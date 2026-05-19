<?php

declare(strict_types=1);

$app = require __DIR__ . '/../bootstrap/app.php';

return \App\Support\LegacyConfig::build($app->config()->all());
