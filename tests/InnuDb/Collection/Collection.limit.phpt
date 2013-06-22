<?php
use Tester\Assert;
use Cz\InnuDb\Collection;

require __DIR__ . '/../bootstrap.php';
require __DIR__ . '/../../../vendor/autoload.php';
require __DIR__ . '/../../../src/Collection.php';

$data = include __DIR__ . '/data.php';

$collection = new Collection($data['persons']);

// Count
Assert::same(3, $collection->getCount());

$collection->limit(1);
Assert::same(1, $collection->getCount());


$collection->limit(2, 1);
Assert::same(2, $collection->getCount());


$collection->limit(2, 3);
Assert::same(0, $collection->getCount());


// Fetch
$collection->limit(NULL);
$ids = array();
foreach($collection as $id => $item)
{
	$ids[] = $id;
}

Assert::same(array('harry-potter', 'gandalf', 'saxana'), $ids);



$collection->limit(1);
$ids = array();
foreach($collection as $id => $item)
{
	$ids[] = $id;
}

Assert::same(array('harry-potter'), $ids);



$collection->limit(2, 1);
$ids = array();
foreach($collection as $id => $item)
{
	$ids[] = $id;
}

Assert::same(array('gandalf', 'saxana'), $ids);



$collection->limit(2, 3);
$ids = array();
foreach($collection as $id => $item)
{
	$ids[] = $id;
}

Assert::same(array(), $ids);

