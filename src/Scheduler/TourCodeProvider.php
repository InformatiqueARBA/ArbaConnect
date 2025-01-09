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
                // config :toutes les 5 heures
                RecurringMessage::cron('30 23 * * *', new TourCodeMessage())
            );
        }
    }
}
