<?php

namespace App\Service;

use App\Service\LogService;
use App\Entity\User;

class LogUserService extends LogService
{
    public function login($message='', $success=False) : self
    {   
        $user = null;
        $requestUsername = $this->requestStack->getCurrentRequest()->get('username');
        $userByUsername =  $this->manager->getRepository(User::class)
                            ->findOneBy(['username' => $requestUsername]);

        if ($userByUsername != null) {
            $user = $userByUsername;
        }else{
            $userByEmail =  $this->manager->getRepository(User::class)
                                ->findOneBy(['email' => $requestUsername]);
            $user = $userByEmail;
        }
        if ($user != null) {
            $this->log->setUser($user);
        }

        if ($success) {
            $this->debug('user', 'auth:login', $requestUsername.' login successful '.$message, True);
        }else{
            $this->info('user', 'auth:login', $requestUsername.' failed logging in '.$message, False);
        }
        return $this;
    }

    public function logout($success=False) : self
    {   
        $this->debug('user', 'auth:logout', $this->log->getUser.' logged out ', True);
        return $this;
    }




}