<?php

declare(strict_types=1);

namespace App\Model\User\UseCase\Network\Detach;

use App\Model\Flusher;
use App\Model\User\Entity\User\Id;
use App\Model\User\Entity\User\UserRepository;

class Handler
{
    private $userRepository;
    private $flusher;

    public function __construct(UserRepository $userRepository, Flusher $flusher)
    {
        $this->userRepository = $userRepository;
        $this->flusher = $flusher;
    }

    public function handle(Command $command): void
    {
        $user = $this->userRepository->get(new Id($command->user));
        $user->detachNetwork(
            $command->network,
            $command->identity
        );
        $this->flusher->flush();
    }
}
