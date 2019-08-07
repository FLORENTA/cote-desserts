<?php

namespace AppBundle\Manager;

use AppBundle\Entity\Category;
use AppBundle\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Class CategoryManager
 * @package AppBundle\Manager
 */
class CategoryManager
{
    /** @var CategoryRepository $categoryRepository */
    private $categoryRepository;

    /**
     * CategoryManager constructor.
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->categoryRepository = $entityManager->getRepository(Category::class);
    }

    /**
     * @param string $name
     * @return Category|null
     */
    public function getCategoryByName(string $name): ?Category
    {
        return $this->categoryRepository->findOneBy(['category' => $name]);
    }

    /**
     * @return array
     */
    public function getAll(): array
    {
        return $this->categoryRepository->getAll();
    }
}