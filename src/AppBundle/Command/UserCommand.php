<?php

namespace AppBundle\Command;

use AppBundle\Entity\User;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\OptimisticLockException;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\Container;

/**
 * Class UserCommand
 * @package AppBundle\Command
 */
class UserCommand extends ContainerAwareCommand
{
    /** @var Container $container */
    private $container;

    /** @var EntityManager $entityManager */
    private $entityManager;

    public function configure()
    {
        $this->setName('app:user');
        $this->addArgument('username', InputArgument::REQUIRED, 'username', null);
        $this->addArgument('password', InputArgument::REQUIRED, 'password', null);
        $this->addArgument('password_bis', InputArgument::REQUIRED, 'password bis', null);
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|void|null
     * @throws \Exception
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $this->container = self::getContainer();
        $this->entityManager = $this->container->get('doctrine.orm.entity_manager');

        if ($input->getArgument('password_bis') == $this->container->getParameter('secret_password')) {
            $password = password_hash($input->getArgument('password'), PASSWORD_DEFAULT);
            /** @var User $user */
            $user = new User();
            $user->setUsername($input->getArgument('username'));
            $user->setPassword($password);
            $user->setRoles(['ROLE_ADMIN']);

            $this->entityManager->persist($user);

            try {
                $this->entityManager->flush();
                $output->writeln("L'utilisateur a bien été créé.");
            } catch (OptimisticLockException $exception) {
                $this->getContainer()->get('logger')->error($exception->getMessage());
            }
        } else {
            $output->writeln("Mauvais mot de passe. L'utilisateur n'a pas été créé.");
        }
    }
}