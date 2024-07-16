<?php

namespace App\Message;

/*Message pour l'envoi de l'AR modifié en asychrone */

class SendARMessage
{
    private $nobon;
    private $formattedDate;
    private $mailAR;

    public function __construct($nobon, $formattedDate, $mailAR)
    {
        $this->nobon = $nobon;
        $this->formattedDate = $formattedDate;
        $this->mailAR = $mailAR;
    }

    public function getNobon()
    {
        return $this->nobon;
    }

    public function getFormattedDate()
    {
        return $this->formattedDate;
    }

    public function getMailAR()
    {
        return $this->mailAR;
    }
}
