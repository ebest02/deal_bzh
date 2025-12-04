<?php

namespace Forum\Model;

class Topic
{
    protected $id;
    protected $categoryId;
    protected $userId;
    protected $title;
    protected $content;
    protected $isPinned;
    protected $isLocked;
    protected $views;
    protected $repliesCount;
    protected $lastReplyAt;
    protected $lastReplyUserId;
    protected $status;
    protected $createdAt;
    protected $updatedAt;
    protected $category;
    protected $user;
    protected $lastReplyUser;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): self
    {
        $this->id = $id;
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

    public function getUserId(): ?int
    {
        return $this->userId;
    }

    public function setUserId(int $userId): self
    {
        $this->userId = $userId;
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

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;
        return $this;
    }

    public function getIsPinned(): bool
    {
        return (bool) ($this->isPinned ?? false);
    }

    public function setIsPinned(bool $isPinned): self
    {
        $this->isPinned = $isPinned;
        return $this;
    }

    public function getIsLocked(): bool
    {
        return (bool) ($this->isLocked ?? false);
    }

    public function setIsLocked(bool $isLocked): self
    {
        $this->isLocked = $isLocked;
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

    public function getRepliesCount(): int
    {
        return $this->repliesCount ?? 0;
    }

    public function setRepliesCount(int $count): self
    {
        $this->repliesCount = $count;
        return $this;
    }

    public function getLastReplyAt(): ?\DateTime
    {
        return $this->lastReplyAt;
    }

    public function setLastReplyAt($lastReplyAt): self
    {
        if (is_string($lastReplyAt)) {
            $this->lastReplyAt = $lastReplyAt ? new \DateTime($lastReplyAt) : null;
        } else {
            $this->lastReplyAt = $lastReplyAt;
        }
        return $this;
    }

    public function getLastReplyUserId(): ?int
    {
        return $this->lastReplyUserId;
    }

    public function setLastReplyUserId(?int $userId): self
    {
        $this->lastReplyUserId = $userId;
        return $this;
    }

    public function getStatus(): string
    {
        return $this->status ?? 'published';
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;
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

    public function getLastReplyUser()
    {
        return $this->lastReplyUser;
    }

    public function setLastReplyUser($user): self
    {
        $this->lastReplyUser = $user;
        return $this;
    }
}

