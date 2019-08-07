<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Contact;
use AppBundle\Form\ContactType;
use AppBundle\Manager\ContactManager;
use AppBundle\Service\Serializor;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Psr\Log\LoggerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Class ContactController
 * @package AppBundle\Controller
 */
class ContactController extends Controller
{
    /**
     * @Route("/admin/contacts/fetch", name="fetch_contacts", methods={"GET"})
     * @Method("GET")
     * @param ContactManager $contactManager
     * @param LoggerInterface $logger
     * @param TranslatorInterface $translator
     * @param Serializor $serializor
     * @return JsonResponse
     */
    public function fetchContacts(
        ContactManager $contactManager,
        LoggerInterface $logger,
        TranslatorInterface $translator,
        Serializor $serializor
    ): JsonResponse
    {
        /** @var array $contact */
        $contact = $contactManager->getContacts();

        if (empty($contact)) {
            $logger->info(
                $translator->trans('query.no_contact'),
                ['_method' => __METHOD__]
            );

            return new JsonResponse(
                $translator->trans('query.no_contact'),
                JsonResponse::HTTP_BAD_REQUEST
            );
        }

        try {
            return new JsonResponse(
                $serializor->getSerializer()->normalize($contact, [
                    'groups' => ['contact']
                ]),
                JsonResponse::HTTP_OK
            );
        } catch (Exception $exception) {
            $logger->error($exception->getMessage(), ['_method' => __METHOD__]);

            return new JsonResponse(
                $translator->trans('generic.error'),
                JsonResponse::HTTP_BAD_REQUEST
            );
        }
    }

    /**
     * @Route("/contact/fetch-form", name="fetch_contact_form", methods={"GET"})
     * @param RouterInterface $router
     * @return JsonResponse
     */
    public function fetchContactForm(RouterInterface $router): JsonResponse
    {
        $contact = new Contact();

        $form = $this->createForm(ContactType::class, $contact, [
            'action' => $router->generate('new_contact')
        ]);

        return new JsonResponse(
            $this->renderView('form/contact_form.html.twig', [
                'form' => $form->createView()
            ]),
            JsonResponse::HTTP_OK
        );
    }

    /**
     * @Route("/contact/new", name="new_contact", methods={"POST"})
     * @param Request $request
     * @param LoggerInterface $logger
     * @param TranslatorInterface $translator
     * @param EntityManagerInterface $entityManager
     * @return JsonResponse
     */
    public function newContact(
        Request $request,
        LoggerInterface $logger,
        TranslatorInterface $translator,
        EntityManagerInterface $entityManager
    ): JsonResponse
    {
        $contact = new Contact();
        $form = $this->createForm(ContactType::class, $contact);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $contact->setDate(new DateTime());
                $contact->setToken(md5(uniqid()));
                $entityManager->persist($contact);
                $entityManager->flush();
            } catch (Exception $exception) {
                $logger->error($exception->getMessage(), [
                    '_method' => __METHOD__
                ]);

                return new JsonResponse(
                    $translator->trans('contact.sent.failure'),
                    JsonResponse::HTTP_BAD_REQUEST
                );
            }

            return new JsonResponse(
                $translator->trans('contact.sent.success'),
                JsonResponse::HTTP_CREATED
            );
        }

        $logger->error(
            'The form received is not valid',
            ['_method' => __METHOD__]
        );

        return new JsonResponse(
            $translator->trans('generic.form.invalid'),
            JsonResponse::HTTP_BAD_REQUEST
        );
    }

    /**
     * @Route("/admin/contact/delete/{token}", name="delete_contact", methods={"DELETE"})
     * @param ContactManager $contactManager
     * @param EntityManagerInterface $entityManager
     * @param TranslatorInterface $translator
     * @param LoggerInterface $logger
     * @param string $token
     * @return JsonResponse
     */
    public function deleteContact(
        ContactManager $contactManager,
        EntityManagerInterface $entityManager,
        TranslatorInterface $translator,
        LoggerInterface $logger,
        $token
    ): JsonResponse
    {
        /** @var Contact|null $contact */
        $contact = $contactManager->getContactByToken($token);

        if (null === $contact) {
            $logger->error(sprintf('Unknown contact for token %s', $token), [
                '_method' => __METHOD__
            ]);

            return new JsonResponse(
                $translator->trans('contact.not_found'),
                JsonResponse::HTTP_BAD_REQUEST
            );
        }

        $entityManager->remove($contact);
        $entityManager->flush();

        $logger->info(
            $translator->trans('contact.deletion.success.logger', [
                '%email%' => $contact->getEmail(),
                '%token%' => $token
            ]), [
            '_method' => __METHOD__
        ]);

        return new JsonResponse(
            $translator->trans('contact.deletion.success'),
            JsonResponse::HTTP_OK
        );
    }
}