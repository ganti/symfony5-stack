<?php

namespace App\Entity;

use App\Repository\LogRepository;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\Traits\TimestampableCreatedTrait;
use App\Entity\Traits\TimestampableUpdatedTrait;
use App\Entity\Traits\TimestampableDeletedTrait;

/**
 * @ORM\Entity(repositoryClass=LogRepository::class)
 * @ORM\Table(name="`logs`")
 */
class Log
{
    use TimestampableCreatedTrait;
    
    public const ALLOWED_LEVELS = ['ERROR', 'WARNING', 'INFO', 'NOTICE', 'DEBUG', 'NO_LEVEL'];

    public function __construct()
    {
        $this->subcontext = '';
    }

    public function __toString()
    {
        return (string) $this->message;
    }

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=50, nullable=false)
     */
    private $level;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $context;

    /**
     * @ORM\Column(type="string", length=255, options={"default":""})
     */
    private $subcontext;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $message;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $success;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="logs")
     */
    private $user;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $clientIP;

    /**
     * @ORM\Column(type="string", length=20, nullable=true)
     */
    private $clientLocale;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $requestMethod;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $requestPath;
    
    public function getId(): ?int
    {
        return $this->id;
    }

    public function setLevel(?string $level): self
    {
        $level = strtoupper($level);
        if (in_array($level, self::ALLOWED_LEVELS)) {
            $this->level = $level;
        } else {
            $this->level = 'NO_LEVEL';
        }
        return $this;
    }

    public function getLevel(): ?string
    {
        return $this->level;
    }

    public function getContext(): ?string
    {
        return $this->context;
    }

    public function setContext(string $context): self
    {
        $this->context = $context;
        return $this;
    }

    public function getSubcontext(): ?string
    {
        return $this->subcontext;
    }

    public function setSubcontext(string $subcontext): self
    {
        $this->subcontext = $subcontext;
        return $this;
    }


    public function getFullcontext(): ?string
    {
        return $this->context . ( empty($this->subcontext) ? '' : ':'.$this->subcontext );
    }

    public function setFullcontext(?string $fullcontext): self
    {
        if (empty($fullcontext) == false) {
            $split = explode(':', $fullcontext);
            $this->setContext(array_kshift($split));
            if (empty($split) == false) {
                $this->setSubcontext(array_kshift(implode(":", $split)));
            }
        }
        return $this;
    }


    public function setMessage(?string $message): self
    {
        $this->message = $message;
        return $this;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function getSuccess(): ?bool
    {
        return $this->success;
    }

    public function setSuccess(?bool $success): self
    {
        $this->success = $success;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getClientIP(): ?string
    {
        return $this->clientIP;
    }

    public function setClientIP(?string $clientIP): self
    {
        $this->clientIP = $clientIP;

        return $this;
    }

    public function getClientLocale(): ?string
    {
        return $this->clientLocale;
    }

    public function setClientLocale(?string $clientLocale): self
    {
        $this->clientLocale = $clientLocale;

        return $this;
    }

    public function getRequestMethod(): ?string
    {
        return $this->requestMethod;
    }

    public function setRequestMethod(?string $requestMethod): self
    {
        $this->requestMethod = $requestMethod;

        return $this;
    }

    public function getRequestPath(): ?string
    {
        return $this->requestPath;
    }

    public function setRequestPath(?string $requestPath): self
    {
        $this->requestPath = $requestPath;

        return $this;
    }

}