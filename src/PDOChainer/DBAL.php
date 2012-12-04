<?php

/**
 * DBAL over PDOChainer.
 * 
 * See usage examples in README file.
 * See lincense text in LICENSE file.
 * 
 * (c) Evgeniy Udodov <flr.null@gmail.com>
 */

namespace PDOChainer;

/**
 * DBAL over PDOChainer realization.
 */
class DBAL
{
    
    /**
     * PDOChainer link.
     * 
     * @var \PDOChainer\PDOChainer
     */
    private $pdo;
    
    /**
     * Default constructor.
     * 
     * @param \PDOChainer\PDOChainer $pdo 
     */
    public function __construct(\PDOChainer\PDOChainer $pdo) {
        $this->pdo = $pdo;
    }
    
    /**
     * Inserts data into DataBase.
     * 
     * @param String $table
     * @param Array $data
     * Array (
     *   array('id', 2, \PDO::PARAM_INT),
     *   array('name', 'James', \PDO::PARAM_STR),
     * )
     * 
     * @return int|false Inserted ID or false
     */
    function insert($table, array $dataArr){
        $fields = $params = $values = array();
        foreach ($dataArr as $data) {
            $fields[] = "`{$data[0]}`";
            $params[] = ":{$data[0]}";
            $values[] = array(":{$data[0]}", $data[1], (isset($data[2]) ? $data[2] : \PDO::PARAM_STR));
        }

        $fields = implode(',', $fields);
        $params = implode(',', $params);

        $sql = "INSERT INTO `{$table}` ({$fields}) VALUES ({$params})";
        $this->pdo->prepare($sql)->bindValues($values)->execute();
        return $this->pdo->lastInsertId();
    }
    
    /**
     * Updates data in DataBase.
     * 
     * @param String $table
     * @param Array $dataArr
     * Array (
     *   array('id', 2, \PDO::PARAM_INT),
     *   array('name', 'James', \PDO::PARAM_STR),
     *   ...
     * )
     * @param Array $whereArr
     * Array (
     *   array('id', 2, \PDO::PARAM_INT),
     * )
     * @param int $limit
     * 
     * @return int Affected rows count
     */
    function update($table, array $dataArr, array $whereArr = array(), $limit = 1){
        $fields = $params = $values = $where = array();
        foreach($dataArr as $data){
            $fields[] = "`{$data[0]}` = :{$data[0]}";
            $values[] = array(":{$data[0]}", $data[1], (isset($data[2]) ? $data[2] : \PDO::PARAM_STR));
        }
        $i = 0;
        foreach($whereArr as $wData){
            $i++; // The $i is in there because row wouldnt update with :value already being set above
            $where[] = "`{$wData[0]}` = :{$wData[0]}{$i}";
            $values[] = array(":{$wData[0]}{$i}", $wData[1], (isset($wData[2]) ? $wData[2] : \PDO::PARAM_STR));
        }

        $fields = implode(',', $fields);
        $whereStr = count($where) ? 'WHERE '.implode(' AND ', $where) : '';

        $sql = "UPDATE `{$table}` SET {$fields} {$whereStr} LIMIT {$limit}";
        $this->pdo->prepare($sql)->bindValues($values)->execute();
        return $this->pdo->rowCount();
    }
    
    /**
     * Removes data from DataBase.
     * 
     * @param String $table
     * @param Array $dataArr
     * Array (
     *   array('id', 2, \PDO::PARAM_INT),
     *   array('name', 'James', \PDO::PARAM_STR),
     *   ...
     * )
     * @param int $limit
     * 
     * @return int Affected rows count
     */
    function delete($table, array $dataArr, $limit = 1){
        foreach($dataArr as $data){
            $fields[] = "`{$data[0]}` = :{$data[0]}";
            $values[] = array(":{$data[0]}", $data[1], (isset($data[2]) ? $data[2] : \PDO::PARAM_STR));
        }

        $fields = implode(' AND ', $fields);

        $sql = "DELETE FROM `{$table}` WHERE {$fields} LIMIT {$limit}";
        $this->pdo->prepare($sql)->bindValues($values)->execute();
        return $this->pdo->rowCount();
    }
    
    /**
     * Inserts multiple data into DataBase.
     * 
     * @param String $table
     * @param Array $dataArr
     * Array (
     *   array (
     *     array('id', 2, \PDO::PARAM_INT),
     *     array('name', 'James', \PDO::PARAM_STR),
     *   ),
     *   ...
     * )
     * 
     * @return int|false Last inserted ID or false
     */
    function insertMulti($table, array $dataArr){
        $i = 0;
        $fields = array();
        foreach($dataArr as $data){
            $placeholders = array();
            foreach($data as $rowData){
                $i++;
                if(!in_array("`{$rowData[0]}`", $fields)) {
                    $fields[] = "`{$rowData[0]}`";
                }
                $placeholders[] = ":{$rowData[0]}{$i}";
                $values[] = array(":{$rowData[0]}{$i}", $rowData[1], (isset($rowData[2]) ? $rowData[2] : \PDO::PARAM_STR));
            }
            $params[] = '(' . implode(',', $placeholders) . ')';
        }

        $fields = implode(',', $fields);
        $params = implode(',', $params);

        $sql = "INSERT INTO `{$table}` ({$fields}) VALUES {$params}";
        $this->pdo->prepare($sql)->bindValues($values)->execute();
        return $this->pdo->lastInsertId();
    }
}