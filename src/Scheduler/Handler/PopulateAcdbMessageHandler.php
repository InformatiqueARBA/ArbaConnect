<?php

namespace App\Scheduler\Handler;

use App\ArbaConnect\Service\OdbcService;
use App\DeliveryDateModule\Service\RequestOdbcDeliveryDateService;
use App\Scheduler\Message\PopulateAcdbMessage;
use App\DeliveryDateModule\Service\PopulateAcdbService;
use App\DeliveryDateModule\Service\DataMapperService;
use App\DeliveryDateModule\Service\DatabaseSwitcherService;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

#[AsMessageHandler]
final class PopulateAcdbMessageHandler
{
    private PopulateAcdbService $populateAcdbService;
    private DataMapperService $dataMapperService;
    private DatabaseSwitcherService $databaseSwitcherService;
    private ParameterBagInterface $params;
    private OdbcService $odbcService;
    private RequestOdbcDeliveryDateService $requestOdbcDeliveryDateService;


    public function __construct(
        PopulateAcdbService $populateAcdbService,
        DataMapperService $dataMapperService,
        DatabaseSwitcherService $databaseSwitcherService,
        ParameterBagInterface $params,
        OdbcService $odbcService,
        RequestOdbcDeliveryDateService $requestOdbcDeliveryDateService
    ) {
        $this->populateAcdbService = $populateAcdbService;
        $this->dataMapperService = $dataMapperService;
        $this->databaseSwitcherService = $databaseSwitcherService;
        $this->params = $params;
        $this->odbcService = $odbcService;
        $this->requestOdbcDeliveryDateService = $requestOdbcDeliveryDateService;
    }


    public function __invoke(PopulateAcdbMessage $message): void
    {

        $this->populateAcdbService->populateAcdb(
            $this->dataMapperService,
            $this->databaseSwitcherService,
            $this->params,
            $this->odbcService,
            $this->requestOdbcDeliveryDateService
        );
    }
}
