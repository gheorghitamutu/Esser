<?php
class Database 
{
  protected $format_connection = HOST_IP . ':' . HOST_PORT . '//' . SYS_DB;
  protected $username = ROOT_ADMIN_USER;
  protected $password = ROOT_ADMIN_PASS;
  protected $connection = null;
  private static $_instance;
  private $conn_handle;
  private $conn_data;
  private $errors_pool;
  private $statements = array();
  private $autocommit = false;
  private $fetch_mode = OCI_BOTH;
  private $last_query;
  private $var_max_size = 1000;
  private $execute_status = false;
  private $charset;
  private $session_mode = OCI_DEFAULT;

  /**
   * Function to set array fetching mode for Fetch methods
   *
   * @param mixed $mode
   */
  public function setFetchMode($mode = OCI_BOTH) {
    $this->fetch_mode = $mode;
  }

  /**
   * Function to set on or off the autocommit option
   *
   * @param bool $mode
   */
  public function setAutoCommit($mode = true) {
    $this->autocommit = $mode;
  }

  /**
   * Function to set the variable max size for binding
   *
   * @param int $size
   */
  public function setVarMaxSize($size) {
    $this->var_max_size = $size;
  }

  /**
   * Returns the last error found.
   *
   */
  public function getError() {
    return @oci_error($this->conn_handle);
  }

  /**
   * Function to set the nls_lang
   *
   * @param string $charset
   */
  public function setNlsLang($charset = ORA_CHARSET_DEFAULT) {
    $this->charset = $charset;
  }

  /**
   * Constructor
   *
   */
  public function __construct() {
    $this->setNlsLang('WE8MSWIN1252');
    $this->setFetchMode(OCI_ASSOC);
    $this->setAutoCommit(false);
  }

  /**
   * Destructor
   *
   */
  public function __destruct() {
    if (is_resource($this->conn_handle)) {
      @oci_close($this->conn_handle);
    }
  }

  /**
   * Connect to the DB
   *
   * @param string $user
   * @param string $pass
   * @param int $mode (OCI_DEFAULT, OCI_SYSDBA, OCI_SYSOPER) In general will be OCI_DEFAULT! SYSDBA and SYSOPER require special php.ini permisions!
   * @param int $type (ORA_CONNECTION_TYPE_DEFAULT, ORA_CONNECTION_TYPE_NEW, ORA_CONNECTION_TYPE_PERSISTENT)
   * @return bool
   */
  public function connect($user = '', $pass = '', $mode = OCI_DEFAULT, $type = ORA_CONNECTION_TYPE_DEFAULT) {
    switch ($type) {
      case ORA_CONNECTION_TYPE_PERSISTENT: {
          $this->conn_handle = oci_pconnect($user, $pass, $this->format_connection, $this->charset, $mode);
        };
        break;
      case ORA_CONNECTION_TYPE_NEW: {
          $this->conn_handle = oci_new_connect($user, $pass, $this->format_connection, $this->charset, $mode);
        };
        break;
      default:
        $this->conn_handle = oci_connect($user, $pass, $this->format_connection, $this->charset, $mode);
    }
    return is_resource($this->conn_handle) ? true : false;
  }

  /**
   * This function returns last command exec status
   *
   * @return bool
   */
  public function getExecuteStatus() {
    return $this->execute_status;
  }

  private function getBindingType($var) {
    if (is_a($var, "OCI-Collection")) {
      $bind_type = SQLT_NTY;
      $this->setVarMaxSize(-1);
    }
    elseif (is_a($var, "OCI-Lob")) {
      $bind_type = SQLT_CLOB;
      $this->setVarMaxSize(-1);
    }
    else {
      $bind_type = SQLT_CHR;
    }
    return $bind_type;
  }

  /**
   * Private method for execute any sql query or pl/sql
   *
   * @param string $sql_text
   * @param array | false $bind
   * @return resource | false
   */
  private function execute($sql_text, &$bind = false) {
    if (!is_resource($this->conn_handle))
      return false;
    $this->last_query = $sql_text;

    $stid = @oci_parse($this->conn_handle, $sql_text);

    $this->statements[$stid]['text'] = $sql_text;
    $this->statements[$stid]['bind'] = $bind;

    if ($bind && is_array($bind)) {
      foreach ($bind as $k => $v) {
        oci_bind_by_name($stid, $k, $bind[$k], $this->var_max_size, $this->getBindingType($bind[$k]));
      }
    }
    $com_mode = $this->autocommit ? OCI_COMMIT_ON_SUCCESS : OCI_DEFAULT;
    $this->execute_status = oci_execute($stid, $com_mode);
    return $this->execute_status ? $stid : false;
  }

  /**
   * select command wrapper
   *
   * @param string $sql the query text
   * @param array | false $bind array of pairs binding variables
   * @return resource | false
   */
  public function select($sql, $bind = false) {
    return $this->execute($sql, $bind);
  }

  /**
   * Fetch array of select statement
   *
   * @param resource $statement valid OCI statement id
   * @return array
   */
  public function fetchArray($statement) {
    return oci_fetch_array($statement, $this->fetch_mode);
  }

  /**
   * Function that returns a numerically indexed array containing the next result-set row of a query.
   * Each array entry corresponds to a column of the row. This function is typically called in a loop
   * until it returns FALSE, indicating no more rows exist.
   *
   * @param resource $statement valid OCI statement id
   * @return array Returns a numerically indexed array. If there are no more rows in the statement then FALSE is returned.
   */
  public function fetchRow($statement) {
    return oci_fetch_row($statement);
  }

  /**
   * Fetch rows from select operation
   *
   * @param resource $statement valid OCI statement identifier
   * @param int $skip number of initial rows to ignore when fetching the result (default value of 0, to start at the first line).
   * @param int $maxrows number of rows to read, starting at the skip th row (default to -1, meaning all the rows).
   * @return array
   */
  public function fetchAll($statement, $skip = 0, $maxrows = -1) {
    $rows = array();
    oci_fetch_all($statement, $rows, $skip, $maxrows, OCI_FETCHSTATEMENT_BY_ROW);
    return $rows;
  }

  /**
   * Fetch row as object
   *
   * @param resource $statement valid OCI statement identifier
   * @return object
   */
  public function fetchObject($statement) {
    return oci_fetch_object($statement);
  }

  /**
   * Fetches the next row (for SELECT statements) into the internal result-buffer.
   *
   * @param resource $statement valid OCI statement id
   * @return bool
   */
  public function fetch($statement) {
    return oci_fetch($statement);
  }

  /**
   * Returns the data from field in the current row, fetched by Fetch()
   *
   * @param resource $statement valid OCI statement id
   * @param mixed $field Can be either use the column number (1-based) or the column name (in uppercase).
   * @return mixed
   */

  public function getResult($statement, $field) {
    return oci_result($statement, $field);
  }

  /**
   * Associates a PHP variable with a column for query fetches using Fetch().
   *
   * @param resource $statement A valid OCI statement identifier
   * @param string $column_name The column name used in the query.
   * @param mixed $variable The PHP variable that will contain the returned column value.
   * @param int $type The data type to be returned.
   * @return bool
   */
  public function defineByName($statement, $column_name, &$variable, $type = SQLT_CHR) {
    return oci_define_by_name($statement, $column_name, $variable, $type);
  }

  public function fieldIsNull($statement, $field) {
    return oci_field_is_null($statement, $field);
  }

  public function fieldName($statement, int $field) {
    return oci_field_name($statement, $field);
  }

  public function fieldPrecition($statement, int $field) {
    return oci_field_precision($statement, $field);
  }

  public function fieldScale($statement, int $field) {
    return oci_field_scale($statement, $field);
  }

  public function fieldSize($statement, $field) {
    return oci_field_size($statement, $field);
  }

  public function fieldTypeRaw($statement, int $field) {
    return oci_field_type_raw($statement, $field);
  }

  public function fieldType($statement, int $field) {
    return oci_field_type($statement, $field);
  }

    /**
     * Insert row into table
     *
     * @param string $table name of table
     * @param array $arrayFieldsValues define pair field => value
     * @param bool $bind define pairs holder => value for binding
     * @param bool $returning define fields for returning clause in insert statement
     * @return mixed if $returnig is defined function return array of fields defined in $returning
     */
  public function insert($table, $arrayFieldsValues, &$bind = false, $returning = false) {
    if (empty($arrayFieldsValues)) {
      return false;
    }
    $fields = array();
    $values = array();
    foreach ($arrayFieldsValues as $f => $v) {
      $fields[] = $f;
      $values[] = $v;
    }
    $fields = implode(",", $fields);
    $values = implode(",", $values);
    $ret = "";
    if ($returning) {
      foreach ($returning as $f => $h) {
        $ret_fields[] = $f;
        $ret_binds[] = ":$h";
        $bind[":$h"] = "";
      }
      $ret = " returning " . (implode(",", $ret_fields)) . " into " . (implode(",", $ret_binds));
    }
    $sql = "insert into $table ($fields) values($values) $ret";
    $result = $this->execute($sql, $bind);
    if ($result === false) {
      return false;
    }
    if ($returning === false) {
      return $result;
    }
    else {
      $result = array();
      foreach ($returning as $f => $h) {
        $result[$f] = $bind[":$h"];
      }
      return $result;
    }
  }

  /**
   * Method for update data in table
   *
   * @param string $table
   * @param array $arrayFieldsValues
   * @param string | false $condition
   * @param array | false $bind
   * @return resource
   */
  public function update($table, $arrayFieldsValues, $condition = false, &$bind = false, $returning = false) {
    if (empty($arrayFieldsValues)) {      
      return false;
    }
    $fields = array();
    $values = array();
    foreach ($arrayFieldsValues as $f => $v) {
      $fields[] = "$f = $v";
    }
    $fields = implode(",", $fields);
    if ($condition === false) {
      $condition = "true";
    }
    $ret = "";
    if ($returning) {
      foreach ($returning as $f => $h) {
        $ret_fields[] = $f;
        $ret_binds[] = ":$h";
        $bind[":$h"] = "";
      }
      $ret = " returning " . (implode(",", $ret_fields)) . " into " . (implode(",", $ret_binds));
    }
    $sql = "update $table set $fields where $condition $ret";
    $result = $this->execute($sql, $bind);
    if ($result === false) {
      return false;
    }
    if ($returning === false) {
      return $result;
    }
    else {
      $result = array();
      foreach ($returning as $f => $h) {
        $result[$f] = $bind[":$h"];
      }
      return $result;
    }
  }

  public function delete($table, $condition, &$bind = false, $returning = false) {
    if ($condition === false) {
      $condition = "true";
    }
    $ret = "";
    if ($returning) {
      foreach ($returning as $f => $h) {
        $ret_fields[] = $f;
        $ret_binds[] = ":$h";
        $bind[":$h"] = "";
      }
      $ret = " returning " . (implode(",", $ret_fields)) . " into " . (implode(",", $ret_binds));
    }
    $sql = "delete from $table where $condition $ret";
    $result = $this->execute($sql, $bind);
    if ($result === false) {
      return false;
    }
    if ($returning === false) {
      return $result;
    }
    else {
      $result = array();
      foreach ($returning as $f => $h) {
        $result[$f] = $bind[":$h"];
      }
      return $result;
    }
  }

  /**
   * Gets the number of rows affected during statement execution.
   *
   * @param resource $statement
   * @return int
   */
  private function getNumRows($statement) {
    return oci_num_rows($statement);
  }

  /**
   * Synonym for getNumRows()
   *
   * @param resource $statement
   * @return int
   */
  public function countRowsAffected($statement) {
    return $this->getNumRows($statement);
  }

  /**
   * Gets the number of columns in the given statement.
   *
   * @param resource $statement
   * @return int
   */
  private function getNumFields($statement) {
    return oci_num_fields($statement);
  }

  /**
   * Synonym for getNumFields()
   *
   * @param resource $statement
   * @return int
   */
  public function countFieldsAffected($statement) {
    return $this->getNumFields($statement);
  }

  // Support for Lob
  /**
   * Allocates resources to hold descriptor or LOB locator.
   *
   * @param resource $connection
   * @param int $type Valid values for type are: OCI_DTYPE_FILE, OCI_DTYPE_LOB and OCI_DTYPE_ROWID.
   * @return OCI-Lob
   */
  public function getNewDescriptor($type = OCI_DTYPE_LOB) {
    return oci_new_descriptor($this->conn_handle, $type);
  }

  /**
   * Allocates a new collection object
   *
   * @param string $typename Should be a valid named type (uppercase).
   * @param string $schema Should point to the scheme, where the named type was created. The name of the current user is the default value.
   * @return OCI-Collection
   */
  public function getNewCollection($typename, $schema = null) {
    return oci_new_collection($this->conn_handle, $typename, $schema);
  }

  // Support for stored procedures and functions
  /**
   * Method to execute stored procedure
   *
   * @param mixed $name
   * @param string $params
   * @param mixed $bind
   * @return resource
   */
  public function getStoredProc($name, $params = false, &$bind = false) {
    if ($params) {
      if (is_array($params))
        $params = implode(",", $params);
      $sql = "BEGIN $name($params); END;";
    } else {
      $sql = "BEGIN $name; END;";
    }
    return $this->execute($sql, $bind);
  }

  /**
   * Methos to execute stored function
   *
   * @param mixed $name
   * @param string $params
   * @param mixed $bind
   * @return mixed
   */
  public function getFunc($name, $params = false, $bind = false) {
    if ($params) {
      if (is_array($params))
        $params = implode(",", $params);
      $sql = "select $name($params) as RESULT from dual";
    } else {
      $sql = "select $name from dual";
    }
    $h = $this->execute($sql, $bind);
    $r = $this->fetchArray($h);
    return $r['RESULT'];
  }

  /**
   * Method to execute cursor defined in stored proc
   *
   * @param string $stored_proc stored proc where cursor is defined
   * @param string $bind binding for out parameter in stored proc
   * @return resource
   * @example Cursor("utils.get_cursor", "dataset"); //begin utils.get_cursor(:dataset); end;
   */
  public function getCursor($stored_proc, $bind) {
    if (!is_resource($this->conn_handle))
      return false;
    $sql = "begin $stored_proc(:$bind); end;";
    $curs = oci_new_cursor($this->conn_handle);
    $stmt = oci_parse($this->conn_handle, $sql);
    oci_bind_by_name($stmt, $bind, $curs, -1, OCI_B_CURSOR);
    oci_execute($stmt);
    oci_execute($curs);
    $this->freeStatement($stmt);
    return $curs;
  }

  /**
   * Invalidates a cursor, freeing all associated resources and cancels the ability to read from it.
   *
   * @param resource $statement valid OCI statement id
   * @return bool
   */
  public function closeCursor($statement) {
    return oci_cancel($statement);
  }

  /**
   * Free resources of OCI statement identifier
   *
   * @param resource $stid
   * @return bool
   */
  public function freeStatement($stid) {
    unset($this->statements[$stid]);
    return oci_free_statement($stid);
  }

  /**
   * Free array of resources of OCI statement identifier
   *
   * @param array $array_stid
   * @return bool
   */
  public function freeStatements($array_stid) {
    if (is_array($array_stid))
      foreach ($array_stid as $stid) {
        unset($this->statements[$stid]);
        oci_free_statement($stid);
      }
    return true;
  }

  /**
   * Commit transaction
   *
   * @return bool
   */
  public function commit() {
    if (is_resource($this->conn_handle))
      return @oci_commit($this->conn_handle);
    else
      return false;
  }

  /**
   * Rollback transaction
   *
   * @return bool
   */
  public function rollback() {
    if (is_resource($this->conn_handle))
      return @oci_rollback($this->conn_handle);
    else
      return false;
  }

  /**
   * Enables or disables internal debug output.
   *
   * @param bool $mode
   */
  public function setInternalDebug($mode) {
    oci_internal_debug($mode);
  }

  public function getStatement($stid) {
    return $this->statements[$stid] ? $this->statements[$stid] : false;
  }

  /**
   * Get sql text operation
   *
   * @param resource $stid valid OCI statement id
   * @return string
   */
  public function getQuerySnapshot($stid = false) {
    if ($stid)
      return $this->statements[$stid]['text'];
    else
      return $this->last_query;
  }

  /**
   * Get Oracle Server version
   *
   * @return string | false
   */
  public function getServerVer() {
    if (is_resource($this->conn_handle)) {
      return @oci_server_version($this->conn_handle);
    }
    else
    {
      return false;
    }
  }

  public function setAction(string $action_name) {
    return @oci_set_action($this->conn_handle, $action_name);
  }

  public function setClientID(string $client_id) {
    return @oci_set_client_identifier($this->conn_handle, $client_id);
  }

  public function setClientInfo(string $client_info) {
    return @oci_set_client_info($this->conn_handle, $client_info);
  }

  public function setPrefetch(int $rows) {
    return oci_set_prefetch($this->conn_handle, $rows);
  }

  /**
   * Returns a keyword identifying the type of the OCI statement.
   *
   * @param resource $statement
   * @return string (ALTER, BEGIN, CALL, CREATE, DECLARE, DELETE, DROP, INSERT, SELECT, UPDATE, UNKNOWN) return false on error
   */
  public function getStatementType($statement) {
    return oci_statement_type($statement);
  }

  public function dumpQueriesStack() {
    var_dump($this->statements);
  }

  public function closeDB() {
    $this->__destruct();
  }

  public function get_handle() {
    return $this->conn_handle;
  }

}

?>
