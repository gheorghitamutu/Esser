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
    //protected $model_name = '';
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
            echo 'Invalid connection in controller!';
        }

        $temp_model = ('AppModel\\' . $model);
        $this->model_class = new $temp_model($adapter);
    }

    protected function auth_user($uname, $psw)
    {
        if ($this->authenticate_user($uname, $psw))
        {
            $_SESSION["uname"] = $uname;

            // Register the IP address that started this session
            $_SESSION["login_ip"] = $_SERVER["REMOTE_ADDR"];

            return true;
        }
        else
        {
            // The authentication failed
            return false;
        }
    }

    protected function authenticate_user($username, $password)
    {
        // Test the username and password parameters
        if (!isset($username) || !isset($password))
            return false;

        $salt = '$1_2jlh83#@J^Q';
        $password_hash = hash('sha512', $username. $salt . $password);

        $user_found = $this->model_class->get_mapper()->findAll('userName = \'' . $username . '\' and userPass = \'' . $password_hash.  '\'');
        // check if $user_found not empty

        return $username === $user_found['userName'];
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
        if (!isset($_SESSION["login_ip"]) || ($_SESSION["login_ip"] != $_SERVER["REMOTE_ADDR"]))
        {
            // The request did not originate from the machine
            // that was used to create the session.
            return false;
        }
        return true;
    }
}
