<?php
/**
 * IDE: PhpStorm
 * Project: esser
 * Filename: Controller.php
 * User: Gheorghita Mutu
 * Email: gheorghitamutu@gmail.com
 * Date: 4/5/2018
 * Time: 2:08 PM
 */


class Controller
{
    protected $model_class = null;

    public static function redirect($url)
    {
        header("location: {$url}");
    }

    public function model($model)
    {
        // array of 4 ELEMENTS -> user, pass, connection mode, and connection type!
        $adapter = new DatabaseConnectivity\OracleAdapter(
            [
                ROOT_ADMIN_USER,
                ROOT_ADMIN_PASS,
                OCI_DEFAULT,
                ORA_CONNECTION_TYPE_DEFAULT
            ]);

        $connection = $adapter->getConnection();

        if (!is_resource($connection))
        {
            new InternalServerErrorController();
            return;
        }

        $temp_model = ('AppModel\\' . $model);
        $this->model_class = new $temp_model($adapter);
    }

    protected function try_authenticate($username, $password, $is_admin_cp)
    {
        if (!$this->authenticate_user($username, $password))
        {
            return false;
        }

        $this->model_class->get_mapper()->update(
            'USERACCS',
            array
            (
                'userState' => 2
            ),
            array
            (
                'userId' => $_SESSION['userid']
            ));

        $this->log_user_activity("'" . ($is_admin_cp ? "Admin" : "Normal") . " user " . $_SESSION["uname"] . " has logged in!'");


        return true;
    }

    protected function authenticate_user($username, $password)
    {
        $password_hash = hash('sha512', $username . SALT . $password);

        $this->model('Useracc');
        $users_found = $this->model_class->get_mapper()->findAll(
            "userName = '$username' AND userPass = '$password_hash'");

        if (count($users_found) > 1)
        {
            //Forbidden/Internal server error(500)!
            new InternalServerErrorController();
            throw new RuntimeException('Multiple matches in login! Please contact an administrator!');
        }

        if (count($users_found) === 0)
        {
            //No match, failed login;
            return false;
        }

        $_SESSION["login_ip"]   = ($_SERVER["REMOTE_ADDR"] == '::1' ? '127.0.0.1' : $_SERVER["REMOTE_ADDR"]);
        $_SESSION["uname"]      = $users_found[0]["userName"];
        $_SESSION["userid"]     = $users_found[0]["userId"];

        return true;
    }

    // Connects to a session and checks that the user has
    // authenticated and that the remote IP address matches
    // the address used to create the session.
    protected function session_authenticate( )
    {
        // Check if the user hasn't logged in
        if (!isset($_SESSION["uname"]))
        {
            return false;
        }

        // Check if the request is from a different IP address to previously
        if (!isset($_SESSION["login_ip"]) || ($_SESSION["login_ip"] != ($_SERVER["REMOTE_ADDR"] == '::1' ? '127.0.0.1' : $_SERVER["REMOTE_ADDR"])))
        {
            // The request did not originate from the machine
            // that was used to create the session.
            return false;
        }
        return true;
    }
//
    protected function log_user_activity($uLogDescription)
    {
        $this->model('UserLog');
        $this->model_class->get_mapper()->insert(
            'USERLOGS',
            array
            (
                'uLogDescription'   => $uLogDescription,
                'uLogSourceIP'      => "'" . (($_SERVER["REMOTE_ADDR"]=='::1')?'127.0.0.1':$_SERVER['REMOTE_ADDR']) . "'"
            )
        );
    }
}
