<?php
/**
 * Created by PhpStorm.
 * Author: Le Minh Ho
 * Date: 7/1/2019
 * Time: 1:17 AM
 */

class DBConnection
{
    private $pdo = null;
    private $strConnection = ""; // String connection to database
    private $HOST = "DELL_INS";
    private $PORT = 1433;
    private $DB_NAME = "QL_SACH";
    private $USER = "sa";
    private $PASSWORD = "Leminhho98";

    function __construct()
    {
        $this->strConnection = $this->initConnection();
    }

    /**
     * @todo Create string connection
     */
    private function initConnection()
    {
        return "sqlsrv:server=tcp:$this->HOST,$this->PORT;Database=$this->DB_NAME";
    }

    /**
     * @todo Open connection to database
     */
    public function openConnection(){
        // Create a new PDO instanace
        try {
            $this->pdo = new PDO($this->strConnection, $this->USER, $this->PASSWORD);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }
        catch (PDOException $e) {
            echo $e->getMessage();
            exit();
        }
    }

    /**
     * @todo Close connection to database
     */
    public function closeConnection()
    {
        $this->pdo = null;
    }

    /**
     * @todo Execute store procedure
     * @param $name
     * @param $data
     * @return mixed
     */
    public function executeProcedure($name, $data)
    {
        try{
            $query = $this->createQueryProcedure($name, count($data), $isReturn = false);
            $statement = $this->pdo->prepare($query);
            $statement->setFetchMode(PDO::FETCH_ASSOC);
            $statement->execute($data);
        } catch(Exception $e){
            echo $e->getMessage();
            exit();
        }
        if ($isReturn == true) return $statement->fetch();

        return true;
    }

    /**
     * @todo Generate query to execute scalar function
     * @param $name
     * @param $countParamsIn
     * @return mixed|string
     */
    private function createQueryScalarFunction($name, $countParamsIn)
    {
        $result = '{? = Call ' . $name . ' (';
        for ($i = 0; $i < $countParamsIn; $i++) {
            $result .= '?,';
        }
        $result = substr_replace($result, ')}', -1, 1);
        return $result;
    }

    /**
     * @todo Generate query to execute table function
     * @param $name
     * @param $countParams
     * @return mixed|string
     */
    private function createQueryTableFunction($name, $countParams)
    {
        $result = 'Select * From '.$name.'(';
        for ($i = 0; $i < $countParams; $i++) {
            $result .= '?,';
        }
        if ($countParams > 0) $result = substr_replace($result, ')', -1, 1);
        else $result .= ')';

        return $result;
    }

    /**
     * @todo Generate query to call procedure
     * @param $name
     * @param $countParams
     * @return mixed|string
     */
    private function createQueryProcedure($name, $countParams)
    {
        $result = 'Exec ' . $name . ' ';
        for ($i = 0; $i < $countParams; $i++) {
            $result .= ' ?,';
        }
        $result = substr_replace($result, '', -1, 1);
        return $result;
    }
}