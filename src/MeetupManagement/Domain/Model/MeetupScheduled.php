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


class MeetupScheduled
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

    public function __construct(MeetupId $meetupId, ScheduledDate $scheduledDate, Title $title)
    {
        $this->meetupId = $meetupId;
        $this->scheduledDate = $scheduledDate;
        $this->title = $title;
    }

    /**
     * @return MeetupId
     */
    public function getMeetupId()
    {
        return $this->meetupId;
    }

    /**
     * @return ScheduledDate
     */
    public function getScheduledDate()
    {
        return $this->scheduledDate;
    }

    /**
     * @return Title
     */
    public function getTitle()
    {
        return $this->title;
    }
}
