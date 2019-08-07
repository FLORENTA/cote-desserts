<?php

namespace AppBundle\EventListener;

use AppBundle\Entity\StatisticType;
use AppBundle\Event\StatisticEvent;
use AppBundle\Manager\StatisticManager;

/**
 * Class StatisticListener
 * @package AppBundle\EventListener
 */
class StatisticListener
{
    /** @var StatisticManager $statisticManager */
    private $statisticManager;

    /**
     * StatisticListener constructor.
     * @param StatisticManager $statisticManager
     */
    public function __construct(StatisticManager $statisticManager)
    {
        $this->statisticManager = $statisticManager;
    }

    /**
     * @param StatisticEvent $statisticEvent
     */
    public function register(StatisticEvent $statisticEvent): void
    {
        /** @var string $data */
        $data = $statisticEvent->getData();

        /** @var StatisticType $statisticType */
        $type = $statisticEvent->getType();

        /** @var int $status */
        $status = $this->statisticManager->registerData($data, $type);

        $statisticEvent->setStatus($status);
    }
}