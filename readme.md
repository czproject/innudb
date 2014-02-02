InnuDb
======

Experimental PHP library for manipulation with array-based "database".

``` php
$pathToFile = __DIR__ . '/data.php'; // see file content below
$loader = new Cz\InnuDb\Loader;
$innudb = new Cz\InnuDb\InnuDb($loader, $pathToFile);

$persons = $innudb->createCollection('persons');

foreach($persons as $id => $item)
{
	var_dump($item);
}

// $persons->getCount();
//
// $persons->limit(limit);
// $persons->limit(limit, offset);
//
// $persons->sort('column'); // ASC is default
// $persons->sort('column', 'ASC'); // or order('column')
// $persons->sort('column', 'DESC'); // ASC is default
// $persons->sort(array(
//     'column' => 'ASC', // or 'DESC'
// ));
// $persons->sort('name')->sort('age', 'DESC'); // fluent interface
//
// $persons->where('column', 'value'); // column = value
// $persons->where('column >', 'value'); // column > value
// $persons->where('column <=', 'value'); // column <= value
// $persons->where('column', 'value')->where('column2 >', 'value2'); // fluent interface: column = value AND column2 > value2
// $persons->where(array(
//     'column' => 'value', // column = value
//     'column2 <=' => 'value2', // AND column2 <= value2
// ));
```

Supported data files: PHP file, [NEON](http://ne-on.org), JSON, INI

PHP file
--------

``` php
<?php
return array(
    'persons' => array( // 'persons' subset
        'harry-potter' => array(
            'name' => 'Harry Potter',
            'story' => 'Harry Potter',
            'age' => 20,
        ),

        'gandalf' => array(
            'name' => 'Gandalf The White',
            'story' => 'Lord of the Rings',
            'age' => 2000,
        ),
    ),
);

```

NEON file
---------

```
persons:
    harry-potter:
        name: Harry Potter
        story: Harry Potter
        age: 20

    gandalf:
        name: Gandalf The White
        story: Lord of the Rings
        age: 2000
```

-----------------------

License: [New BSD License](license.md)
<br>Author: Jan Pecha, http://janpecha.iunas.cz/

