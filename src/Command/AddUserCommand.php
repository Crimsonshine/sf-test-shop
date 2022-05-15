<?php

namespace App\Command;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\RuntimeException;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(
    name: 'app:add-user',
    description: 'Создать пользователя',
)]
class AddUserCommand extends Command
{
    private EntityManagerInterface $entityManager;
    private UserPasswordHasherInterface $passwordHasher;
    private UserRepository $userRepository;

    public function __construct(EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher, UserRepository $userRepository)
    {
        parent::__construct();
        $this->entityManager = $entityManager;
        $this->passwordHasher = $passwordHasher;
        $this->userRepository = $userRepository;
    }

    protected function configure(): void
    {
        $this
            ->addOption('email', 'em',InputArgument::OPTIONAL, 'E-mail')
            ->addOption('password', 'p',InputArgument::OPTIONAL, 'Пароль')
            ->addOption('isAdmin', '',InputArgument::OPTIONAL, 'Устаналивается если пользователь администратор', 0)
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $email = $input->getOption('email');
        $password = $input->getOption('password');
        $isAdmin = $input->getOption('isAdmin');

        $io->title('Команда добавления пользователя');
        $io->text([
            'Пожалуйста, введите некоторую информацию'
        ]);

        if (!$email){
            $email = $io->ask('E-mail');
        }

        if (!$password){
            $password = $io->askHidden('Пароль');
        }

        if (!$isAdmin){
            $question = new Question('Установить пользователя как администратора? (1 или 0)', 0);
            $isAdmin = $io->askQuestion($question);
        }

        try{
            $user = $this->createUser($email, $password, $isAdmin);
        }
        catch (RuntimeException $exception) {
            $io->comment($exception->getMessage());
            return Command::FAILURE;
        }
        $isAdmin = boolval($isAdmin);


        $successMessage = sprintf('%s был успешно создан: %s',
            $isAdmin ? 'Администратор' : 'Пользователь',
            $email
        );

        $io->success($successMessage);

        return Command::SUCCESS;
    }

    /**
     * @param string $email
     * @param string $password
     * @param bool $isAdmin
     * @return User
     */
    private function createUser(string $email, string $password, bool $isAdmin): User
    {
        $existingUser = $this->userRepository->findOneBy(['email' => $email]);

        if ($existingUser){
            throw new RuntimeException('Пользователь уже существует');
        }

        $user = new User();
        $user->setEmail($email);
        $user->setRoles([$isAdmin ? 'ROLE_ADMIN' : 'ROLE_USER']);

        $hashedPassword = $this->passwordHasher->hashPassword($user, $password);
        $user->setPassword($hashedPassword);
        $user->setIsVerified(true);

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $user;
    }
}
