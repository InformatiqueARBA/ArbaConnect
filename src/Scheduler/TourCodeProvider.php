<?php

namespace App\Scheduler;


use App\Scheduler\Message\TourCodeMessage;
use Symfony\Component\Scheduler\Attribute\AsSchedule;
use Symfony\Component\Scheduler\RecurringMessage;
use Symfony\Component\Scheduler\Schedule;
use Symfony\Component\Scheduler\ScheduleProviderInterface;

#[AsSchedule('tourCode')]
class TourCodeProvider implements ScheduleProviderInterface
{
    public function getSchedule(): Schedule
    { {
            return (new Schedule())->add(
                // RecurringMessage::every('5 seconds', new WriteInFileMessage(2))
                // config : 23h30 tous les jours
                RecurringMessage::cron('*/10 * * * *', new TourCodeMessage())
                // RecurringMessage::cron('30 22 * * *', new TourCodeMessage())

            );
        }
    }
}
