<?php

namespace App\Scheduler;

use App\Scheduler\Message\PopulateAcdbMessage;
use Symfony\Component\Scheduler\Attribute\AsSchedule;
use Symfony\Component\Scheduler\RecurringMessage;
use Symfony\Component\Scheduler\Schedule;
use Symfony\Component\Scheduler\ScheduleProviderInterface;

#[AsSchedule('populate_acdb')]
class PopulateAcdbProvider implements ScheduleProviderInterface
{
    public function getSchedule(): Schedule
    { {
            return (new Schedule())->add(
                // config : 10 minutes
                RecurringMessage::cron('*/10 * * * *', new PopulateAcdbMessage())
            );
        }
    }
}
