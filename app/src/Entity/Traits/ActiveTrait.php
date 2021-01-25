<?php

namespace App\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;

trait ActiveTrait
{
    /**
     * @var boolean
     * @ORM\Column(type="boolean", options={"default": false})
     */
    private $isActive = false;
    /**
     * @return bool
     */
    public function isActive(): bool
    {
        return $this->isActive;
    }
    /**
     * @param bool $active
     * @return ActiveTrait
     */
    public function setActive(bool $isActive) :self
    {
        $this->isActive = $isActive;
        return $this;
    }
}