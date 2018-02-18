<?php
declare(strict_types=1);

namespace MeetupManagement\Domain\Model;

final class Meetup
{
    /**
     * @var MeetupId
     */
    private $meetupId;

    /**
     * @var ScheduledDate
     */
    private $scheduledDate;

    /**
     * @var Title
     */
    private $title;

    /**
     * @var bool
     */
    private $cancelled = false;


    private $eventList = [];

    private function __construct()
    {
    }

    public static function schedule(MeetupId $meetupId, ScheduledDate $scheduledDate, Title $title): Meetup
    {
        $meetup = new static();
        $meetup->record(new MeetupScheduled($meetupId, $scheduledDate, $title));

        return $meetup;
    }

    public static function reconstitute(array $events)
    {
        $meetup = new static();

        array_walk($events, [$meetup, 'applyEvent']);

        return $meetup;
    }

    public function reschedule(ScheduledDate $newDate): void
    {
        if ($this->cancelled) {
            throw new \LogicException('You can not rescheduled a cancelled meetup');
        }

        $this->record(new MeetupRescheduled($this->meetupId, $newDate));
    }

    public function cancel(): void
    {
        $this->record(new MeetupCancelled($this->meetupId));
    }

    public function hasBeenCancelled(): bool
    {
        return $this->cancelled;
    }

    public function meetupId(): MeetupId
    {
        return $this->meetupId;
    }

    public function scheduledDate(): ScheduledDate
    {
        return $this->scheduledDate;
    }

    public function title(): Title
    {
        return $this->title;
    }

    public function dequeueEvent()
    {
        return array_shift($this->eventList);
    }

    private function record($event)
    {
        $this->applyEvent($event);

        $this->eventList[] = $event;
    }

    private function meetupScheduled(MeetupScheduled $event)
    {
        $this->meetupId = $event->getMeetupId();
        $this->scheduledDate = $event->getScheduledDate();
        $this->title = $event->getTitle();
    }

    private function meetupRescheduled(MeetupRescheduled $event)
    {
        $this->scheduledDate = $event->getNewDate();
    }

    private function meetupCancelled(MeetupCancelled $event)
    {
        $this->cancelled = true;
    }

    /**
     * @param $event
     */
    private function applyEvent($event)
    {
        if ($event instanceof MeetupScheduled) {
            $this->meetupScheduled($event);
        } elseif ($event instanceof MeetupRescheduled) {
            $this->meetupRescheduled($event);
        } elseif ($event instanceof MeetupCancelled) {
            $this->meetupCancelled($event);
        }
    }
}
