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
        if ($is_admin_cp)
        {
            if (($result = $this->authenticate_admin_cp($username, $password))['0'] !== false)
            {
                $_SESSION["login_ip"]   = $_SERVER["REMOTE_ADDR"];

                //Register other session details that could be usefull;
                $_SESSION["uname"]      = $result[1]['userName'];
                $_SESSION["userid"]     = $result[1]['userId'];

                //aici deja e incarcat modeul de Useracc $this->model('Useracc');
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

                $this->model('UserLog');
                $this->model_class->get_mapper()->insert(
                    'USERLOGS',
                    array
                    (
                        'uLogDescription'   => "'".$_SESSION['uname']   ." has logged in!'",
                        'uLogSourceIP'      => "'".$_SESSION['login_ip']."'"
                    )
                );
                return $result;
            }
            else
            {
                  return array('0' => false);
            }
        }
        else if (!$is_admin_cp)
        {
            if (($result = $this->authenticate_user($username, $password)) !== false)
            {
                // Register the IP address that started this session
                $_SESSION["login_ip"] = $_SERVER["REMOTE_ADDR"];

                //Register other session details that could be usefull;
                $_SESSION["uname"] = $result[1]['userName'];
                $_SESSION["userid"] = $result[1]['userId'];
                $this->model_class->get_mapper()->update(
                    'USERACCS',
                    array
                    (
                        'userState' => 2
                    ),
                    array
                    (
                        'userId' => $_SESSION['userid']
                    )
                );
                return $result;
            }
            else
            {
                // The authentication failed
                return array(0 => false);
            }
        }
        else
        {
            // The authentication failed
            return array(0 => false);
        }
    }

    protected function authenticate_admin_cp($username, $password)
    {
        $salt = '$1_2jlh83#@J^Q';
        $password_hash = hash('sha512', $username . $salt . $password);
        $queries = $this->model_class->get_mapper()->findAll(
            "userName = '$username' AND userPass = '$password_hash' and userType = 3");

        if (count($queries) > 1)
        {
            //Forbidden/Internal Server Error(500)!
            new InternalServerErrorController();
            throw new RuntimeException('Multiple matches in login! Please check database!');
        }

        if (count($queries) === 0 || count($queries) === null)
        {
            return false;
        }

        $result =  array(($queries['0']['userName'] === $username), $queries['0'] );

        return $result;
    }

    protected function authenticate_user($username, $password)
    {
        $salt = '$1_2jlh83#@J^Q';
        $password_hash = hash('sha512', $username. $salt . $password);

        $user_found = $this->model_class->get_mapper()->findAll("userName = '$username' AND userPass = '$password_hash'");

        if (count($user_found) > 1)
        {
            //Forbidden/Internal server error(500)!
            new InternalServerErrorController();
            throw new RuntimeException('Multiple matches in login! Please contact an administrator!');
        }

        if (count($user_found) === 0 || count($user_found) === null)
        {
            //No match, so failed login;
            return false;
        }
        $result = [($user_found[0]['userName'] === $username), $user_found[0]];
        return $result;
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
