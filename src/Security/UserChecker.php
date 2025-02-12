<?php

namespace App\Security;

use App\Security\User as AppUser;
use App\Exception\AccountDeletedException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\Exception\AccountExpiredException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;

class UserChecker implements UserCheckerInterface
{
    public function checkPreAuth(UserInterface $user)
    {
        // dd($user->getActivationToken());

        if ($user->getActivationToken() !== null) {
            
            throw new CustomUserMessageAuthenticationException('Compte non activé');
            return $user;
        }
        
        if (!$user instanceof AppUser) {
            return;
        }

        // user is deleted, show a generic Account Not Found message.
        if ($user->isDeleted()) {
            throw new AccountDeletedException();
        }
    }

    public function checkPostAuth(UserInterface $user)
    {
        // if (!$user instanceof AppUser) {
        //     return;
        // }

        // // // user account is expired, the user may be notified
        // if ($user->isExpired()) {
        //     throw new AccountExpiredException('...');
        // }
    }
}