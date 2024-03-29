<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Log;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

class LogService
{
    protected Log $log;
    private Security $security;
    protected RequestStack $requestStack; 
    protected EntityManagerInterface $manager;

    /**
     * LogService constructor.
     * @param EntityManagerInterface $manager
     * @param Security $security
     * @param RequestStack $requestStack
     */
    public function __construct(EntityManagerInterface $manager, Security $security, RequestStack $requestStack)
    {
        $this->manager = $manager;
        $this->security = $security;
        $this->requestStack = $requestStack;

        $this->log = new Log();
        $this->log->setUser( $this->security->getUser() );
        $this->log->setClientIP( $this->requestStack->getCurrentRequest()->getClientIp() );
        $this->log->setClientLocale( $this->requestStack->getCurrentRequest()->getLocale() );
        $this->log->setRequestMethod( $this->requestStack->getCurrentRequest()->getMethod() );
        $this->log->setRequestPath( $this->requestStack->getCurrentRequest()->getPathInfo() );
        
    }

    private function logEvent(string $level, string $context, string $subcontext = null, string $message, bool $isSuccess = null): self
    {
        $this->log->setLevel($level);
        $this->log->setContext($context);
        $this->log->setSubcontext($subcontext);
        $this->log->setMessage($message);
        $this->log->setSuccess($isSuccess);
        

        $this->manager->persist($this->log);
        $this->manager->flush();
        return $this;
    }

    protected function info(string $context, string $subcontext, string $message, bool $isSuccess = null): self
    {
        $this->logEvent('INFO', $context, $subcontext, $message, $isSuccess);
        return $this;
    }

    protected function debug(string $context, string $subcontext, string $message, bool $isSuccess = null): self
    {
        $this->logEvent('DEBUG', $context, $subcontext, $message, $isSuccess);
        return $this;
    }

    protected function warning(string $context, string $subcontext, string $message, bool $isSuccess = False): self
    {
        $this->logEvent('WARNING', $context, $subcontext, $message, $isSuccess);
        return $this;
    }

    protected function error(string $context, string $subcontext, string $message, bool $isSuccess = False): self
    {
        $this->logEvent('ERROR', $context, $subcontext, $message, $isSuccess);
        return $this;
    }


}