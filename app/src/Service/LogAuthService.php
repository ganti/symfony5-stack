<?php

namespace App\Service;

use App\Service\LogService;
use App\Entity\User;

class LogAuthService extends LogService
{
    public function login($message='', $success=False) : self
    {   
        $requestUsername = $this->requestStack->getCurrentRequest()->get('username');

        $user =  $this->manager->getRepository(User::class)
                        ->findOneBy(['username' => $requestUsername]);

        $this->log->setUserId($user);


        if ($success) {
            $this->debug('auth', 'login', $requestUsername.' login successful '.$message, True);
        }else{
            $this->info('auth', 'login', $requestUsername.' failed logging in '.$message, False);
        }
        return $this;
    }

    public function logout($message='', $success=False) : self
    {   
        $this->debug('auth', 'logout', $requestUsername.' logged out ', True);
        return $this;
    }




}