<?php

$root = dirname(__DIR__, 3);

if (! is_file($root.'/vendor/autoload.php')) {
    throw new RuntimeException('Place this plugin in an Azuriom installation before running its tests.');
}

$loader = require $root.'/vendor/autoload.php';
$loader->addPsr4('Azuriom\\Plugin\\PlayerNotes\\', dirname(__DIR__).'/src');

if (is_file(dirname(__DIR__).'/.artifacts/tools/vendor/autoload.php')) {
    require dirname(__DIR__).'/.artifacts/tools/vendor/autoload.php';
}

require __DIR__.'/TestCase.php';
