PHP dbal wrapper
===========================================

Low level logic DataBase manipulation class.

Usage example
--------

```php

use \PDOChainer\PDOChainer;

$params = array('host'=>'127.0.0.1', 'dbname'=>'test', 'user'=>'root', 'pass'=>'');
$db = new PDOChainer($params);

$result = $db->query("SELECT * FROM `table`")
             ->fetchAll(PDO::FETCH_NUM);

$row = $db->prepare("SELECT * FROM `table` WHERE `id` = :id")
          ->bindValue(':id', 1, PDO::PARAM_INT)
          ->execute()
          ->fetch(PDO::FETCH_ASSOC);

```