<?php

namespace Forum\Model;

class Post
{
    protected $id;
    protected $topicId;
    protected $userId;
    protected $content;
    protected $isFirstPost;
    protected $status;
    protected $createdAt;
    protected $updatedAt;
    protected $topic;
    protected $user;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): self
    {
        $this->id = $id;
        return $this;
    }

    public function getTopicId(): ?int
    {
        return $this->topicId;
    }

    public function setTopicId(int $topicId): self
    {
        $this->topicId = $topicId;
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

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;
        return $this;
    }

    public function getIsFirstPost(): bool
    {
        return (bool) ($this->isFirstPost ?? false);
    }

    public function setIsFirstPost(bool $isFirstPost): self
    {
        $this->isFirstPost = $isFirstPost;
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

    public function getTopic()
    {
        return $this->topic;
    }

    public function setTopic($topic): self
    {
        $this->topic = $topic;
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
}

