<?php

namespace App\Service;

use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class MailerService
{
    private $mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }


    public function sendMail(String $to, String $subject, String $content)
    {

        $email = (new Email())
            ->from('informatique@arba.coop')
            ->to($to)
            ->subject($subject)
            ->text($content)
            ->html('<p>' . $content . '</p>');

        $this->mailer->send($email);
    }
}
