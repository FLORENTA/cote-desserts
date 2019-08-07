<?php

namespace AppBundle\Entity;

use DateTime;

/**
 * Statistic
 */
class Statistic
{
    const NAVIGATION_TYPE = 'navigation';
    const SEARCH_TYPE = 'search';
    const NEWSLETTER_TYPE = 'newsletter';

    /**
     * @var int
     */
    private $id;

    /**
     * @var DateTime
     */
    private $date;

    /**
     * @var string
     */
    private $url;

    /**
     * @var bool
     */
    private $bot;

    /**
     * @var string $sessionId
     */
    private $sessionId;

    /**
     * @var string
     */
    private $data;

    /**
     * @var StatisticType|null $statisticType
     */
    private $statisticType;

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set date
     *
     * @param DateTime $date
     *
     * @return Statistic
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date
     *
     * @return DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set url
     *
     * @param string $url
     *
     * @return Statistic
     */
    public function setUrl($url)
    {
        $this->url = $url;

        return $this;
    }

    /**
     * Get url
     *
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Set bot
     *
     * @param boolean $bot
     *
     * @return Statistic
     */
    public function setBot($bot)
    {
        $this->bot = $bot;

        return $this;
    }

    /**
     * Get bot
     *
     * @return bool
     */
    public function getBot()
    {
        return $this->bot;
    }

    /**
     * Set data
     *
     * @param string $data
     *
     * @return Statistic
     */
    public function setData($data)
    {
        $this->data = $data;

        return $this;
    }

    /**
     * Get data
     *
     * @return string
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param string $sessionId
     *
     * @return $this
     */
    public function setSessionId(string $sessionId)
    {
        $this->sessionId = $sessionId;

        return $this;
    }

    /**
     * @return string
     */
    public function getSessionId(): string
    {
        return $this->sessionId;
    }

    /**
     * Set statisticType.
     *
     * @param StatisticType|null $statisticType
     *
     * @return Statistic
     */
    public function setStatisticType(StatisticType $statisticType = null)
    {
        $this->statisticType = $statisticType;

        return $this;
    }

    /**
     * Get statisticType.
     *
     * @return StatisticType|null
     */
    public function getStatisticType(): ?StatisticType
    {
        return $this->statisticType;
    }
}
