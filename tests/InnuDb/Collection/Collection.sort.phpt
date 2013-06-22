<?php
use Tester\Assert;
use Cz\InnuDb\Collection;

require __DIR__ . '/../bootstrap.php';
require __DIR__ . '/../../../vendor/autoload.php';
require __DIR__ . '/../../../src/Collection.php';

function fetchColumn($collection, $column)
{
	$item = $collection->fetch();
	
	if($item !== FALSE)
	{
		return $item[$column];
	}
	
	return FALSE;
}

$basicData = array(
	array(
		'name' => 'Harry Potter',
		'age' => 20,
	),
	
	array(
		'name' => 'Gandalf The White',
		'age' => 2000,
	),
	
	array(
		'name' => 'Saxana',
		'age' => 400,
	),
);


// by one column by default ASC
$collection = new Collection($basicData);

$collection->sort('name');
Assert::same('Gandalf The White', fetchColumn($collection, 'name'));
Assert::same('Harry Potter', fetchColumn($collection, 'name'));
Assert::same('Saxana', fetchColumn($collection, 'name'));


// by one column by DESC
$collection = new Collection($basicData);

$collection->sort('name', 'DESC');
Assert::same('Saxana', fetchColumn($collection, 'name'));
Assert::same('Harry Potter', fetchColumn($collection, 'name'));
Assert::same('Gandalf The White', fetchColumn($collection, 'name'));


// by more column
$collection = new Collection(array(
	array(
		'name' => 'Adam',
		'age' => 198,
	),
	
	array(
		'name' => 'Adam',
		'age' => 23,
	),
	
	array(
		'name' => 'Adam',
		'age' => 28,
	),
	
	array(
		'name' => 'Adam',
		'age' => 16,
	),
	
	array(
		'name' => 'Adam',
		'age' => 80,
	),
	
	array(
		'name' => 'Adam',
		'age' => 90,
	),
	
	array(
		'name' => 'Adam',
		'age' => 70,
	),
));

$collection->sort('name', 'DESC')
	->sort('age');
Assert::same(16, fetchColumn($collection, 'age'));
Assert::same(23, fetchColumn($collection, 'age'));
Assert::same(28, fetchColumn($collection, 'age'));
Assert::same(70, fetchColumn($collection, 'age'));
Assert::same(80, fetchColumn($collection, 'age'));
Assert::same(90, fetchColumn($collection, 'age'));
Assert::same(198, fetchColumn($collection, 'age'));


// missing column
$collection = new Collection(array(
	array(
		'name' => 'Bob',
	),
	array(
		'name' => 'Ted',
	),
));

$collection->sort('age');
Assert::throws(function() use ($collection) {
	$collection->fetch();
}, 'Cz\InnuDb\CollectionException', "Sort: column 'age' is missing in first or second item.");

