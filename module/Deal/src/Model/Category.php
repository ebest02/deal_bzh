<?php

namespace Deal\Model;

class Category
{
    protected $id;
    protected $name;
    protected $slug;
    protected $description;
    protected $parentId;
    protected $isActive;
    protected $createdAt;
    protected $updatedAt;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): self
    {
        $this->id = $id;
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

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;
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

    public function getParentId(): ?int
    {
        return $this->parentId;
    }

    public function setParentId(?int $parentId): self
    {
        $this->parentId = $parentId;
        return $this;
    }

    public function getIsActive(): bool
    {
        return (bool) ($this->isActive ?? true);
    }

    public function setIsActive(bool $isActive): self
    {
        $this->isActive = $isActive;
        return $this;
    }

    public function getCreatedAt(): ?\DateTime
    {
        return $this->createdAt;
    }

    public function setCreatedAt($createdAt): self
    {
        if (is_string($createdAt)) {
            $this->createdAt = new \DateTime($createdAt);
        } else {
            $this->createdAt = $createdAt;
        }
        return $this;
    }

    public function getUpdatedAt(): ?\DateTime
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt($updatedAt): self
    {
        if (is_string($updatedAt)) {
            $this->updatedAt = new \DateTime($updatedAt);
        } else {
            $this->updatedAt = $updatedAt;
        }
        return $this;
    }
}

