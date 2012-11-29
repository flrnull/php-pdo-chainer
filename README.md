PHP PDO wrappers
===========================================

1. PDOChainer — low level DataBase manipulation class.
2. DBAL — DB layer over PDOChainer. 

PDOChainer usage example
--------

```php

use \PDOChainer\PDOChainer;

$params = array('host'=>'127.0.0.1', 'dbname'=>'test', 'user'=>'root', 'pass'=>'');
$db = new PDOChainer($params);

// Fetch all rows
$result = $db->query("SELECT * FROM `table`")
             ->fetchAll(PDO::FETCH_NUM);

// Fetch first row
$row = $db->prepare("SELECT * FROM `table` WHERE `id` = :id")
          ->bindValue(':id', 1, PDO::PARAM_INT)
          ->execute()
          ->fetch(PDO::FETCH_ASSOC);

```

DBAL usage example
--------

```php

use \PDOChainer\PDOChainer;
use \PDOChainer\DBAL;

$params = array('host'=>'127.0.0.1', 'dbname'=>'test', 'user'=>'root', 'pass'=>'');
$dbal = new DBAL(new PDOChainer($params));
$table = 'users';

// Insert
$data = array(
    array('id', 2),
    array('name', 'James'),
);
$dbal->insert($table, $data);

// Update
$data = array(
    array('name', 'James'),
);
$where = array(
    array('id', 2),
);
$dbal->update($table, $data, $where);

```