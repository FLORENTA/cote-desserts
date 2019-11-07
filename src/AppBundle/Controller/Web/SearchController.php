<?php

namespace AppBundle\Controller\Web;

use AppBundle\Form\SearchType;
use AppBundle\Model\SearchModel;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;

/**
 * Class SearchController
 * @package AppBundle\Controller
 */
class SearchController extends Controller
{
    /**
     * @Route("/search/form", name="get_search_form", methods={"GET"})
     * @param RouterInterface $router
     * @return JsonResponse
     */
    public function getSearchForm(RouterInterface $router): JsonResponse
    {
        $searchModel = new SearchModel();
        $form = $this->createForm(SearchType::class, $searchModel, [
            'action' => $router->generate('handle_search')
        ]);

        return new JsonResponse(
            $this->renderView('form/search_form.html.twig', [
                'form' => $form->createView()
            ]),
            JsonResponse::HTTP_OK
        );
    }
}