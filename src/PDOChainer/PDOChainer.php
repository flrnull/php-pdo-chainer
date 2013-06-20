<?php

/**
 * PDO chain wrapper.
 * 
 * See usage examples in README file.
 * See lincense text in LICENSE file.
 * 
 * (c) Evgeniy Udodov <flr.null@gmail.com>
 */

namespace PDOChainer;

/**
 * Main PDO wrapper class.
 */
class PDOChainer
{
    private $host = '127.0.0.1';
    private $port = 3306;
    private $dbname = 'test';
    private $user = 'root';
    private $pass = '';
    private $errorMode = \PDO::ERRMODE_WARNING;
    
    private $pdo; // Db handler
    private $pdoStatement; // Statement object
    
    /**
     * Main constructor.
     * 
     * @param Array $params Db connection params
     */
    public function __construct(array $options = array()) {
        $host = isset($options['host']) ? $options['host'] : $this->host;
        $port = isset($options['port']) ? $options['port'] : $this->port;
        $dbname = isset($options['dbname']) ? $options['dbname'] : $this->dbname;
        $user = isset($options['user']) ? $options['user'] : $this->user;
        $pass = isset($options['pass']) ? $options['pass'] : $this->pass;
        $errorMode = isset($options['errorMode']) ? $options['errorMode'] : $this->errorMode;
        try {
            $db = new \PDO('mysql:host='.$host.';port='.$port.';dbname='.$dbname, $user, $pass);
            $db->setAttribute(\PDO::ATTR_ERRMODE, $errorMode);
        } catch (\PDOException $e) {
            trigger_error('DataBase error: ' . $e->getMessage(), E_USER_ERROR);
        }
        $this->pdo = $db;
    }
    
    /**
     * PDO prepare.
     * 
     * @param String $query
     * 
     * @return \PDOChainer\PDOChainer
     */
    public function prepare($query) {
        $this->pdoStatement = $this->pdo->prepare($query);
        return $this;
    }
    
    /**
     * PDO bindValue.
     * 
     * @param String $name
     * @param String  $value
     * @param int $type
     * 
     * @return \PDOChainer\PDOChainer 
     */
    public function bindValue($name, $value, $type = \PDO::PARAM_STR) {
        $this->pdoStatement->bindValue($name, $value, $type);
        return $this;
    }
    
    /**
     * PDO bindValues for array of values.
     * 
     * @param Array $binds
     * Array (
     *   array(':id', 2, \PDO::PARAM_INT),
     *   array(':name', 'James', \PDO::PARAM_STR),
     *   ...
     * )
     * 
     * @return \PDOChainer\PDOChainer
     */
    public function bindValues(array $binds) {
        foreach($binds as $valuesArray) {
            $this->bindValue($valuesArray[0], $valuesArray[1], (isset($valuesArray[2]) ? $valuesArray[2] : \PDO::PARAM_STR));
        }
        return $this;
    }
    
    /**
     * PDO execute.
     * 
     * @return \PDOChainer\PDOChainer
     */
    public function execute() {
        try {
            $this->pdoStatement->execute();
        } catch (\PDOException $e) {
            trigger_error('DataBase error: ' . $e->getMessage(), E_USER_ERROR);
        }
        return $this;
    }
    
    /**
     * PDO fetch.
     * 
     * @param int $type
     * 
     * @return Array|false
     */
    public function fetch($type = \PDO::FETCH_BOTH) {
        return ($this->pdoStatement) ? $this->pdoStatement->fetch($type) : false;
    }
    
    /**
     * PDO fetchAll.
     * 
     * @param int $type 
     * 
     * @return Array|false
     */
    public function fetchAll($type = \PDO::FETCH_BOTH) {
        return ($this->pdoStatement) ? $this->pdoStatement->fetchAll($type) : false;
    }
    
    /**
     * PDO query.
     * 
     * @param String $query
     * 
     * @return \PDOChainer\PDOChainer 
     */
    public function query($query) {
        try {
            $this->pdoStatement = $this->pdo->query($query);
        } catch (\PDOException $e) {
            trigger_error('DataBase error: ' . $e->getMessage(), E_USER_ERROR);
        }
        return $this; 
    }
    
    /**
     * PDO lastInsertId.
     * 
     * @return String Last inserted ID
     */
    public function lastInsertId() {
        return $this->pdo->lastInsertId();
    }
    
    /**
     * PDO rowCount.
     * 
     * @return int|false
     */
    public function rowCount() {
        return ($this->pdoStatement) ? $this->pdoStatement->rowCount() : false;
    }
}
