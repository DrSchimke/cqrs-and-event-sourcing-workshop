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

class MeetupCancelled implements MeetupEvent
{
    /** @var MeetupId */
    private $meetupId;

    public function __construct(MeetupId $meetupId)
    {
        $this->meetupId = $meetupId;
    }

    public function getMeetupId(): MeetupId
    {
        return $this->meetupId;
    }
}
