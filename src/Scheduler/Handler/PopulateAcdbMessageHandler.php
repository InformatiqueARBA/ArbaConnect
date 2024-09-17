<?php

namespace App\Scheduler\Handler;

use App\Scheduler\Message\PopulateAcdbMessage;
use App\Service\PopulateAcdbService;
use App\Service\DataMapperService;
use App\Service\DatabaseSwitcherService;
use App\Service\OdbcService;
use App\Service\RequestOdbcService;
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
    private RequestOdbcService $requestOdbcService;


    public function __construct(
        PopulateAcdbService $populateAcdbService,
        DataMapperService $dataMapperService,
        DatabaseSwitcherService $databaseSwitcherService,
        ParameterBagInterface $params,
        OdbcService $odbcService,
        RequestOdbcService $requestOdbcService
    ) {
        $this->populateAcdbService = $populateAcdbService;
        $this->dataMapperService = $dataMapperService;
        $this->databaseSwitcherService = $databaseSwitcherService;
        $this->params = $params;
        $this->odbcService = $odbcService;
        $this->requestOdbcService = $requestOdbcService;
    }


    public function __invoke(PopulateAcdbMessage $message): void
    {

        $this->populateAcdbService->populateAcdb(
            $this->dataMapperService,
            $this->databaseSwitcherService,
            $this->params,
            $this->odbcService,
            $this->requestOdbcService
        );
    }
}
