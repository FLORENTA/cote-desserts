<?php

namespace AppBundle\Model;

use AppBundle\Entity\StatisticType;
use DateTime;

/**
 * Class StatisticModel
 * @package AppBundle\Model
 */
class StatisticModel
{
    /** @var Datetime|null $startTime */
    private $startTime;

    /** @var DateTime|null $endTime */
    private $endTime;

    /** @var bool|null $bot */
    private $bot;

    /** @var StatisticType|null $statisticType */
    private $statisticType;

    /**
     * @return DateTime|null
     */
    public function getStartTime(): ?DateTime
    {
        return $this->startTime;
    }

    /**
     * @param DateTime|null $startTime
     * @return StatisticModel
     */
    public function setStartTime(?DateTime $startTime): StatisticModel
    {
        $this->startTime = $startTime;

        return $this;
    }

    /**
     * @return DateTime|null
     */
    public function getEndTime(): ?DateTime
    {
        return $this->endTime;
    }

    /**
     * @param DateTime|null $endTime
     * @return StatisticModel
     */
    public function setEndTime(?DateTime $endTime): StatisticModel
    {
        $this->endTime = $endTime;

        return $this;
    }

    /**
     * @return bool|null
     */
    public function isBot(): ?bool
    {
        return $this->bot;
    }

    /**
     * @param bool|null $bot
     * @return StatisticModel
     */
    public function setBot(?bool $bot): StatisticModel
    {
        $this->bot = $bot;

        return $this;
    }

    /**
     * @return StatisticType|null
     */
    public function getStatisticType(): ?StatisticType
    {
        return $this->statisticType;
    }

    /**
     * @param StatisticType|null $statisticType
     * @return StatisticModel
     */
    public function setStatisticType(?StatisticType $statisticType): ?StatisticModel
    {
        $this->statisticType = $statisticType;

        return $this;
    }
}