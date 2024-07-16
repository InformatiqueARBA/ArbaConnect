<?php


namespace App\MessageHandler;

use App\Message\SendARMessage;
use App\Service\SendARService;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class SendARMessageHandler
{
    private $sendARService;

    public function __construct(SendARService $sendARService)
    {
        $this->sendARService = $sendARService;
    }

    public function __invoke(SendARMessage $message)
    {
        $this->sendARService->sendAR(
            $message->getNobon(),
            $message->getFormattedDate(),
            $message->getMailAR()
        );
    }
}
