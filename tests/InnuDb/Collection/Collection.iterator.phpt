<?php
use Tester\Assert;
use Cz\InnuDb\Collection;

require __DIR__ . '/../bootstrap.php';
require __DIR__ . '/../../../vendor/autoload.php';
require __DIR__ . '/../../../src/Collection.php';

$data = include __DIR__ . '/data.php';

$collection = new Collection($data['persons']);
$ids = array();

foreach($collection as $id => $item)
{
	$ids[] = $id;
}

Assert::same(array('harry-potter', 'gandalf', 'saxana'), $ids);
Assert::false($collection->fetch());


$collection->rewind();
Assert::same($data['persons']['harry-potter'], $collection->fetch());
Assert::same($data['persons']['gandalf'], $collection->fetch());
Assert::same($data['persons']['saxana'], $collection->fetch());
Assert::false($collection->fetch());
