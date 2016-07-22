<?php
namespace app;

use app\Exceptions\QueryException;
use MongoDB\Driver\Query;
use PDO;

class DBManager {
    private $db;

    /**
     * DBManager constructor
     */
    public function __construct() {
        if($this->db == null) {
            try {
                $this->db = new PDO(getenv('DB_TYPE').':dbname='.getenv('DB_DATABASE').';host='.getenv('DB_HOST'),
                    getenv('DB_USERNAME'), getenv('DB_PASSWORD'));
            } catch(\PDOException $pdoe) {
                $this->logConnectError(__CLASS__,__FUNCTION__,$pdoe->getMessage(),$pdoe->getTrace());
                throw new \PDOException('Failed to connect to database.',0,$pdoe);
            }
        }
    }

    /**
     * query
     * Public facing method for executing queries. It will return the result set back.
     *
     * @param   string          $sql                The query to be prepared and executed
     * @param   array           $bindings           An array of query parameters
     * @param   array|null      $attributeKeys      Key of Key-Value Pair to PDO Attributes
     * @param   array|null      $attributeValues    Value of Key-Value Pair to PDO Attributes
     * @return  \PDOStatement                       Results from query
     * @throws  QueryException
     */
    public function query($sql,$bindings,$attributeKeys = null, $attributeValues = null) {
        try {
            $result = $this->executePreparedStatement($sql,$bindings,$attributeKeys,$attributeValues);
            return $result;
        } catch(QueryException $qe) {
            $this->logQueryError(__CLASS__,__FUNCTION__,$qe);
            throw new QueryException('Failed to query users from database.',0,
                $this->db,$this->getErrorCode(),$this->getErrorInfo(),$qe);
        }
    }

    /**
     * executePreparedStatement
     * Prepares the query($sql), binds the parameters, executes the query, then returns the result set.
     *
     * @param   string      $sql        The query to be prepared and executed
     * @param   array       $bindings   An array of query parameters
     * @return  \PDOStatement           Results from the prepared statement
     * @throws  QueryException          Checks if prepared statement was successful created and executed
     */
    private function executePreparedStatement($sql,$bindings,$attributeKeys,$attributeValues) {
        if(is_null($attributeKeys) || is_null($attributeValues)) {
            $stmt = $this->db->prepare($sql);
        } else {
            $stmt = $this->db->prepare($sql); //implement dynamically assoc - not sure how yet
        }

        if($stmt === false) {
            $message = "Failed to generate prepared statement.\nError Code: " .
                $this->db->errorCode() . "\nError Info: " . array_values($this->db->errorInfo());
            throw new QueryException($message,$sql,$this->db);
        }

        $result = $stmt->execute($bindings);

        if($result === false) {
            $message = "Failed to execute prepared statement.\nError Code: " .
                $this->db->errorCode() . "\nError Info: " . array_values($this->db->errorInfo());
            throw new QueryException($message,$sql,$this->db);
        }

        return $stmt;
    }

    /**
     * getErrorCode
     * Returns PDOException if db not instantiated, otherwise returns the error code.
     *
     * @return mixed
     */
    private function getErrorCode() {
        if(is_null($this->db)) {
            return null;
        }
        return $this->db->errorCode();
    }

    /**
     * getErrorInfo
     * Returns PDOException if db not instantiated, otherwise returns the error info.
     *
     * @return array
     */
    private function getErrorInfo() {
        if(is_null($this->db)) {
            return null;
        }
        return $this->db->errorInfo();
    }

    /**
     * logConnectError
     * Emails devs that a connection error has occured and then generates .log file
     *
     * @param   string          $class          Class Name
     * @param   string          $method         Function Name
     * @param   string          $message        Error Message
     * @param   array           $trace          Exception Trace
     */
    private static function logConnectError($class, $method, $message, $trace) {
        $path = self::createFile();
        $remote_addr = $_SERVER['REMOTE_ADDR'];
        $server_addr = getenv('SERVER_ADDR');

        $headers = array('server'=>$server_addr,'method'=>$method,'path'=>$path);
        $mail = false;/*Mail::send(['html' => 'emails.errors.pdoconnectexception'],$headers, function($m) {
            $m->from('');
            $m->to('')->subject('CRITICAL: PDOConnectException');
        });*/

        date_default_timezone_set('America/New_York');
        $message = "Error in $class $method \n Error logged at: " . date('m/d/Y h:i:s a') .
            "\n Email sent to Devs: $mail \n Error logged on IP: $remote_addr \n Error Message: $message" .
            "\n Error Trace: \n" . json_encode($trace) . "\n ------------------------------------- \n";
        error_log($message,3,$path);
    }

    /**
     * logQueryError
     * Emails devs that a connection error has occured and then generates .log file
     *
     * @param   string          $class          Class Name
     * @param   string          $method         Function Name
     * @param   QueryException  $exception      Exception object to retrieve trace, message, and SQL
     * @param   DBManager       $db             Database object to retrieve errors
     * @param   array           $params         urlId and projectId
     */
    private static function logQueryError($class, $method, $exception) {
        $path = self::createFile();
        $remote_addr = $_SERVER['REMOTE_ADDR'];

        $trace = $exception->getTrace();
        $errorcode = $exception->getDB()->getErrorCode();
        $errorinfo = $exception->getDB()->getErrorInfo();
        $ip = $_SERVER['REMOTE_ADDR'];
        $message = $exception->getMessage();
        $sql = $exception->getQuery();

        $headers = array('trace'=>$trace,'errorcode'=>$errorcode,'erorrinfo'=>$errorinfo,'ip'=>$ip,'message'=>$message,'sql'=>$sql);
        $mail = false;/*Mail::send(['html' => 'emails.errors.pdoqueryexception'],$headers, function($m) {
            $m->from('');
            $m->to('')->subject('CRITICAL: PDOQueryException');
        });*/

        date_default_timezone_set('America/New_York');
        $message = "Error in $class $method \n Error logged at: " . date('m/d/Y h:i:s a') .
            "\n Email sent to Devs: $mail \n Error logged on IP: $remote_addr \n Error Message: $message" .
            "\n Error Trace: \n" . json_encode($trace) . "\n ------------------------------------- \n";
        error_log($message,3,$path);
    }

    /**
     * createFile
     * Executes common processes for logQueryError and logConnectionError.
     *
     * @return  string
     */
    private static function createFile() {
        $path = '../storage/logs/' . date('m-d-Y') . '.log';
        if(!file_exists($path)) {
            $file = fopen($path,'w');
            fclose($file);
        }

        return $path;
    }
}