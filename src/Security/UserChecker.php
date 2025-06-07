<?php
namespace App\Security;

use App\Entity\User;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAccountStatusException;

class UserChecker implements UserCheckerInterface
{
    public function checkPreAuth(UserInterface $user): void
    {
        if (!$user instanceof User) {
            return;
        }

        if ($user->getStatus() !== "Active") {
            throw new CustomUserMessageAccountStatusException(
                'Your account is blocked. Please contact support.'
            );
        }
    }

    public function checkPostAuth(UserInterface $user): void
    {
        // You can add additional checks after authentication if needed
    }
}