<?php

namespace AppBundle\Controller\Rest;

use AppBundle\Entity\Contact;
use AppBundle\Form\ContactType;
use AppBundle\Manager\ContactManager;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\View\View;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Class ContactController
 * @package AppBundle\Controller\Rest
 */
class ContactController extends AbstractFOSRestController
{
    /** @var EntityManagerInterface $em */
    private $em;

    /** @var ContactManager $contactManager */
    private $contactManager;

    /** @var TranslatorInterface $translator */
    private $translator;

    /** @var LoggerInterface $logger */
    private $logger;

    /**
     * ContactController constructor.
     * @param EntityManagerInterface $entityManager
     * @param ContactManager $contactManager
     * @param TranslatorInterface $translator
     * @param LoggerInterface $logger
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        ContactManager $contactManager,
        TranslatorInterface $translator,
        LoggerInterface $logger
    )
    {
        $this->em = $entityManager;
        $this->contactManager = $contactManager;
        $this->translator = $translator;
        $this->logger = $logger;
    }

    /**
     * @Rest\View(serializerGroups={"contact"}, serializerEnableMaxDepthChecks=true)
     * @return View|Response
     */
    public function getContactsAction()
    {
        /** @var array $contacts */
        $contacts = $this->contactManager->getContacts();

        if (empty($contact)) {
            $this->logger->info('No message received.', ['_method' => __METHOD__]);

            return $this->view(
                $this->translator->trans('query.no_contact'),
                JsonResponse::HTTP_BAD_REQUEST
            );
        }

        try {
            return $this->handleView($this->view($contacts, JsonResponse::HTTP_OK));
        } catch (Exception $exception) {
            $this->logger->error($exception->getMessage(), ['_method' => __METHOD__]);

            return $this->view($this->translator->trans('generic.error'), JsonResponse::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @param Request $request
     * @return View
     */
    public function createContactAction(Request $request): View
    {
        $contact = new Contact();
        $form = $this->createForm(ContactType::class, $contact);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $contact->setDate(new DateTime());
                $contact->setToken(md5(uniqid()));
                $this->contactManager->createContact($contact);
            } catch (Exception $exception) {
                $this->logger->error($exception->getMessage(), [
                    '_method' => __METHOD__
                ]);

                return $this->view(
                    $this->translator->trans('contact.sent.failure'),
                    JsonResponse::HTTP_BAD_REQUEST
                );
            }

            return $this->view(
                $this->translator->trans('contact.sent.success'),
                JsonResponse::HTTP_CREATED
            );
        }

        $this->logger->error('The form is not valid', ['_method' => __METHOD__]);

        return $this->view(
            $this->translator->trans('generic.form.invalid'),
            JsonResponse::HTTP_BAD_REQUEST
        );
    }

    /**
     * @param string $token
     * @return View
     */
    public function deleteContactAction(string $token): View
    {
        /** @var Contact|null $contact */
        $contact = $this->contactManager->getContactByToken($token);

        if (null === $contact) {
            $this->logger->error(sprintf('Unknown contact for token %s', $token), [
                '_method' => __METHOD__
            ]);

            return $this->view($this->translator->trans('contact.not_found'), JsonResponse::HTTP_BAD_REQUEST);
        }

        $this->contactManager->removeContact($contact);

        $this->logger->info(
            $this->translator->trans('contact.deletion.success.logger', [
                '%email%' => $contact->getEmail(),
                '%token%' => $token
            ]), [
            '_method' => __METHOD__
        ]);

        return $this->view($this->translator->trans('contact.deletion.success'), JsonResponse::HTTP_OK);
    }
}