<?php

namespace AppBundle\Event;

use Symfony\Component\EventDispatcher\Event;

/**
 * Class StatisticEvent
 * @package AppBundle\Event
 */
final class StatisticEvent extends Event
{
    const APP_BUNDLE_STATISTICS_NEW = 'app_bundle.statistics.new';

    /** @var string|null $data */
    private $data;

    /** @var string|null $type */
    private $type;

    /** @var int $status */
    private $status;

    /**
     * StatisticEvent constructor.
     * @param string|null $data
     * @param string $type
     */
    public function __construct(string $data = null, string $type= null)
    {
        $this->data = $data;
        $this->type = $type;
    }

    /**
     * @return string|null
     */
    public function getData(): ?string
    {
        return $this->data;
    }

    /**
     * @return string|null
     */
    public function getType(): ?string
    {
        return $this->type;
    }

    /**
     * @param int $status
     * @return StatisticEvent
     */
    public function setStatus(int $status): StatisticEvent
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @return int
     */
    public function getStatus(): int
    {
        return $this->status;
    }
}