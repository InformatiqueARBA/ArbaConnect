<?php

namespace App\ArbaConnect\Service;

use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\Part\DataPart;
use Symfony\Component\Mime\Part\Multipart\RelatedPart;

class MailerService
{
    private MailerInterface $mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    private function getSignature(): string
    {
        $signaturePath = '/var/www/ArbaConnect/public/signature/signature.html';

        if (file_exists($signaturePath)) {
            return file_get_contents($signaturePath);
        }

        // si la signature ne fonctionne pas
        return '<p>Coopérative ARBA</p>';
    }


    public function sendMail(string $to, string $subject, string $content): void
    {
        $signature = $this->getSignature();
        $emailContent = $content . '<br><br>' . $signature;

        $email = (new Email())
            ->from('cooperativearba@arba.coop')
            ->to($to)
            ->subject($subject)
            ->embed(fopen('/var/www/ArbaConnect/public/signature/artipole_signature.png', 'r'), 'artipole_signature.png')
            ->embed(fopen('/var/www/ArbaConnect/public/signature/logoArba_signature.png', 'r'), 'logoArba_signature.png')
            ->embed(fopen('/var/www/ArbaConnect/public/signature/salleExpo_signature.png', 'r'), 'salleExpo_signature.png')
            ->embed(fopen('/var/www/ArbaConnect/public/signature/linkedin_signature.png', 'r'), 'linkedin_signature.png')
            ->html($emailContent);


        $this->mailer->send($email);
    }
}
