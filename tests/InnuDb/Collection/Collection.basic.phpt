<?php
use Tester\Assert;
use Cz\InnuDb\Collection;

require __DIR__ . '/../bootstrap.php';
require __DIR__ . '/../../../vendor/autoload.php';
require __DIR__ . '/../../../src/Collection.php';

$data = include __DIR__ . '/data.php';

$collection = new Collection($data['persons']);


// Get Data
Assert::same($data['persons'], $collection->getData());


// Find
$item = $collection->find('saxana');
Assert::same('Saxana (movie)', $item['story']);

