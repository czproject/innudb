<?php
use Tester\Assert;
use Cz\InnuDb\Collection;

require __DIR__ . '/../bootstrap.php';
require __DIR__ . '/../../../vendor/autoload.php';
require __DIR__ . '/../../../src/Collection.php';

$data = include __DIR__ . '/data.php';

$collection = new Collection($data['persons']);

// Test 1
$coll = clone $collection;
$coll->where('name', 'Harry Potter');

$ids = array();
foreach($coll as $id => $item)
{
	$ids[] = $id;
}

Assert::same(array('harry-potter'), $ids);



// Test 2
$coll = clone $collection;
$coll->where('age >', 20);

$ids = array();
foreach($coll as $id => $item)
{
	$ids[] = $id;
}

Assert::same(array('gandalf', 'saxana'), $ids);



// Test 3
$coll = clone $collection;
$coll->where('age >=', 20);

$ids = array();
foreach($coll as $id => $item)
{
	$ids[] = $id;
}

Assert::same(array('harry-potter', 'gandalf', 'saxana'), $ids);



// Test 4
$coll = clone $collection;
$coll->where('age <=', 20);

$ids = array();
foreach($coll as $id => $item)
{
	$ids[] = $id;
}

Assert::same(array('harry-potter'), $ids);



// Test 5
$coll = clone $collection;
$coll->where('age =', 400);

$ids = array();
foreach($coll as $id => $item)
{
	$ids[] = $id;
}

Assert::same(array('saxana'), $ids);



// Test 6
$coll = clone $collection;
$coll->where('age >=', 400);

$ids = array();
foreach($coll as $id => $item)
{
	$ids[] = $id;
}

Assert::same(array('gandalf', 'saxana'), $ids);



// Test 7
$coll = clone $collection;
$coll->where('age <=', 400);

$ids = array();
foreach($coll as $id => $item)
{
	$ids[] = $id;
}

Assert::same(array('harry-potter', 'saxana'), $ids);



// Test 8
$coll = clone $collection;
$coll->where('age <=', 400)
	->where(array(
		'name' => 'Harry Potter',
	));

$ids = array();
foreach($coll as $id => $item)
{
	$ids[] = $id;
}

Assert::same(array('harry-potter'), $ids);



// Test 9
$coll = clone $collection;
$coll->where('age <=', 400)
	->where(array(
		'       age     <        ' /*spaces*/ => 400,
	));

$ids = array();
foreach($coll as $id => $item)
{
	$ids[] = $id;
}

Assert::same(array('harry-potter'), $ids);



// Test 10
$coll = clone $collection;
$coll->where(array(
		'       age     <        ' /*spaces*/ => 4000,
	))
	->where('age >', 400);

$ids = array();
foreach($coll as $id => $item)
{
	$ids[] = $id;
}

Assert::same(array('gandalf'), $ids);


// Test 11
Assert::exception(function() use ($coll) {
	$coll->where('name');
}, 'Cz\InnuDb\CollectionException');

