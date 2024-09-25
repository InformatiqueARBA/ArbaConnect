<?php

namespace App\Scheduler\Handler;

use App\Scheduler\Message\TourCodeMessage;
use App\DeliveryDateModule\Service\TourCodeService;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class TourCodeMessageHandler
{
    private $tourCodeService;

    public function __construct(TourCodeService $tourCodeService)
    {
        $this->tourCodeService = $tourCodeService;
    }

    public function __invoke(TourCodeMessage $message)
    {
        $this->tourCodeService->getCodeTour();
    }
}
