<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Category;
use AppBundle\Form\CategoryType;
use AppBundle\Manager\CategoryManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;

/**
 * Class CategoryController
 * @package AppBundle\Controller
 */
class CategoryController extends Controller
{
    /**
     * @Route("/category/fetch-form", name="fetch_category_form", methods={"GET"})
     * @param RouterInterface $router
     * @return JsonResponse
     */
    public function fetchCategoryForm(RouterInterface $router): JsonResponse
    {
        $category = new Category();

        $form = $this->createForm(CategoryType::class, $category, [
            'action' => $router->generate('fetch_articles_by_category')
        ]);

        return new JsonResponse($this->renderView('form/category_form.html.twig', [
            'form' => $form->createView()
        ]), JsonResponse::HTTP_OK);
    }

    /**
     * @Route("/category/fetch", name="fetch_categories", methods={"GET"})
     * @param CategoryManager $categoryManager
     * @return JsonResponse
     */
    public function fetchCategories(CategoryManager $categoryManager): JsonResponse
    {
        return new JsonResponse(
            array_map(function($category) {
                return $category['category'];
            }, $categoryManager->getAll()
        ), JsonResponse::HTTP_OK);
    }
}