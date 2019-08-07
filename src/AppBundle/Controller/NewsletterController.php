<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Newsletter;
use AppBundle\Entity\Statistic;
use AppBundle\Event\StatisticEvent;
use AppBundle\Form\NewsletterType;
use AppBundle\Manager\NewsletterManager;
use AppBundle\Manager\StatisticManager;
use AppBundle\Service\Serializor;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Translation\TranslatorInterface;
use Exception;

/**
 * Class NewsletterController
 * @package AppBundle\Controller
 */
class NewsletterController extends Controller
{
    /**
     * @Route("/admin/newsletters", name="admin_newsletters", methods={"GET"})
     * @param NewsletterManager $newsletterManager
     * @param Serializor $serializor
     * @param LoggerInterface $logger
     * @param TranslatorInterface $translator
     * @return JsonResponse
     */
    public function fetchNewsletters(
        NewsletterManager $newsletterManager,
        Serializor $serializor,
        LoggerInterface $logger,
        TranslatorInterface $translator
    ): JsonResponse
    {
        /** @var array $newsletters */
        $subscribers = $newsletterManager->getSubscribers();

        if (empty($subscribers)) {
            $logger->warning('No subscriber.', ['_method' => __METHOD__]);

            return new JsonResponse(
                $translator->trans('query.no_subscriber'),
                JsonResponse::HTTP_BAD_REQUEST
            );
        }

        try {
            return new JsonResponse(
                $serializor->getSerializer()->normalize($subscribers, [
                    'groups' => ['newsletter']
                ]), JsonResponse::HTTP_OK);
        } catch (Exception $exception) {
            $logger->error($exception->getMessage(), ['_method' => __METHOD__]);

            return new JsonResponse(
                $translator->trans('generic.error'),
                JsonResponse::HTTP_BAD_REQUEST
            );
        }
    }

    /**
     * @Route("/newsletter/fetch-form", name="fetch_newsletter_form", methods={"GET"})
     * @return JsonResponse
     */
    public function newsletterForm(): JsonResponse
    {
        $newsletter = new Newsletter();
        $form = $this->createForm(NewsletterType::class, $newsletter);

        return new JsonResponse(
            $this->renderView('form/newsletter_form.html.twig', [
                'form' => $form->createView()
            ]),
            JsonResponse::HTTP_OK
        );
    }

    /**
     * Function to add a new user to the newsletter
     *
     * @Route("/newsletter/new", name="newsletter_new", methods={"POST"})
     * @param NewsletterManager $newsletterManager
     * @param TranslatorInterface $translator
     * @param Request $request
     * @param LoggerInterface $logger
     * @return JsonResponse
     */
    public function newSubscriber(
        NewsletterManager $newsletterManager,
        TranslatorInterface $translator,
        Request $request,
        LoggerInterface $logger
    ): JsonResponse
    {
        $newsletter = new Newsletter();
        $form = $this->createForm(NewsletterType::class, $newsletter);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var string $email */
            $email = $newsletter->getEmail();

            /** @var Newsletter|null $email */
            $subscriber = $newsletterManager->getSubscriberByEmail($email);

            // If not in db, new registration
            if (null === $subscriber) {
                try {
                    // See EntityListener for sending message
                    $newsletterManager->createSubscriber($newsletter);

                    return new JsonResponse(
                        $translator->trans('newsletter.subscription.success'),
                        JsonResponse::HTTP_OK
                    );
                } catch (Exception $exception) {
                    $logger->error($exception->getMessage(), [
                        '_method' => __METHOD__
                    ]);

                    return new JsonResponse(
                        $translator->trans('generic.error'),
                        JsonResponse::HTTP_BAD_REQUEST
                    );
                }
            }

            $logger->info(
                sprintf('%s has already subscribed to the newsletter.', $email),
                ['_method' => __METHOD__]
            );

            return new JsonResponse(
                $translator->trans('newsletter.subscription.already_registered'),
                JsonResponse::HTTP_BAD_REQUEST
            );
        }

        return new JsonResponse(
            $translator->trans('generic.form.invalid'),
            JsonResponse::HTTP_BAD_REQUEST
        );
    }

    /**
     * No XHR
     * @Route("/unsubscribe/{token}", name="user_unsubscribe")
     * @param NewsletterManager $newsletterManager
     * @param TranslatorInterface $translator
     * @param LoggerInterface $logger
     * @param string $token
     * @return Response
     */
    public function userUnsubscribe(
        NewsletterManager $newsletterManager,
        TranslatorInterface $translator,
        LoggerInterface $logger,
        $token
    ): Response
    {
        /** @var Newsletter|null $newsletter */
        $subscriber = $newsletterManager->getSubscriberByToken($token);

        if (null === $subscriber) {
            $logger->warning(sprintf('Unknown subscriber with token %s.', $token), [
                '_method' => __METHOD__
            ]);

            return new Response(
                $translator->trans('newsletter.unsubscription.failure'),
                JsonResponse::HTTP_BAD_REQUEST
            );
        }

        // See EntityListener for sending message
        $newsletterManager->deleteSubscriber($subscriber);

        return new Response(
            $translator->trans('newsletter.unsubscription.success'),
            JsonResponse::HTTP_OK
        );
    }

    /**
     * No email sent !
     * @Route("/newsletter/remove/token}/{token}", name="admin_unsubscribe")
     * @param NewsletterManager $newsletterManager
     * @param LoggerInterface $logger
     * @param TranslatorInterface $translator
     * @param string $token
     * @return JsonResponse|Response
     */
    public function adminUnsubscribe(
        NewsletterManager $newsletterManager,
        LoggerInterface $logger,
        TranslatorInterface $translator,
        string $token
    ): Response
    {
        /** @var Newsletter|null $newsletter */
        $subscriber = $newsletterManager->getSubscriberByToken($token);

        if (null === $subscriber) {
            $logger->error(sprintf('Unknown subscriber %s', $token), [
                '_method' => __METHOD__
            ]);

            return new JsonResponse(
                $translator->trans('query.no_subscriber'),
                JsonResponse::HTTP_OK
            );
        }

        $newsletterManager->deleteSubscriber($subscriber);

        return new JsonResponse(
            $translator->trans('newsletter.admin.deletion.success'),
            JsonResponse::HTTP_OK
        );
    }

    /**
     * @Route("/pullIn/article/{slug}", name="pull_in")
     * @param EventDispatcherInterface $eventDispatcher
     * @param Request $request
     * @param LoggerInterface $logger
     * @param string $slug
     * @return RedirectResponse
     */
    public function pullIn(
        EventDispatcherInterface $eventDispatcher,
        Request $request,
        LoggerInterface $logger,
        $slug
    ): RedirectResponse
    {
        $url = $_SERVER['HTTP_HOST'] . "/#/" . "article/$slug";

        if (!preg_match("/^(http|https):\/\//", $url)) {
            $url = 'http://' . $url;
        }

        /** @var bool|string $data */
        $data = substr($request->getRequestUri(), 7);

        if (!empty($data)) {
            $statisticEvent = new StatisticEvent($data, Statistic::NEWSLETTER_TYPE);

            try {
                $eventDispatcher->dispatch(
                    StatisticEvent::APP_BUNDLE_STATISTICS_NEW,
                    $statisticEvent
                );

                /** @var int $status */
                $status = $statisticEvent->getStatus();

                if ($status === StatisticManager::ERROR) {
                    $logger->error('An error occurred when trying to register data', [
                        '_method' => __METHOD__,
                        '_status' => $status
                    ]);
                }

            } catch (Exception $exception) {
                $logger->error($exception->getMessage(), [
                    '_method' => __METHOD__
                ]);
            }
        }

        return new RedirectResponse($url);
    }
}