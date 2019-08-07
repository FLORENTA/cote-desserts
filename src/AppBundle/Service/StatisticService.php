<?php

namespace AppBundle\Service;

use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Class StatisticService
 * @package AppBundle\Service
 */
class StatisticService
{
    /** @var Request $request */
    private $request;

    /** @var LoggerInterface $logger */
    private $logger;

    /** @var array $data */
    private $data = [];

    /**
     * StatisticService constructor.
     * @param RequestStack $request
     * @param LoggerInterface $logger
     */
    public function __construct(
        RequestStack $request,
        LoggerInterface $logger
    )
    {
        $this->data['total'] = 0;
        $this->request = $request->getCurrentRequest();
        $this->logger = $logger;
    }

    /**
     * @param array $statistics
     */
    public function processAdminStatistics($statistics): void
    {
        array_walk($statistics, [$this, "treat"]);
    }

    /**
     * @param $stats
     * @param array $nbOfVisitors
     * @param string|null $nbOfVisitedPages
     * @param string|int $nbOfArticles
     */
    public function processUserStatistics(
        &$stats,
        $nbOfVisitors,
        $nbOfVisitedPages,
        $nbOfArticles
    ): void
    {
        $stats['nbOfUniqueVisitors'] = $nbOfVisitors;
        $stats['nbOfVisitedPages'] = $nbOfVisitedPages;
        $stats['nbOfArticles'] = $nbOfArticles;
    }

    /**
     * @return array
     */
    public function gets(): array
    {
        return $this->data;
    }

    /**
     * @param array $value
     */
    private function treat($value): void
    {
        if (!empty($data = $value['data']) && !empty($number = $value['number'])) {
           $this->data['data'][$data] = $number;
           $this->data['total'] += (int) $number;
        }
    }

    /**
     * @return bool
     */
    public function checkIfBot(): bool
    {
        $array = explode(" ", $this->request->server->get("HTTP_USER_AGENT"));

        foreach ($array as $value) {
            if (preg_match("/YandexBot|YandexImages|GigablastOpenSource|Mail.RU_Bot|Pinterestbot|MJ12bot|ScoutJet|Snarfer|SimplePie|sogou|BLEXBot|Sitebot|zspider|Charlotte|Gigabot|msnbot|okhttp|ips-agent|SiteLockSpider|MSRbot|mxbot|Googlebot|bingbot|AdsBot-Google-Mobile|Googlebot-Mobile|Slurp|DuckDuckBot|Baiduspider|facebookexternalhit|Exabot|Konqueror|Trident|facebot|ia_archiver/", $value)) {
                return true;
            }
        }

        return false;
    }
}