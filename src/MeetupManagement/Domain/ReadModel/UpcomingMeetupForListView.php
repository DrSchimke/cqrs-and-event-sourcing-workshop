<?php
declare(strict_types=1);

namespace MeetupManagement\Domain\ReadModel;

use MeetupManagement\Domain\Model\ScheduledDate;
use MeetupManagement\Domain\Model\Title;

final class UpcomingMeetupForListView
{
    /** @var Title */
    public $title;

    /** @var ScheduledDate */
    public $scheduledDate;
}
