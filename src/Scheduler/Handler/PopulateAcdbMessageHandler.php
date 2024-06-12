<?php

namespace App\Scheduler\Handler;

use App\Scheduler\Message\PopulateAcdbMessage;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use App\Service\PopulateAcdbService;

#[AsMessageHandler]
final class PopulateAcdbMessageHandler
{
    private $populateAcdbService;

    public function __construct(PopulateAcdbService $populateAcdbService)
    {
        $this->populateAcdbService = $populateAcdbService;
    }

    public function __invoke(PopulateAcdbMessage $message)
    {
        $this->populateAcdbService->populateAcdb();
    }
}
