<?php

namespace Deal\Model;

class Deal
{
    protected $id;
    protected $userId;
    protected $categoryId;
    protected $title;
    protected $description;
    protected $type;
    protected $status;
    protected $location;
    protected $price;
    protected $isNegotiable;
    protected $views;
    protected $createdAt;
    protected $updatedAt;
    protected $publishedAt;
    protected $category;
    protected $user;
    protected $images = [];

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): self
    {
        $this->id = $id;
        return $this;
    }

    public function getUserId(): ?int
    {
        return $this->userId;
    }

    public function setUserId(int $userId): self
    {
        $this->userId = $userId;
        return $this;
    }

    public function getCategoryId(): ?int
    {
        return $this->categoryId;
    }

    public function setCategoryId(int $categoryId): self
    {
        $this->categoryId = $categoryId;
        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;
        return $this;
    }

    public function getType(): string
    {
        return $this->type ?? 'exchange';
    }

    public function setType(string $type): self
    {
        $this->type = $type;
        return $this;
    }

    public function getStatus(): string
    {
        return $this->status ?? 'draft';
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;
        return $this;
    }

    public function getLocation(): ?string
    {
        return $this->location;
    }

    public function setLocation(?string $location): self
    {
        $this->location = $location;
        return $this;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(?float $price): self
    {
        $this->price = $price;
        return $this;
    }

    public function getIsNegotiable(): bool
    {
        return (bool) ($this->isNegotiable ?? false);
    }

    public function setIsNegotiable(bool $isNegotiable): self
    {
        $this->isNegotiable = $isNegotiable;
        return $this;
    }

    public function getViews(): int
    {
        return $this->views ?? 0;
    }

    public function setViews(int $views): self
    {
        $this->views = $views;
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

    public function getPublishedAt(): ?\DateTime
    {
        return $this->publishedAt;
    }

    public function setPublishedAt($publishedAt): self
    {
        if (is_string($publishedAt)) {
            $this->publishedAt = $publishedAt ? new \DateTime($publishedAt) : null;
        } else {
            $this->publishedAt = $publishedAt;
        }
        return $this;
    }

    public function getCategory()
    {
        return $this->category;
    }

    public function setCategory($category): self
    {
        $this->category = $category;
        return $this;
    }

    public function getUser()
    {
        return $this->user;
    }

    public function setUser($user): self
    {
        $this->user = $user;
        return $this;
    }

    public function getImages(): array
    {
        return $this->images ?? [];
    }

    public function setImages(array $images): self
    {
        $this->images = $images;
        return $this;
    }

    public function isPublished(): bool
    {
        return $this->status === 'published';
    }
}

