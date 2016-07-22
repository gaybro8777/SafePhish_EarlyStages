<?php
/**
 * Created by PhpStorm.
 * User: tthrockmorton
 * Date: 7/11/2016
 * Time: 2:19 PM
 */

namespace app;


class PDOIterator implements \Iterator
{
    private $pdoStatement;
    private $key;
    private $result;
    private $valid;

    public function __construct(\PDOStatement $pdoStatement) {
        $this->pdoStatement = $pdoStatement;
    }

    public function next() {
        $this->key++;
        $this->result = $this->pdoStatement->fetch(
            \PDO::FETCH_OBJ,
            \PDO::FETCH_ORI_ABS,
            $this->key
        );
        if ($this->result === false) {
            $this->valid = false;
            return null;
        }
    }

    public function valid() {
        return $this->valid;
    }

    public function current() {
        return $this->result;
    }

    public function rewind() {
        $this->key = 0;
    }

    public function key() {
        return $this->key;
    }
}