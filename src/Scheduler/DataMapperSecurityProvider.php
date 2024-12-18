<?php

namespace App\Scheduler;

use App\Scheduler\Message\DataMapperSecurityMessage;
use Symfony\Component\Scheduler\Attribute\AsSchedule;
use Symfony\Component\Scheduler\RecurringMessage;
use Symfony\Component\Scheduler\Schedule;
use Symfony\Component\Scheduler\ScheduleProviderInterface;

#[AsSchedule('data_mapper_security')]
class DataMapperSecurityProvider implements ScheduleProviderInterface
{
    public function getSchedule(): Schedule
    { {
            return (new Schedule())->add(
                RecurringMessage::cron('30 23 * * *', new DataMapperSecurityMessage())
            );
        }
    }
}
