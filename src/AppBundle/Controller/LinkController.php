<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Link;
use AppBundle\Form\LinkType;
use AppBundle\Manager\LinkManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Class LinkController
 * @package AppBundle\Controller
 */
class LinkController extends Controller
{
    /**
     * @Route("/admin/link/fetch-form", name="fetch_link_form", methods={"GET"})
     * @return JsonResponse
     */
    public function fetchForm(): JsonResponse
    {
        $link = new Link();
        $form = $this->createForm(LinkType::class, $link);

        return new JsonResponse($this->renderView('form/link_form.html.twig', [
            'form' => $form->createView()
        ]));
    }

    /**
     * @Route("/admin/link/new", name="new_link", methods={"POST"})
     * @param Request $request
     * @param LinkManager $linkManager
     * @param TranslatorInterface $translator
     * @param RouterInterface $router
     * @return JsonResponse
     */
    public function createLink(
        Request $request,
        LinkManager $linkManager,
        TranslatorInterface $translator,
        RouterInterface $router
    )
    {
        $link = new Link();
        $form = $this->createForm(LinkType::class, $link, [
            'action' => $router->generate('new_link')
        ]);

        $form->handleRequest($request);

        $linkManager->createLink($link);

        return new JsonResponse($translator->trans('link.creation.success'), JsonResponse::HTTP_OK);
    }

    /**
     * @Route("/admin/link/update", name="update_link", methods={"PUT"})
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @param TranslatorInterface $translator
     * @param Link $link
     * @return JsonResponse
     */
    public function updateLink(
        Request $request,
        EntityManagerInterface $entityManager,
        TranslatorInterface $translator,
        Link $link
    ): JsonResponse
    {
        $form = $this->createForm(LinkType::class, $link);

        $form->handleRequest($request);

        $entityManager->flush();

        return new JsonResponse($translator->trans('link.update.success'), JsonResponse::HTTP_OK);
    }

    /**
     * @Route("/admin/link/delete", name="delete_link", methods={"DELETE"})
     * @param LinkManager $linkManager
     * @param TranslatorInterface $translator
     * @param Link $link
     * @return JsonResponse
     */
    public function deleteLink(
        LinkManager $linkManager,
        TranslatorInterface $translator,
        Link $link
    ): JsonResponse
    {
        $linkManager->deleteLink($link);

        return new JsonResponse($translator->trans('link.deletion.success'), JsonResponse::HTTP_OK);
    }
}