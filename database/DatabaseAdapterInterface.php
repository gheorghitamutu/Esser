<?php

namespace DatabaseConnectivity;

interface DatabaseAdapterInterface
{
    function getError();

    function setFetchmode($mode);

    function setNLSLang($charset);

    function setAutoCommit($mode);

    function setVarMaxSize($size);

    function connect();

    function disconnect();

    function fetch($statement);

    function fetchArray($statement);

    function fetchRow($statement);

    function fetchAll($statement);

    function fetchObject($statement);

    function getResult($statement, $field);

    function defineByName($statement, $column_name, &$variable, $type);

    function getFieldIsNull($statement, $field);

    function getFieldName($statement, int $field);

    function getFieldPrecision($statement, int $field);

    function getFieldScale($statement, int $field);

    function getFieldSize($statement, $field);

    function getFieldTypeRaw($statement, int $field);

    function getFieldType($statement, int $field);

    function select($table, $where = '', $fields = ''*'', $order = '', $limit = null, $offset = null, $bind = false);

    function insert($table, array $arrayFieldsValues, &$bind = false, $returning = false);

    function update($table, array $arrayFieldsValues, $condition = false, &$bind = false, $returning = false);

    function delete($table, $condition, &$bind = false, $returning = false);

    function countRowsAffected($statement);

    function countFieldsAffected($statement);

    function getNewDescriptor($type = OCI_DTYPE_LOB);

    function getNewCollection($typename, $schema = null);

    function getStoredProc($name, $params = false, &$bind = false);

    function getFunc($name, $params = false, $bind = false);

    function getCursor($stored_proc, $bind);

    function closeCursor($statement);

    function freeStatement($stid);

    function freeStatements(array $array_stid) ;

    function commit();

    function rollback();

    function setInternalDebug($mode);

    function getStatement($stid);

    function getExecuteStatus();

    function getQuerySnapshot($stid = false);

    function getServerVer();

    function setAction(string $action_name);

    function setClientID(string $client_id);

    function setClientInfo(string $client_info);

    function setPrefetch(int $rows);

    function getStatementType($statement);

    function dumpQueriesStack();

    function getConnection();
}
