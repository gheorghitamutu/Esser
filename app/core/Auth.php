<?php
/**
 * IDE: PhpStorm
 * Project: esser
 * Filename: Auth.php
 * User: Gheorghita Mutu
 * Email: gheorghitamutu@gmail.com
 * Date: 6/7/2018
 * Time: 6:00 PM
 */

class Auth
{
    // $connection should be db connection
    static function auth_user($connection, $uname, $psw)
    {
        if (Auth::authenticateUser($connection, $uname, $psw))
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

    static function authenticateUser($connection, $username, $password)
    {
        // Test the username and password parameters
        if (!isset($username) || !isset($password))
            return false;

        // Create a digest of the password
        // $password_digest = md5(trim($password));

        // Formulate the SQL find the user
        // Execute the query

        return true;
    }

    // Connects to a session and checks that the user has
    // authenticated and that the remote IP address matches
    // the address used to create the session.
    static function sessionAuthenticate( )
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

