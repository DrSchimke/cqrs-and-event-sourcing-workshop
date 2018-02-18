<?php
/**
 * This file is part of cqrs-and-event-sourcing-workshop.
 *
 * (c) Sascha Schimke <sascha@schimke.me>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace MeetupManagement\Domain\Model;

class MeetupRescheduled implements MeetupEvent
{
    /** @var MeetupId */
    private $meetupId;

    /** @var ScheduledDate */
    private $newDate;

    public function __construct(MeetupId $meetupId, ScheduledDate $newDate)
    {
        $this->meetupId = $meetupId;
        $this->newDate = $newDate;
    }

    public function getMeetupId(): MeetupId
    {
        return $this->meetupId;
    }

    public function getNewDate(): ScheduledDate
    {
        return $this->newDate;
    }
}
