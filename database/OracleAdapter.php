<?php

namespace DatabaseConnectivity;
use InvalidArgumentException;
use RuntimeException;

class OracleAdapter implements DatabaseAdapterInterface {

    protected $_config = array();
    protected $_result;
    protected $format_connection = HOST_IP . ':' . HOST_PORT . '//' . SYS_DB;
    protected $username = ROOT_ADMIN_USER;
    protected $password = ROOT_ADMIN_PASS;
    protected $connection = null;
    private $statements = array();
    private $autocommit = true;
    private $fetch_mode = OCI_ASSOC+OCI_RETURN_NULLS;
    private $last_query;
    private $var_max_size = 1000;
    private $execute_status = false;
    private $charset;

    /**
     * Constructor function
     * @config parameter type array of 4 ELEMENTS! [user, pass, connection mode, and connection type!
     * @param array $config
     */
    public function __construct(array $config)
    {

        $this->setNlsLang('WE8MSWIN1252');
        $this->setFetchMode(OCI_ASSOC + OCI_RETURN_NULLS);
        $this->setAutoCommit(true);

        if (count($config) !== 4)
        {
            new \InternalServerErrorController();
            throw new InvalidArgumentException('Invalid number of connection parameters!');
        }

        $this->_config =
            array
            (
                $config[0],
                $config[1],
                $this->format_connection,
                $this->charset,
                $config[2],
                $config[3]
            );
    }

    /**
     * Destructor
     *
     */
    public function __destruct()
    {
        if (is_resource($this->connection))
        {
            @oci_close($this->connection);
        }
    }

    /**
     * Returns the last error found.
     *
     */
    public function getError()
    {
        return @oci_error($this->connection);
    }

    /**
     * Function to set array fetching mode for Fetch methods
     *
     * @param mixed $mode
     */
    public function setFetchMode($mode = OCI_BOTH)
    {
        $this->fetch_mode = $mode;
    }

    /**
     * Function to set the nls_lang
     *
     * @param string $charset
     */
    public function setNlsLang($charset = ORA_CHARSET_DEFAULT)
    {
        $this->charset = $charset;
    }

    /**
     * Function to set on or off the autocommit option
     *
     * @param bool $mode
     */
    public function setAutoCommit($mode = true)
    {
        $this->autocommit = $mode;
    }

    /**
     * Function to set the variable max size for binding
     *
     * @param int $size
     */
    public function setVarMaxSize($size)
    {
        $this->var_max_size = $size;
    }

    public function connect()
    {
        // Connection only once!
        if ($this->connection === null)
        {
            switch ($this->_config[5])
            {
                case ORA_CONNECTION_TYPE_PERSISTENT:
                    list($user, $password, $format, $charset, $mode) =
                        array
                        (
                            $this->_config[0],
                            $this->_config[1],
                            $this->_config[2],
                            $this->_config[3],
                            $this->_config[4]
                        );

                    $this->connection = @oci_pconnect($user, $password, $format, $charset, $mode);
                    if (!$this->connection)
                    {
                        new \InternalServerErrorController();
                        throw new RuntimeException('Error connection to the server: ' . oci_error());
                    }
                    break;

                case ORA_CONNECTION_TYPE_NEW:
                    list($user, $password, $format, $charset, $mode) =
                        array
                        (
                            $this->_config[0],
                            $this->_config[1],
                            $this->_config[2],
                            $this->_config[3],
                            $this->_config[4]
                        );
                    $this->connection = @oci_new_connect($user, $password, $format, $charset, $mode);
                    if (!$this->connection)
                    {
                        new \InternalServerErrorController();
                        throw new RuntimeException('Failed connecting to database: ' . oci_error());
                    }
                    break;

                default:
                    list($user, $password, $format, $charset, $mode) =
                        array
                        (
                            $this->_config[0],
                            $this->_config[1],
                            $this->_config[2],
                            $this->_config[3],
                            $this->_config[4]
                        );
                    $this->connection = @oci_connect($user, $password, $format, $charset, $mode);
                    if (!$this->connection)
                    {
                        new \InternalServerErrorController();
                        throw new RuntimeException('Failed connecting to database: ' . oci_error());
                    }
                    break;
            }
            unset($user, $password, $format, $charset, $mode);
        }
        return $this->connection;
    }

    public function disconnect()
    {
        $this->__destruct();
    }

    /**
     * Force execute the specified query
     * Not safe - Prune to execution errors - Use execute($sql_text, $bind) instead;
     * @param $query
     * @return resource
     */
    private function query($query)
    {
        if (!is_string($query) || empty($query))
        {
            new \InternalServerErrorController();
            throw new InvalidArgumentException('The specified query is not valid.');
        }

        // lazy connect to MySQL
        $this->connect();
        if (!$this->_result = oci_parse($this->connection, $query))
        {
            throw new RuntimeException('Error executing the specified query ' . $query . oci_error($this->connection));
        }

        return $this->_result;
    }

    private function getBindingType($var)
    {
        if (is_a($var, "OCI-Collection"))
        {
            $bind_type = SQLT_NTY;
            $this->setVarMaxSize(-1);
        }
        else if (is_a($var, "OCI-Lob"))
        {
            $bind_type = SQLT_CLOB;
            $this->setVarMaxSize(-1);
        }
        else
        {
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
    private function execute($sql_text, &$bind = [])
    {
        if (!is_resource($this->connection))
        {
            return false;
        }

        $this->last_query = $sql_text;
        $stid = @oci_parse($this->connection, $sql_text);

        $sd_id_int = (int)$stid;

        $this->statements[$sd_id_int]['text'] = $sql_text;
        $this->statements[$sd_id_int]['bind'] = $bind;

        if (!empty($bind))
        {
            foreach ($bind as $k => $v)
            {
                oci_bind_by_name(
                    $stid,
                    $k,
                    $bind[$k],
                    $this->var_max_size,
                    $this->getBindingType($bind[$k]));
            }
        }

        $com_mode = $this->autocommit ? OCI_COMMIT_ON_SUCCESS : OCI_DEFAULT;

        $this->execute_status = oci_execute($stid, $com_mode);

        return $this->execute_status ? $stid : false;
    }

    /**
     * Fetches the next row (for SELECT statements) into the internal result-buffer.
     *
     * @param resource $statement valid OCI statement id
     * @return bool
     */
    public function fetch($statement)
    {
        return oci_fetch($statement);
    }

    /**
     * Fetch array of select statement
     *
     * @param resource $statement valid OCI statement id
     * @return array
     */
    public function fetchArray($statement)
    {
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
    public function fetchRow($statement)
    {
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
    public function fetchAll($statement, $skip = 0, $maxrows = -1)
    {
        $rows = array();
        oci_fetch_all(
            $statement,
            $rows,
            $skip,
            $maxrows,
            OCI_FETCHSTATEMENT_BY_ROW + OCI_ASSOC);

        return $rows;
    }

    /**
     * Fetch row as object
     *
     * @param resource $statement valid OCI statement identifier
     * @return object
     */
    public function fetchObject($statement)
    {
        return oci_fetch_object($statement);
    }

    public function getResult($statement, $field)
    {
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
    public function defineByName($statement, $column_name, &$variable, $type = SQLT_CHR)
    {
        return oci_define_by_name($statement, $column_name, $variable, $type);
    }

    public function getFieldIsNull($statement, $field)
    {
        return oci_field_is_null($statement, $field);
    }

    public function getFieldName($statement, int $field)
    {
        return oci_field_name($statement, $field);
    }

    public function getFieldPrecision($statement, int $field)
    {
        return oci_field_precision($statement, $field);
    }

    public function getFieldScale($statement, int $field)
    {
        return oci_field_scale($statement, $field);
    }

    public function getFieldSize($statement, $field)
    {
        return oci_field_size($statement, $field);
    }

    public function getFieldTypeRaw($statement, int $field)
    {
        return oci_field_type_raw($statement, $field);
    }

    public function getFieldType($statement, int $field)
    {
        return oci_field_type($statement, $field);
    }

    /**
     * select command wrapper
     *
     * @param string $sql the query text
     * @param array | false $bind array of pairs binding variables
     * @return resource | false
     */
    private function parseSelect($sql, $bind = false)
    {
        return $this->execute($sql, $bind);
    }

    /**
     * Perform a SELECT statement
     * @param $table
     * @param string $where
     * @param string $fields
     * @param string $order
     * @param null $limit
     * @param null $offset
     * @param bool $bind
     * @return false|resource
     */
    public function select($table, $where = '', $fields = '*', $order = '', $limit = null, $offset = null, $bind = false)
    {
        if (!$limit || $limit == null)
        {
            $query = 'SELECT ' . $fields . ' FROM ' . $table
                . (($where) ? ' WHERE ' . $where : '')
                . (($order) ? ' ORDER BY ' . $order : '');
        }
        else {
            if (!$offset) {
                $query = 'SELECT ' . '*' . ' FROM ( '
                    . 'SELECT ' . $fields . ' FROM ' . $table
                    . (($where) ? ' WHERE ' . $where : '')
                    . (($order) ? ' ORDER BY ' . $order : '')
                    . ') WHERE ROWNUM ' . $limit ;
            }
            else {
                if ($offset === 1) {
                    $limit = 26;
                    $query = 'SELECT ' . '*' . ' FROM ( '
                        . 'SELECT ' . $fields . ' FROM ' . $table
                        . (($where) ? ' WHERE ' . $where : '')
                        . (($order) ? ' ORDER BY ' . $order : '')
                        . ') WHERE ROWNUM < ' . $limit;
                }
                else {
                    $highlimit = 25 * $offset + 1;
                    $lowlimit = 25 * ($offset - 1) - 1;
                    $query = 'SELECT ' . '*' . ' FROM ( '
                        . 'SELECT ' . $fields . ' FROM ' . $table
                        . (($where) ? ' WHERE ' . $where : '')
                        . (($order) ? ' ORDER BY ' . $order : '')
                        . ') WHERE ROWNUM > ' . $lowlimit . ' AND ROWNUM < ' . $highlimit ;
                }
            }
        }

        return $this->parseSelect($query, $bind);
    }

    /**
     * Perform a COUNT SELECT ON A TABLE
     */
    public function selectCount($table, $where = '', $fields = 'COUNT(*)', $order = '', $limit = null, $offset = null, $bind = false)
    {
        $query = 'SELECT ' . 'COUNT(*)' . ' FROM ' . $table
            . (($where) ? ' WHERE ' . $where : '');

        // . (($limit) ? ' LIMIT ' . $limit : '')
        //  . (($offset && $limit) ? ' OFFSET ' . $offset : '')
        //  . (($order) ? ' ORDER BY ' . $order : '');
        return $this->parseSelect($query, $bind);
    }

    /**
     * Insert row into table
     *
     * @param string $table name of table
     * @param array $arrayFieldsValues define pair field => value
     * @param bool $bind define pairs holder => value for binding
     * @param array $returning define fields for returning clause in insert statement
     * @return mixed if $returning is defined function return array of fields defined in $returning
     */
    public function insert($table, array $arrayFieldsValues, &$bind = false, $returning = [])
    {
        if (empty($arrayFieldsValues))
        {
            return false;
        }

        $fields = array();
        $values = array();

        foreach ($arrayFieldsValues as $f => $v)
        {
            $fields[] = $f;
            $values[] = $v;
        }

        $fields = implode(",", $fields);
        $values = implode(",", $values);
        $ret = "";
        if (!empty($returning))
        {
            foreach ($returning as $f => $h)
            {
                $ret_fields[] = $f;
                $ret_binds[] = ":$h";
                $bind[":$h"] = "";
            }
            $ret = " returning " . (implode(",", $ret_fields)) . " into " . (implode(",", $ret_binds));
        }

        $sql = "insert into $table ($fields) values($values) $ret";

        $result = $this->execute($sql, $bind);

        if ($result === false)
        {
            return false;
        }

        if (empty($returning))
        {
            return $result;
        }
        else
        {
            $result = array();
            foreach ($returning as $f => $h)
            {
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
     * @param array $condition
     * @param array | false $bind
     * @param array $returning
     * @return resource
     */
    public function update($table, array $arrayFieldsValues, $condition =  [], &$bind = false, $returning = [])
    {
        if (empty($arrayFieldsValues))
        {
            return null;
        }

        $fields = array();
        $values = array();

        foreach ($arrayFieldsValues as $f => $v)
        {
            $fields[] = "$f = $v";
        }

        $fields = implode(",", $fields);
        if (empty($condition))
        {
            $where = "true";
        }
        else
        {
            foreach ($condition as $c => $v)
            {
                $where[] = "$c = $v";
            }

            $where = implode(' AND ', $where);
        }
//        echo $fields . "<br />";
//        echo $where . "<br />";

        $ret = "";
        if ($returning) {
            foreach ($returning as $f => $h) {
                $ret_fields[] = $f;
                $ret_binds[] = ":$h";
                $bind[":$h"] = "";
            }
            $ret = " returning " . (implode(",", $ret_fields)) . " into " . (implode(",", $ret_binds));
        }
        $sql = "update $table set $fields where $where $ret";
//        echo "Query de update este: $sql<br /><br />";
        $result = $this->execute($sql, $bind);
        if ($result === false) {
            return null;
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
        return oci_new_descriptor($this->connection, $type);
    }

    /**
     * Allocates a new collection object
     *
     * @param string $typename Should be a valid named type (uppercase).
     * @param string $schema Should point to the scheme, where the named type was created. The name of the current user is the default value.
     * @return OCI-Collection
     */
    public function getNewCollection($typename, $schema = null) {
        return oci_new_collection($this->connection, $typename, $schema);
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
        if (!is_resource($this->connection))
            return false;
        $sql = "begin $stored_proc(:$bind); end;";
        $curs = oci_new_cursor($this->connection);
        $stmt = oci_parse($this->connection, $sql);
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
    public function freeStatements(array $array_stid) {
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
        if (is_resource($this->connection))
            return @oci_commit($this->connection);
        else
            return false;
    }

    /**
     * Rollback transaction
     *
     * @return bool
     */
    public function rollback() {
        if (is_resource($this->connection))
            return @oci_rollback($this->connection);
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
        return $this->statements[(int)$stid] ? $this->statements[(int)$stid] : false;
    }

    /**
     * This function returns last command exec status
     *
     * @return bool
     */
    public function getExecuteStatus() {
        return $this->execute_status;
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
        if (is_resource($this->connection)) {
            return @oci_server_version($this->connection);
        }
        else
        {
            return false;
        }
    }

    public function setAction(string $action_name) {
        return @oci_set_action($this->connection, $action_name);
    }

    public function setClientID(string $client_id) {
        return @oci_set_client_identifier($this->connection, $client_id);
    }

    public function setClientInfo(string $client_info) {
        return @oci_set_client_info($this->connection, $client_info);
    }

    public function setPrefetch(int $rows) {
        return oci_set_prefetch($this->connection, $rows);
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

    public function getConnection() {
        if($this->connection === null)
        {
            $this->connect();
        }

        return $this->connection;
    }

}
