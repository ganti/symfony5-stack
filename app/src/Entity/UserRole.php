<?php

namespace App\Entity;

use App\Repository\UserRoleRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

use App\Entity\Traits\ActiveTrait;
use App\Entity\Traits\TimestampableCreatedTrait;
use App\Entity\Traits\TimestampableUpdatedTrait;
use App\Entity\Traits\TimestampableDeletedTrait;
/**
 * @ORM\Entity(repositoryClass=UserRoleRepository::class)
 * @ORM\Table(name="`user_role`")
 */
class UserRole
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
     * @ORM\Column(type="string", length=255, unique=true)
     */
    private $role;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $description;

    /**
     * @ORM\Column(type="boolean")
     */
    private $systemrole;

    /**
     * @ORM\ManyToOne(targetEntity=UserRole::class, inversedBy="parentRole", cascade={"persist", "remove"})
     */
    private $parentRole;

    public function __construct()
    {
        $this->isActive = true;
        $this->parentRole = null;
    }

    public function __toString()
    {
        return (string) $this->role;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRole(): ?string
    {
        return $this->role;
    }

    public function setRole(string $role): self
    {
        $this->role = $role;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getSystemrole(): ?bool
    {
        return $this->systemrole;
    }

    public function setSystemrole(bool $systemrole): self
    {
        $this->systemrole = $systemrole;

        return $this;
    }


    public function getParentRole(): ?self
    {
        return $this->parentRole;
    }

    public function setParentRole(?self $parentRole): self
    {
        $this->parentRole = $parentRole;
        return $this;
    }

    /*
     * Recursive fetch all Parent Roles
     */
    public function getParentRoleRecursive(): ?Array
    {
        $return = [];
        $parent = $this->parentRole;
        while($parent){
            $return[] = $parent->getRole() ;
            $parent = $parent->parentRole;
        }
        return $return;
    }

    public function getRoleAndParents(): ?Array{
        return array_filter(array_unique(array_merge([$this->role], $this->getParentRoleRecursive())));
    }

}
