<?php
/**
 * Created by PhpStorm.
 * User: ghita
 * Date: 4/13/2018
 * Time: 7:40 PM
 */

use PHPUnit\Framework\TestCase;

// dependencies required by the test
// for this test we change the path for log file to tests\Database\log.txt modifying the macro

// logger messages macros
define('ERROR'     , '[ERROR] ');
define('WARNING'   , '[WARNING] ');

require_once '../../logger/Logger.php';
require_once '../../database/CustomDatabase.php';

class CustomDatabaseTest extends TestCase
{
    public function test__construct()
    {
        $db_name = 'test_db';

        $db = new CustomDatabase($db_name);
        $this->addToAssertionCount($db->databaseExists());

        $db->deleteDatabase();
        $this->addToAssertionCount(!$db->databaseExists());
    }
}
