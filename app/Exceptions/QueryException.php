<?php
/**
 * Created by PhpStorm.
 * User: tthrockmorton
 * Date: 7/7/2016
 * Time: 11:05 AM
 */

namespace app\Exceptions;

use PhpSpec\Exception\Exception;


class QueryException extends Exception
{
    private $sql;
    private $errorcode;
    private $errorinfo;
    private $db;

    public function __construct($message, $sql, \PDO $db, $code = 0, $errorinfo = '', Exception $previous = null) {
        $this->sql = $sql;
        $this->errorcode = $code;
        $this->errorinfo = $errorinfo;
        $this->db = $db;
        parent::__construct($message,$code,$previous);
    }

    public function __toString() {
        return __CLASS__ . ": [{$this->errorcode}]: {$this->errorinfo}; Query: {$this->sql}: {$this->message}";
    }

    public function getQuery() {
        return $this->sql;
    }

    public function getErrorcode()
    {
        return $this->errorcode;
    }

    public function getErrorinfo()
    {
        return $this->errorinfo;
    }

    public function getDB() {
        return $this->db;
    }

}