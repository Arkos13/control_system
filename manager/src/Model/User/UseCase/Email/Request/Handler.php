<?php

declare(strict_types=1);

namespace App\Model\User\UseCase\Email\Request;

use App\Model\Flusher;
use App\Model\User\Entity\User\Email;
use App\Model\User\Entity\User\Id;
use App\Model\User\Entity\User\UserRepository;
use App\Model\User\Service\NewEmailConfirmTokenizer;
use App\Model\User\Service\NewEmailConfirmTokenSender;

class Handler
{
    private $userRepository;
    private $tokenizer;
    private $sender;
    private $flusher;

    public function __construct(
        UserRepository $userRepository,
        NewEmailConfirmTokenizer $tokenizer,
        NewEmailConfirmTokenSender $sender,
        Flusher $flusher
    )
    {
        $this->userRepository = $userRepository;
        $this->tokenizer = $tokenizer;
        $this->sender = $sender;
        $this->flusher = $flusher;
    }

    public function handle(Command $command): void
    {
        $user = $this->userRepository->get(new Id($command->id));
        $email = new Email($command->email);
        if ($this->userRepository->hasByEmail($email)) {
            throw new \DomainException('Email is already in use.');
        }
        $user->requestEmailChanging(
            $email,
            $token = $this->tokenizer->generate()
        );
        $this->flusher->flush();
        $this->sender->send($email, $token);
    }
}
