<?php

namespace AppBundle\EventListener;

use AppBundle\Entity\Article;
use AppBundle\Entity\Comment;
use AppBundle\Entity\Contact;
use AppBundle\Entity\Image;
use AppBundle\Entity\Newsletter;
use AppBundle\Event\CommentEvent;
use AppBundle\Event\ContactEvent;
use AppBundle\Event\NewsletterEvent;
use AppBundle\Service\AppTools;
use AppBundle\Service\CommentService;
use AppBundle\Service\ContactService;
use AppBundle\Service\FileService;
use AppBundle\Service\NewsletterService;
use DateTime;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\OnFlushEventArgs;
use Psr\Log\LoggerInterface;
use Exception;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Doctrine\ORM\Event\PreUpdateEventArgs;

/**
 * Class EntityListener
 * @package AppBundle\EventListener
 */
class EntityListener
{
    /** @var AppTools $appTools */
    private $appTools;

    /** @var FileService $fileService */
    private $fileService;

    /** @var LoggerInterface $logger */
    private $logger;

    /** @var EventDispatcherInterface $eventDispatcher */
    private $eventDispatcher;

    /**
     * EntityListener constructor.
     * @param AppTools $appTools
     * @param FileService $fileService
     * @param LoggerInterface $logger
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function __construct(
        AppTools $appTools,
        FileService $fileService,
        LoggerInterface $logger,
        EventDispatcherInterface $eventDispatcher
    )
    {
        $this->appTools = $appTools;
        $this->fileService = $fileService;
        $this->logger = $logger;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @param LifecycleEventArgs $lifecycleEventArgs
     */
    public function prePersist(LifecycleEventArgs $lifecycleEventArgs): void
    {
        /** @var object $entity */
        $entity = $lifecycleEventArgs->getEntity();

        if (!$entity instanceof Article && !$entity instanceof Image) {
            return;
        }

        if ($entity instanceof Article) {
            /** @var string $slug */
            $slug = $this->appTools->slugify($entity->getTitle());

            /** @var UploadedFile|null $file */
            $file = $entity->getFile();

            if ($file instanceof UploadedFile) {
                /** @var string $filename */
                $filename = $this->fileService->uploadFile($file);
                $entity->setPdf($filename);
            }

            try {
                $entity->setDate(new DateTime());
            } catch (Exception $exception) {
                $this->logger->error($exception->getMessage(), [
                    '_method' => __METHOD__
                ]);
            }

            $entity->setToken(md5(uniqid()))->setSlug($slug);
        }

        if ($entity instanceof Image) {
            try {
                /** @var UploadedFile|null $file */
                $file = $entity->getFile();

                if (null !== $file) {
                    /** @var string $filename */
                    $filename = $this->uploadFile($file);
                    $entity->setSrc($filename);
                }

                $entity->setTitle($entity->getArticle()->getTitle());
            } catch (Exception $exception) {
                $this->logger->error($exception->getMessage(), [
                    '_method' => __METHOD__
                ]);
            }
        }
    }

    /**
     * @param OnFlushEventArgs $onFlushEventArgs
     */
    public function onFlush(OnFlushEventArgs $onFlushEventArgs): void
    {
        $unitOfWork = $onFlushEventArgs->getEntityManager()->getUnitOfWork();

        foreach ($unitOfWork->getScheduledEntityInsertions() as $entity) {
            if ($entity instanceof Newsletter) {
                /** @var string $email */
                $email = $entity->getEmail();

                /** @var string $token */
                $token = $entity->getToken();

                $newsletterEvent = new NewsletterEvent(null, $email, $token);

                $this->eventDispatcher->dispatch(
                    NewsletterEvent::APP_BUNDLE_NEWSLETTER_CONFIRM_SUBSCRIPTION,
                    $newsletterEvent
                );

                if ($newsletterEvent->getStatus() === NewsletterService::ERROR) {
                    $this->logger->error(
                        sprintf('An error occurred when trying to send subscription confirmation to %s', $email), [
                        '_method' => __METHOD__
                    ]);
                }
            }

            if ($entity instanceof Comment) {
                $commentEvent = new CommentEvent($entity);
                $this->eventDispatcher->dispatch(CommentEvent::APP_BUNDLE_NEW_COMMENT, $commentEvent);

                if ($commentEvent->getStatus() === CommentService::ERROR) {
                    $this->logger->error(
                        sprintf('An error occurred when trying to send unsubscription confirmation to %s.', $email), [
                        '_method' => __METHOD__
                    ]);
                }
            }

            if ($entity instanceof Contact) {
                $contactEvent = new ContactEvent($entity);
                $this->eventDispatcher->dispatch(ContactEvent::APP_BUNDLE_NEW_CONTACT, $contactEvent);

                if ($contactEvent->getStatus() === ContactService::ERROR) {
                    $this->logger->error(
                        sprintf('An error occurred when trying to send unsubscription confirmation to %s.', $email), [
                        '_method' => __METHOD__
                    ]);
                }
            }
        }
    }

    /**
     * @param PreUpdateEventArgs $preUpdateEventArgs
     */
    public function preUpdate(PreUpdateEventArgs $preUpdateEventArgs)
    {
        /** @var object $entity */
        $entity = $preUpdateEventArgs->getEntity();

        if ($entity instanceof Article) {
            /** @var UploadedFile|null $pdf */
            $file = $entity->getFile();

            if ($file instanceof UploadedFile) {
                /** @var string $filename */
                $filename = $this->uploadFile($file);
                $this->removeFile($entity->getPdf());
                $entity->setPdf($filename);
            }
        }

        if ($entity instanceof Image) {
            if (null !== $entity->getFile()) {
                $this->removeFile($entity->getSrc());

                /** @var string $filename */
                $filename = $this->uploadFile($entity->getFile());
                $entity->setSrc($filename);
            }
        }
    }

    /**
     * @param LifecycleEventArgs $lifecycleEventArgs
     */
    public function preRemove(LifecycleEventArgs $lifecycleEventArgs): void
    {
        /** @var object $entity */
        $entity = $lifecycleEventArgs->getEntity();

        if ($entity instanceof Article) {
            /** @var string|null $file */
            $file = $entity->getPdf();
            $this->removeFile($file);
        }

        if ($entity instanceof Image) {
            /** @var string|null $file */
            $file = $entity->getSrc();
            $this->removeFile($file);
        }

        if ($entity instanceof Newsletter) {
            /** @var string $email */
            $email = $entity->getEmail();
            $newsletterEvent = new NewsletterEvent(null, $email);

            $this->eventDispatcher->dispatch(
                NewsletterEvent::APP_BUNDLE_NEWSLETTER_CONFIRM_UNSUBSCRIPTION,
                $newsletterEvent
            );

            if ($newsletterEvent->getStatus() === NewsletterService::ERROR) {
                $this->logger->error(
                    sprintf('An error occurred when trying to send unsubscription confirmation to %s.', $email), [
                    '_method' => __METHOD__
                ]);
            }
        }
    }

    /**
     * @param UploadedFile $uploadedFile
     * @return string
     */
    private function uploadFile(UploadedFile $uploadedFile): string
    {
        return $this->fileService->uploadFile($uploadedFile);
    }

    /**
     * @param string|null $file
     */
    private function removeFile(?string $file): void
    {
        if (null === $file) {
            return;
        }

        try {
            $this->fileService->removeFile($file);
            $this->logger->info(sprintf("Removed file %s", $file), [
                '_method' => __METHOD__
            ]);
        } catch (Exception $exception) {
            $this->logger->error($exception->getMessage(), [
                '_method' => __METHOD__
            ]);
        }
    }
}