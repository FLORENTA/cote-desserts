<?php

namespace AppBundle\Controller\Web;

use AppBundle\Entity\Category;
use AppBundle\Form\CategoryType;
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
     * @Route("/categories/form", name="get_categories_form", methods={"GET"})
     * @param RouterInterface $router
     * @return JsonResponse
     */
    public function getCategoryForm(RouterInterface $router): JsonResponse
    {
        $category = new Category();

        $form = $this->createForm(CategoryType::class, $category, [
            'action' => $router->generate('get_articles_by_categories')
        ]);

        return new JsonResponse($this->renderView('form/category_form.html.twig', [
            'form' => $form->createView()
        ]), JsonResponse::HTTP_OK);
    }
}