<?php

namespace Forum\Model;

class ForumCategory
{
    protected $id;
    protected $name;
    protected $slug;
    protected $description;
    protected $order;
    protected $isActive;
    protected $createdAt;
    protected $updatedAt;
    protected $topicsCount = 0;
    protected $postsCount = 0;

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

    public function getOrder(): int
    {
        return $this->order ?? 0;
    }

    public function setOrder(int $order): self
    {
        $this->order = $order;
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

    public function getTopicsCount(): int
    {
        return $this->topicsCount ?? 0;
    }

    public function setTopicsCount(int $count): self
    {
        $this->topicsCount = $count;
        return $this;
    }

    public function getPostsCount(): int
    {
        return $this->postsCount ?? 0;
    }

    public function setPostsCount(int $count): self
    {
        $this->postsCount = $count;
        return $this;
    }
}

