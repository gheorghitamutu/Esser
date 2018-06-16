<?php
/**
 * Created by PhpStorm.
 * User: ghita
 * Date: 6/16/2018
 * Time: 12:31 PM
 */

require_once "../../../php/pear/Mail.php";

class GMail
{
    private $from = "gheorghitamutu@gmail.com";
    private $password = "REPLACE_THIS";
    private $type = 'smtp';
    private $host = 'ssl://smtp.gmail.com';
    private $port = '465';
    private $auth = true;

    public function __construct()
    {

    }

    public static function send_email($to, $subject, $body)
    {
        $instance = new GMail();
        return $instance->send($to, $subject, $body);
    }


    private function send($to, $subject, $body)
    {

        $headers = array(
            'From' => $this->from,
            'To' => $to,
            'Subject' => $subject
        );

        $smtp = Mail::factory(
            $this->type,
            array
            (
                'host' => $this->host,
                'port' => $this->port,
                'auth' => $this->auth,
                'username' => $this->from,
                'password' => $this->password
            ));

        $mail = $smtp->send($to, $headers, $body);

        return $mail === true;
    }
}
