<?php
/**
 * Created by PhpStorm.
 * User: ghita
 * Date: 4/13/2018
 * Time: 7:05 PM
 */

require_once('Database.php');

class CustomDatabase extends Database
{
    public function __construct($dbname)
    {
        // modify $dbname before calling parent constructor and that will construct a connection with the new dbname
        $this->dbname = $dbname;
        Parent::__construct();
    }

    public function deleteDatabase()
    {
        pg_close($this->connection);

        $format = 'set PGPASSWORD=%s&& psql -h %s -U %s -p %d -c "%s"';
        $query = 'DROP DATABASE ' . $this->dbname;
        $cmd = sprintf($format, $this->password, $this->host, $this->user, $this->port, $query);
        shell_exec($cmd);
    }
}