<?php
declare(strict_types=1);

namespace MeetupManagement\Infrastructure\Persistence;

use MeetupManagement\Domain\ReadModel\UpcomingMeetupForListView;
use MeetupManagement\Domain\ReadModel\UpcomingMeetupForListViewRepository;

final class InMemoryUpcomingMeetupForListViewRepository implements UpcomingMeetupForListViewRepository
{
    private $meetups = [];

    /**
     * @param string $id
     * @return UpcomingMeetupForListView
     * @throws \RuntimeException
     */
    public function byId(string $id): UpcomingMeetupForListView
    {
        if (!isset($this->meetups[$id])) {
            throw new \RuntimeException();
        }

        return $this->meetups[$id];
    }

    /**
     * @param string $id
     * @return void
     */
    public function remove(string $id): void
    {
        unset($this->meetups[$id]);
    }

    /**
     * @param string $id
     * @param UpcomingMeetupForListView $meetup
     * @return void
     */
    public function save(string $id, UpcomingMeetupForListView $meetup)
    {
        $this->meetups[$id] = $meetup;
    }
}
