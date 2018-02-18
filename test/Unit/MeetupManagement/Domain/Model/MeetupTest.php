<?php
declare(strict_types=1);

namespace Test\Unit\MeetupManagement\Domain\Model;

use MeetupManagement\Domain\Model\Meetup;
use MeetupManagement\Domain\Model\MeetupCancelled;
use MeetupManagement\Domain\Model\MeetupId;
use MeetupManagement\Domain\Model\MeetupRescheduled;
use MeetupManagement\Domain\Model\MeetupScheduled;
use MeetupManagement\Domain\Model\ScheduledDate;
use MeetupManagement\Domain\Model\Title;

class MeetupTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function it_has_an_id_title_and_scheduled_date(): void
    {
        $meetupId = MeetupId::fromString('00de6d41-f99d-488e-9753-03932e79d7d0');
        $scheduledDate = ScheduledDate::fromString('2016-06-13');
        $title = Title::fromString('An evening with CQRS');

        $meetup = Meetup::schedule($meetupId, $scheduledDate, $title);

        /** @var MeetupScheduled $event */
        $event = $meetup->dequeueEvent();

        self::assertInstanceOf(MeetupScheduled::class, $event);
        self::assertEquals($meetupId, $event->getMeetupId());
        self::assertEquals($title, $event->getTitle());
        self::assertEquals($scheduledDate, $event->getScheduledDate());
    }

    /**
     * @test
     */
    public function it_can_be_rescheduled(): void
    {
        $meetup = Meetup::schedule(
            MeetupId::fromString('00de6d41-f99d-488e-9753-03932e79d7d0'),
            ScheduledDate::fromString('2016-06-13'),
            Title::fromString('An evening with CQRS')
        );

        $newDate = ScheduledDate::fromString('2016-07-14');
        $meetup->reschedule($newDate);

        $meetup->dequeueEvent();

        /** @var MeetupRescheduled $event */
        $event = $meetup->dequeueEvent();

        self::assertInstanceOf(MeetupRescheduled::class, $event);
        self::assertEquals($newDate, $event->getNewDate());
    }

    /**
     * @test
     */
    public function it_can_be_cancelled(): void
    {
        $meetup = Meetup::schedule(
            MeetupId::fromString('00de6d41-f99d-488e-9753-03932e79d7d0'),
            ScheduledDate::fromString('2016-06-13'),
            Title::fromString('An evening with CQRS')
        );

        $meetup->cancel();

        $meetup->dequeueEvent();

        /** @var MeetupCancelled $event */
        $event = $meetup->dequeueEvent();

        self::assertInstanceOf(MeetupCancelled::class, $event);
    }

    /**
     * @test
     */
    public function a_cancelled_meetup_can_not_be_rescheduled(): void
    {
        $meetup = Meetup::schedule(
            MeetupId::fromString('00de6d41-f99d-488e-9753-03932e79d7d0'),
            ScheduledDate::fromString('2016-06-13'),
            Title::fromString('An evening with CQRS')
        );
        $meetup->cancel();

        $this->expectException(\LogicException::class);

        $meetup->reschedule(ScheduledDate::fromString('2016-07-14'));
    }

    /**
     * @test
     */
    public function we_can_retrieve_the_recorded_events()
    {
        $meetup = Meetup::schedule(
            MeetupId::fromString('00de6d41-f99d-488e-9753-03932e79d7d0'),
            ScheduledDate::fromString('2016-06-13'),
            Title::fromString('An evening with CQRS')
        );
        $meetup->reschedule(ScheduledDate::fromString('2016-07-14'));
        $meetup->reschedule(ScheduledDate::fromString('2016-07-15'));
        $meetup->cancel();

        $event1 = $meetup->dequeueEvent();
        $event2 = $meetup->dequeueEvent();
        $event3 = $meetup->dequeueEvent();
        $event4 = $meetup->dequeueEvent();

        self::assertNull($meetup->dequeueEvent());

        self::assertInstanceOf(MeetupScheduled::class, $event1);
        self::assertInstanceOf(MeetupRescheduled::class, $event2);
        self::assertInstanceOf(MeetupRescheduled::class, $event3);
        self::assertInstanceOf(MeetupCancelled::class, $event4);
    }

    /**
     * @test
     */
    public function it_can_be_reconstituted_from_events()
    {
        $meetupId = MeetupId::fromString('00de6d41-f99d-488e-9753-03932e79d7d0');

        $scheduled = new MeetupScheduled(
            $meetupId,
            ScheduledDate::fromString('2016-06-13'),
            Title::fromString('An evening with CQRS')
        );
        $rescheduled = new MeetupRescheduled($meetupId, ScheduledDate::fromString('2016-07-13'));
        $cancelled = new MeetupCancelled($meetupId);

        $meetup = Meetup::reconstitute([$scheduled, $rescheduled, $cancelled]);

        self::assertInstanceOf(Meetup::class, $meetup);
    }
}
