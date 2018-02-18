<?php
/**
 * This file is part of cqrs-and-event-sourcing-workshop.
 *
 * (c) Sascha Schimke <sascha@schimke.me>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Test\Unit\MeetupManagement\Domain\ReadModel;

use MeetupManagement\Domain\Model\MeetupCancelled;
use MeetupManagement\Domain\Model\MeetupId;
use MeetupManagement\Domain\Model\MeetupRescheduled;
use MeetupManagement\Domain\Model\MeetupScheduled;
use MeetupManagement\Domain\Model\ScheduledDate;
use MeetupManagement\Domain\Model\Title;
use MeetupManagement\Domain\ReadModel\UpcomingMeetupForListView;
use MeetupManagement\Domain\ReadModel\UpcomingMeetupForListViewRepository;
use MeetupManagement\Domain\ReadModel\UpcommingMeetupForListProjector;

class UpcommingMeetupForListProjectorTest extends \PHPUnit_Framework_TestCase
{
    /** @var UpcommingMeetupForListProjector */
    private $subject;

    /** @var UpcomingMeetupForListViewRepository|\PHPUnit_Framework_MockObject_MockObject */
    private $repository;

    /**
     * @test
     */
    public function it_should_handle_meetup_scheduling()
    {
        $meetupId = MeetupId::fromString('00de6d41-f99d-488e-9753-03932e79d7d0');
        $scheduledDate = ScheduledDate::fromString('2016-06-13');
        $title = Title::fromString('An evening with CQRS');

        $readModel = new UpcomingMeetupForListView();
        $readModel->scheduledDate = $scheduledDate;
        $readModel->title = $title;

        $this->repository
            ->expects($this->once())
            ->method('save')
            ->with('00de6d41-f99d-488e-9753-03932e79d7d0', $readModel);

        $this->subject->onMeetupScheduled(new MeetupScheduled($meetupId, $scheduledDate, $title));
    }

    /**
     * @test
     */
    public function it_should_handle_meetup_rescheduling()
    {
        $meetupId = MeetupId::fromString('00de6d41-f99d-488e-9753-03932e79d7d0');
        $newDate = ScheduledDate::fromString('2016-06-13');

        $this->repository
            ->expects($this->once())
            ->method('byId')
            ->with('00de6d41-f99d-488e-9753-03932e79d7d0')
            ->willReturn(new UpcomingMeetupForListView());

        $savedReadModel = new UpcomingMeetupForListView();
        $savedReadModel->scheduledDate = $newDate;

        $this->repository
            ->expects($this->once())
            ->method('save')
            ->with('00de6d41-f99d-488e-9753-03932e79d7d0', $savedReadModel);

        $this->subject->onMeetupRescheduled(new MeetupRescheduled($meetupId, $newDate));
    }

    /**
     * @test
     */
    public function it_should_handle_meetup_cancellation()
    {
        $meetupId = MeetupId::fromString('00de6d41-f99d-488e-9753-03932e79d7d0');

        $this->repository->expects($this->once())->method('remove')->with('00de6d41-f99d-488e-9753-03932e79d7d0');

        $this->subject->onMeetupCancelleld(new MeetupCancelled($meetupId));
    }

    protected function setUp()
    {
        $this->repository = self::createMock(UpcomingMeetupForListViewRepository::class);
        $this->subject = new UpcommingMeetupForListProjector($this->repository);
    }
}
