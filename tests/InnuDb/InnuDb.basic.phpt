<?php
use Tester\Assert;
use Cz\InnuDb\Collection;
use Cz\InnuDb\Loader;
use Cz\InnuDb\InnuDb;

require __DIR__ . '/bootstrap.php';
require __DIR__ . '/../../vendor/autoload.php';
require __DIR__ . '/../../loader.php';

$path = __DIR__ . '/Collection/data.php';
$data = include $path;

$loader = new Loader;
$innudb = new InnuDb($loader, $path);

Assert::same($data, $innudb->getData());
Assert::same($path, $innudb->getPath());



// Creating of collection
$collection = $innudb->createCollection();
Assert::same($data, $collection->getData());


// Creating of collection - get subset
$collection = $innudb->createCollection('persons');
Assert::same($data['persons'], $collection->getData());


// Creating of collection - missing subset
Assert::throws(function() use ($innudb) {
	$innudb->createCollection('missing-subset');
}, 'Cz\InnuDb\InnuDbException', "Collection: data subset 'missing-subset' missing.");



// Missing data file
$path = __DIR__ . '/missing-data-file.php';
Assert::throws(function() use ($loader, $path) {
	$innudb = new InnuDb($loader, $path);
}, 'Cz\InnuDb\InnuDbException', "Data file not found in $path");

