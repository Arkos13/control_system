<?php

declare(strict_types=1);

namespace App\Model\User\UseCase\Create;

use App\Model\Flusher;
use App\Model\User\Entity\User\Email;
use App\Model\User\Entity\User\Name;
use App\Model\User\Entity\User\User;
use App\Model\User\Entity\User\Id;
use App\Model\User\Entity\User\UserRepository;
use App\Model\User\Service\PasswordGenerator;
use App\Model\User\Service\PasswordHasher;

class Handler
{
    private $userRepository;
    private $hasher;
    private $generator;
    private $flusher;

    public function __construct(
        UserRepository $userRepository,
        PasswordHasher $hasher,
        PasswordGenerator $generator,
        Flusher $flusher
    )
    {
        $this->userRepository = $userRepository;
        $this->hasher = $hasher;
        $this->generator = $generator;
        $this->flusher = $flusher;
    }

    public function handle(Command $command): void
    {
        $email = new Email($command->email);
        if ($this->userRepository->hasByEmail($email)) {
            throw new \DomainException('User with this email already exists.');
        }
        $user = User::create(
            Id::next(),
            new \DateTimeImmutable(),
            new Name(
                $command->firstName,
                $command->lastName
            ),
            $email,
            $this->hasher->hash($this->generator->generate())
        );
        $this->userRepository->add($user);
        $this->flusher->flush();
    }
}
