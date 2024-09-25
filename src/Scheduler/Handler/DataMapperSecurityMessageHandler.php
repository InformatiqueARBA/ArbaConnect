<?php

namespace App\Scheduler\Handler;

use App\ArbaConnect\Service\DataMapperSecurityService;
use App\Scheduler\Message\DataMapperSecurityMessage;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsMessageHandler]
final class DataMapperSecurityMessageHandler
{
    private $dataMapperSecurityService;
    private $hasher;

    public function __construct(DataMapperSecurityService $dataMapperSecurityService, UserPasswordHasherInterface $hasher)
    {
        $this->dataMapperSecurityService = $dataMapperSecurityService;
        $this->hasher = $hasher;
    }

    public function __invoke(DataMapperSecurityMessage $dataMapperSecurityMessage)
    {
        $this->dataMapperSecurityService->userMapper($this->hasher);
    }
}
