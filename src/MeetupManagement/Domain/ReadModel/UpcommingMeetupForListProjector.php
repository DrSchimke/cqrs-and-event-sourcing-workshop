<?php
/**
 * This file is part of cqrs-and-event-sourcing-workshop.
 *
 * (c) Sascha Schimke <sascha@schimke.me>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace MeetupManagement\Domain\ReadModel;

use MeetupManagement\Domain\Model\MeetupCancelled;
use MeetupManagement\Domain\Model\MeetupRescheduled;
use MeetupManagement\Domain\Model\MeetupScheduled;

class UpcommingMeetupForListProjector
{
    /** @var UpcomingMeetupForListViewRepository */
    private $repository;

    public function __construct(UpcomingMeetupForListViewRepository $repository)
    {
        $this->repository = $repository;
    }

    public function onMeetupScheduled(MeetupScheduled $event)
    {
        $meetup = new UpcomingMeetupForListView();
        $meetup->title = $event->getTitle();
        $meetup->scheduledDate = $event->getScheduledDate();

        $this->repository->save($event->getMeetupId(), $meetup);
    }

    public function onMeetupRescheduled(MeetupRescheduled $event)
    {
        $meetup = $this->repository->byId($event->getMeetupId());
        $meetup->scheduledDate = $event->getNewDate();

        $this->repository->save($event->getMeetupId(), $meetup);
    }

    public function onMeetupCancelleld(MeetupCancelled $event)
    {
        $this->repository->remove($event->getMeetupId());
    }
}