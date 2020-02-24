<?php
/**
 * Created by PhpStorm.
 * User: ghita
 * Date: 6/16/2018
 * Time: 12:31 PM
 */

class GMail
{
    private $from = "gheorghitamutu@gmail.com";     // replace this as needed
    private $reply_to = "gheorghitamutu@gmail.com"; // replace this as needed
    private $x_mailer = 'PHP/';
    private $headers = null;

    public function __construct()
    {
        $this->x_mailer  .= phpversion();

        $this->headers =
            array
            (
                'From' => $this->from,
                'Reply-To' => $this->reply_to,
                'X-Mailer' => $this->x_mailer
            );
    }

    public static function send_email($to, $subject, $body)
    {
        $instance = new GMail();
        return $instance->send($to, $subject, $body);
    }


    private function send($to, $subject, $body)
    {
        return mail($to, $subject, $body, $this->headers) === true;
    }
}
