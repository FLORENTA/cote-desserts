<?php

namespace AppBundle\Manager;

use AppBundle\Entity\Newsletter;
use AppBundle\Repository\NewsletterRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Exception;

/**
 * Class NewsletterManager
 * @package AppBundle\Manager
 */
class NewsletterManager
{
    /** @var EntityManagerInterface $em */
    private $em;

    /** @var NewsletterRepository $newsletterRepository */
    private $newsletterRepository;

    /**
     * NewsletterManager constructor.
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->em = $entityManager;
        $this->newsletterRepository = $entityManager->getRepository(Newsletter::class);
    }

    /**
     * @param string $id
     * @return Newsletter|null
     */
    public function getSubscriberById(string $id): ?Newsletter
    {
        return $this->newsletterRepository->find($id);
    }

    /**
     * @return array
     */
    public function getSubscribers(): array
    {
        return $this->newsletterRepository->getSubscribers();
    }

    /**
     * @param string $email
     * @return Newsletter|null
     */
    public function getSubscriberByEmail(string $email): ?Newsletter
    {
        return $this->newsletterRepository->findOneBy(['email' => $email]);
    }

    /**
     * @param string $token
     * @return Newsletter|null
     */
    public function getSubscriberByToken(string $token): ?Newsletter
    {
        return $this->newsletterRepository->findOneBy(['token' => $token]);
    }

    /**
     * @param Newsletter $newsletter
     * @throws Exception
     */
    public function createSubscriber(Newsletter $newsletter): void
    {
        $newsletter->setDate(new DateTime())->setToken(md5(uniqid()));
        $this->em->persist($newsletter);
        $this->em->flush();
    }

    /**
     * @param Newsletter $subscriber
     */
    public function deleteSubscriber(Newsletter $subscriber): void
    {
        $this->em->remove($subscriber);
        $this->em->flush();
    }
}