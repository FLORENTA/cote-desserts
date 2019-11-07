<?php

namespace AppBundle\Controller\Web;

use AppBundle\Entity\Article;
use AppBundle\Entity\Link;
use AppBundle\Form\LinkType;
use AppBundle\Manager\ArticleManager;
use AppBundle\Manager\LinkManager;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Context\Context;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\View\View;
use FOS\RestBundle\View\ViewHandlerInterface;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Class LinkController
 * @package AppBundle\Controller
 */
class LinkController extends AbstractFOSRestController
{
    private $articleManager;

    public function __construct(ArticleManager $articleManager)
    {
        $this->articleManager = $articleManager;
    }
//    /**
//     * @Route("/admin/link/fetch-form", name="fetch_link_form", methods={"GET"})
//     * @return JsonResponse
//     */
//    public function fetchForm(): JsonResponse
//    {
//        $link = new Link();
//        $form = $this->createForm(LinkType::class, $link);
//
//        return new JsonResponse($this->renderView('form/link_form.html.twig', [
//            'form' => $form->createView()
//        ]));
//    }

//

    /**
     * @Rest\Get("/articles/{id}")
     * @Rest\View(serializerGroups={"article"}, serializerEnableMaxDepthChecks=true)
     * @param Article $id
     * @return Response
     */
    public function getLinkAction(Article $id): Response
    {
        $view = View::create($id);
        return $this->handleView($view);
    }

    public function deleteLinkAction($token)
    {

    }
}