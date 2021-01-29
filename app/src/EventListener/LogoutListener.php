<?php

namespace App\EventListener;

use App\Service\LogUserService;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Logout\LogoutHandlerInterface;
 
class LogoutListener implements LogoutHandlerInterface
{
 
    private LogUserService $log;
    
    public function __construct(LogUserService $log)
    {
        $this->log = $log;
    }

    /**
     * @{inheritDoc}
     */
    public function logout(Request $request, Response $response, TokenInterface $token)
    {
        $this->log->logout($token->getUser(), True);
    }
}