<?php

namespace Tests\AppBundle\Repository;

use AppBundle\Entity\Category;
use AppBundle\Repository\CategoryRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * Class CategoryRepositoryTest
 * @package Tests\AppBundle\Repository
 */
class CategoryRepositoryTest extends KernelTestCase
{
    /** @var CategoryRepository $categoryRepository */
    private $categoryRepository;

    public function setUp()
    {
        $kernel = $this->bootKernel();
        $container = $kernel->getContainer();
        $this->categoryRepository = $container->get('doctrine.orm.entity_manager')->getRepository(Category::class);
    }
}