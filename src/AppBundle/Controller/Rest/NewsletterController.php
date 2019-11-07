<?php

namespace AppBundle\Controller\Rest;

use AppBundle\Entity\Newsletter;
use AppBundle\Form\NewsletterType;
use AppBundle\Manager\NewsletterManager;
use Exception;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\View\View;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Translation\TranslatorInterface;

class NewsletterController extends AbstractFOSRestController
{
    private $newsletterManager;
    private $logger;
    private $translator;

    public function __construct(
        NewsletterManager $newsletterManager,
        LoggerInterface $logger,
        TranslatorInterface $translator
    )
    {
        $this->newsletterManager = $newsletterManager;
        $this->logger = $logger;
        $this->translator = $translator;
    }

    /**
     * @Rest\View(serializerGroups={"newsletter"}, serializerEnableMaxDepthChecks=true)
     * @return Response|View
     */
    public function getNewslettersAction()
    {
        /** @var array $newsletters */
        $subscribers = $this->newsletterManager->getSubscribers();

        if (empty($subscribers)) {
            $this->logger->warning('No subscriber.', ['_method' => __METHOD__]);

            return $this->view($this->translator->trans('query.no_subscriber'),
                JsonResponse::HTTP_BAD_REQUEST
            );
        }

        try {
            return $this->handleView($this->view($subscribers, JsonResponse::HTTP_OK));
        } catch (Exception $exception) {
            $this->logger->error($exception->getMessage(), ['_method' => __METHOD__]);

            return $this->view($this->translator->trans('generic.error'), JsonResponse::HTTP_BAD_REQUEST);
        }
    }

    /**
     * Function to add a new user to the newsletter
     *
     * @param Request $request
     * @return Response|View
     */
    public function createNewsletterAction(Request $request): JsonResponse
    {
        $newsletter = new Newsletter();
        $form = $this->createForm(NewsletterType::class, $newsletter);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var string $email */
            $email = $newsletter->getEmail();

            /** @var Newsletter|null $email */
            $subscriber = $this->newsletterManager->getSubscriberByEmail($email);

            // If not in db, new registration
            if (null === $subscriber) {
                try {
                    // See EntityListener for sending message
                    $this->newsletterManager->createSubscriber($newsletter);

                    return $this->view($this->translator->trans('newsletter.subscription.success'), JsonResponse::HTTP_OK);
                } catch (Exception $exception) {
                    $this->logger->error($exception->getMessage(), [
                        '_method' => __METHOD__
                    ]);

                    return $this->view($this->translator->trans('generic.error'), JsonResponse::HTTP_BAD_REQUEST);
                }
            }

            $this->logger->info(
                sprintf('%s has already subscribed to the newsletter.', $email),
                ['_method' => __METHOD__]
            );

            return $this->view($this->translator->trans('newsletter.subscription.already_registered'), JsonResponse::HTTP_BAD_REQUEST);
        }

        return $this->view($this->translator->trans('generic.form.invalid'), JsonResponse::HTTP_BAD_REQUEST);
    }

    /**
     * No XHR
     * @param string $token
     * @return Response
     */
    public function deleteNewsletterAction($token): Response
    {
        /** @var Newsletter|null $newsletter */
        $subscriber = $this->newsletterManager->getSubscriberByToken($token);

        if (null === $subscriber) {
            $this->logger->warning(sprintf('Unknown subscriber with token %s.', $token), [
                '_method' => __METHOD__
            ]);

            return $this->handleView($this->view(
                $this->translator->trans('newsletter.unsubscription.failure'),
                JsonResponse::HTTP_BAD_REQUEST
            ));
        }

        // See EntityListener for sending message
        $this->newsletterManager->deleteSubscriber($subscriber);

        return $this->handleView($this->view(
            $this->translator->trans('newsletter.unsubscription.success'),
            JsonResponse::HTTP_OK
        ));
    }

    /**
     * No email sent !
     * @Rest\Route("/newsletter/remove/token}/{token}", name="admin_delete_newsletter")
     * @param string $token
     * @return View
     */
    public function adminDeleteNewsletterAction(string $token): View
    {
        /** @var Newsletter|null $newsletter */
        $subscriber = $this->newsletterManager->getSubscriberByToken($token);

        if (null === $subscriber) {
            $this->logger->error(sprintf('Unknown subscriber %s', $token), [
                '_method' => __METHOD__
            ]);

            return $this->view(
                $this->translator->trans('query.no_subscriber'),
                JsonResponse::HTTP_OK
            );
        }

        $this->newsletterManager->deleteSubscriber($subscriber);

        return $this->view(
            $this->translator->trans('newsletter.admin.deletion.success'),
            JsonResponse::HTTP_OK
        );
    }
}