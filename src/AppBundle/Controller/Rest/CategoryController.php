<?php

namespace AppBundle\Controller\Rest;

use AppBundle\Manager\CategoryManager;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class CategoryController extends AbstractFOSRestController
{
    /** @var CategoryManager $categoryManager */
    private $categoryManager;

    /**
     * CategoryController constructor.
     * @param CategoryManager $categoryManager
     */
    public function __construct(CategoryManager $categoryManager)
    {
        $this->categoryManager = $categoryManager;
    }

    /**
     * @return Response
     */
    public function getCategoriesAction(): Response
    {
        return $this->handleView($this->view(array_map(function($category) {
            return $category['category'];
        }, $this->categoryManager->getAll()), JsonResponse::HTTP_OK));
    }
}