<?php

namespace App\Service;

use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

/*
Service sollicité pour l'envoi des mails
*/

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
            // TODO: definir le mail expéditeur
            ->from('informatique@arba.coop')
            ->to($to)
            ->subject($subject)
            ->html($content);

        $this->mailer->send($email);
    }


    public function sendMailWithAttachment(String $from, String $to, String $subject, String $html, String $attachmentPath)
    {
        $email = (new Email())
            ->from($from)
            ->to($to)
            ->subject($subject)
            ->html($html)
            ->attachFromPath($attachmentPath)
            ->bcc('informatique@arba.coop');

        $this->mailer->send($email);
    }
}
