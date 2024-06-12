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
                // RecurringMessage::every('5 seconds', new WriteInFileMessage(2))
                RecurringMessage::cron('*/5 * * * *', new PopulateAcdbMessage())
            );
        }
    }
}
