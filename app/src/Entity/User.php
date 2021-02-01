<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

use App\Entity\Traits\TimestampableCreatedTrait;
use App\Entity\Traits\TimestampableUpdatedTrait;
use App\Entity\Traits\TimestampableDeletedTrait;
use App\Entity\Traits\ActiveTrait;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @ORM\Table(name="`users`")
 * @ORM\HasLifecycleCallbacks()
 * @ORM\EntityListeners(
 *     {
 *          "App\EventListener\UserListener"
 *     }
 * )
 * @UniqueEntity(fields={"username"}, message="There is already an account with this username")
 * @UniqueEntity(fields={"email"}, message="It looks like your already have an account!")
 */
class User implements UserInterface
{
    use ActiveTrait;
    use TimestampableCreatedTrait;
    use TimestampableUpdatedTrait;
    use TimestampableDeletedTrait;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     */
    private $username;

    /**
     * @ORM\Column(type="json")
     */
    private $roles = ['ROLE_USER'];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     */
    private $email;

    /**
     * @ORM\OneToMany(targetEntity=Log::class, mappedBy="user")
     */
    private $logs;

    /**
     * @Assert\Length(min=8, max=128)
     */
    private ?string $plainPassword = null;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isVerified = false;
    
    public function __construct()
    {
        $this->isActive = true;
        $this->logs = new ArrayCollection();
    }

    public function __toString()
    {
        return (string) $this->username;
    }


    public function getId(): ?int
    {
        return $this->id;
    }


    public function getUsername(): string
    {
        return (string) $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }


    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }


    public function getRoles(): array
    {
        return array_unique($this->roles);
    }


    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    public function getPassword(): string
    {
        return (string) $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function cleanPassword(): self
    {
        return $this->setPassword('');
    }

    public function getSalt()
    {
        // not needed when using the "bcrypt" algorithm in security.yaml
    }

    public function eraseCredentials()
    {
        $this->plainPassword = null;
    }

    public function getPlainPassword(): ?string
    {
        return $this->plainPassword;
    }

    public function setPlainPassword(string $password): void
    {
        $this->plainPassword = $password;
    }


    /**
     * @return Collection|Log[]
     */
    public function getLogs(): Collection
    {
        return $this->logs;
    }

    public function addLogs(Log $logs): self
    {
        if (!$this->logs->contains($logs)) {
            $this->logs[] = $logs;
            $logs->setUser($this);
        }

        return $this;
    }

    public function removeLogs(Log $logs): self
    {
        if ($this->logs->removeElement($logs)) {
            // set the owning side to null (unless already changed)
            if ($logs->getUser() === $this) {
                $logs->setUser(null);
            }
        }

        return $this;
    }

    public function isVerified(): bool
    {
        return $this->isVerified;
    }

    public function setIsVerified(bool $isVerified): self
    {
        $this->isVerified = $isVerified;

        return $this;
    }


}
