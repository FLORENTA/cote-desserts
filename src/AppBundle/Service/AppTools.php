<?php

namespace AppBundle\Service;

use AppBundle\Manager\ArticleManager;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Container\ContainerInterface;

/**
 * Class AppTools
 * @package AppBundle\Service
 */
class AppTools
{
    private $container;

    /** @var EntityManagerInterface $em */
    private $em;

    /** @var ArticleManager $articleManager */
    private $articleManager;

    /**
     * AppTools constructor.
     * @param EntityManagerInterface $entityManager
     * @param ArticleManager $articleManager
     */
    public function __construct(
        ContainerInterface $container
    )
    {
        $this->container = $container;
    }

    /**
     * Function to transform the article title for the url
     * @param string $slug
     * @return string
     */
    public function slugify(string $slug): string
    {
        $slug = htmlspecialchars(trim($slug));
        $slug = str_replace(["à", "â", "ä"], 'a', $slug);
        $slug = str_replace(["ö", "ô"], 'o', $slug);
        $slug = str_replace(["é", "è", "ê", "ë"], 'e', $slug);
        $slug = str_replace(["û", "ü", "ù"], 'u', $slug);
        $slug = str_replace(["ï", "î"], 'i', $slug);
        $slug = preg_replace('/\s|\'|,/', '-', $slug);

        return $this->checkForSimilarSlug(strtolower($slug));
    }

    /**
     * Function to check whether the slug already exists
     * If so, let's increment a number at the end of the slug
     *
     * @param string $slug
     * @return string
     */
    public function checkForSimilarSlug($slug): string
    {
        $val = 0;

        /** @var array $slugs */
        $slugs = $this->articleManager->getNumberOfArticlesBySlug($slug);

        if (isset($slugs['count_same_slug'])) {
            $val = intval($slugs['count_same_slug']);
        }

        if ($val) {
            $val++;
            return $slug . '-' . $val;
        }

        return $slug;
    }
}