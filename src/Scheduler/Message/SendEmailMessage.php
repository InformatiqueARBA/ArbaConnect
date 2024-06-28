<?php

namespace App\Scheduler\Message;

final class SendEmailMessage
{

    private $from;
    private $to;
    private $subject;
    private $html;
    private $path;

    public function __construct(string $from, string $to, string $subject, string $html, string $path)
    {

        $this->from = $from;
        $this->to = $to;
        $this->subject = $subject;
        $this->html = $html;
        $this->path = $path;
    }

    public function getFrom(): string
    {
        return $this->from;
    }

    public function getTo(): string
    {
        return $this->to;
    }

    public function getSubject(): string
    {
        return $this->subject;
    }

    public function getHtml(): string
    {
        return $this->html;
    }

    public function getPath(): string
    {
        return $this->path;
    }
}
