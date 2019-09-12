<?php

namespace App\Command;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * Class UserPromoteCommand.
 */
class UserPromoteCommand extends Command
{
    /**
     * @var string
     */
    protected static $defaultName = 'app:user:promote';
    /**
     * @var UserRepository
     */
    private $userRepository;
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * UserPromoteCommand constructor.
     *
     * @param UserRepository         $userRepository
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(UserRepository $userRepository, EntityManagerInterface $entityManager)
    {
        $this->userRepository = $userRepository;
        $this->entityManager = $entityManager;

        parent::__construct(self::$defaultName);
    }

    protected function configure()
    {
        $this
            ->setDescription('Promotes a user to a role')
            ->addArgument('email', InputArgument::OPTIONAL, 'Email of the user')
            ->addArgument('role', InputArgument::OPTIONAL, 'Role to promote');
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return int|void|null
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $email = $input->getArgument('email');
        $role = $input->getArgument('role');

        $user = $this->userRepository->findOneBy([
            'email' => $email,
        ]);

        if (false === $user instanceof User) {
            $io->error('User not found.');

            return;
        }

        $user->addRole($role);
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $io->success(sprintf('User %s promoted with role %s.', $email, $role));
    }
}
