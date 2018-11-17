<?php

namespace Prism;

use MySQLi;

/**
 * Invloves all Database operations.
 *
 */
class DB
{
  private static $db;
  private $connection;

  /**
   * Adjusts the value of $connection based on which connection condition is met
   * from the DB prism config.php.
   *
   */
  private function __construct()
  {
    foreach($GLOBALS['DB'] as $connection){
      if($connection['condition']){
        $this->connection = new MySQLi($connection['servername'], $connection['username'], $connection['password'], $connection['db']);
        break;
      }
    }
  }

  /**
   * Creates a php errors table if it does not exist in the database.
   *
   */
  public static function createErrorLog()
  {
    $sql = "CREATE TABLE IF NOT EXISTS `prism_php_errors` (
      `record_id` int(11) NOT NULL AUTO_INCREMENT,
      `errno` int(11) NOT NULL,
      `errstr` varchar(100) NOT NULL,
      `errfile` varchar(100) NOT NULL,
      `errline` varchar(100) NOT NULL,
      `timestamp` varchar(30) NOT NULL,
      PRIMARY KEY (record_id)
    ) ENGINE=InnoDB DEFAULT CHARSET=latin1";
    mysqli_query(self::connect(), $sql);
  }

  /**
   * Runs any sql query. If successful, a success response is returned. If the
   * query was unsuccessful, the mysqli_error is returned.
   *
   * @param  string $sql Sql query
   * @return array       Request status and message.
   */
  public static function query($sql)
  {
    if(mysqli_query(self::connect(), $sql)){
      return ['status'=>'success'];
    } else {
      trigger_error(mysqli_error(self::connect()));
      return ['status'=>'error', 'message'=> mysqli_error(self::connect())];
    }
  }

  /**
   * Runs an insert sql query. If successful, the mysqli insert id is returned.
   * If the query was unsuccessful, the mysqli_error is returned.
   *
   * @param  string $sql Sql query
   * @return array       Request status and message.
   */
  public static function insert($sql)
  {
    if(mysqli_query(self::connect(), $sql)){
      return mysqli_insert_id(self::connect());
    } else {
      trigger_error(mysqli_error(self::connect()));
      return ['status'=>'error', 'message'=> mysqli_error(self::connect())];
    }
  }

  /**
   * Runs a select sql query, preferably a select query. If successful,
   * result rows are then returned in an array schema. If the query was
   * unsuccessful, the mysqli_error is returned.
   *
   * @param  string $sql Sql query
   * @return array       Request status and message.
   */
  public static function select($sql)
  {
    $result = mysqli_query(self::connect(), $sql);
    if($result){
      $output = [];
      while($row = mysqli_fetch_array($result)){
        $output[] = $row;
      }
      return $output;
    } else {
      trigger_error(mysqli_error(self::connect()));
      return ['status'=>'error', 'message'=> mysqli_error(self::connect())];
    }
  }

  /**
   * Runs a select sql query, preferably a select query. If successful,
   * a result row is then returned in an array schema. If the query was
   * unsuccessful, the mysqli_error is returned.
   *
   * @param  string $sql Sql query
   * @return array       Request status and message.
   */
  public static function selectOne($sql)
  {
    $result = mysqli_query(self::connect(), $sql);
    if($result){
      return mysqli_fetch_array($result);
    } else {
      trigger_error(mysqli_error(self::connect()));
      return ['status'=>'error', 'message'=> mysqli_error(self::connect())];
    }
  }

  /**
   * Runs any sql query, preferably a update query. If successful, a success
   * response is returned. If the query was unsuccessful, the mysqli_error is
   * returned.
   *
   * @param  string $sql Sql query
   * @return array       Request status and message.
   */
  public static function update($sql)
  {
    return self::query($sql);
  }

  /**
   * Runs any sql query, preferably a delete query. If successful, a success
   * response is returned. If the query was unsuccessful, the mysqli_error is
   * returned.
   *
   * @param  string $sql Sql query
   * @return array       Request status and message.
   */
  public static function delete($sql)
  {
    return self::query($sql);
  }

  /**
   * Insantiates the databse function.
   *
   */
  public static function connect()
  {
    if(self::$db == null){
      self::$db = new DB();
    }
    return self::$db->connection;
  }

  /**
   * Sanitizes first and second dimension values of the post array.
   *
   */
  public static function sanitize()
  {
    if($_POST){
      foreach($_POST as $key => $value){
        if(is_array($value)){
          foreach($value as $sub_key => $sub_value){
            $value[$sub_key] = mysqli_real_escape_string(self::connect(), $value[$sub_key]);
          }
        } else {
          $_POST[$key] = mysqli_real_escape_string(self::connect(), $_POST[$key]);
        }
      }
    }
  }

  /**
   * Retrieves timestamp in UTC format.
   *
   * @return string Current timestamp
   */
  public static function timestamp()
  {
    return date("Y-m-d H:i:s", strtotime('now'));
  }
}