<?php

namespace Message\Model;

class Message
{
    protected $id;
    protected $fromUserId;
    protected $toUserId;
    protected $dealId;
    protected $subject;
    protected $content;
    protected $status;
    protected $isRead;
    protected $readAt;
    protected $createdAt;
    protected $fromUser;
    protected $toUser;
    protected $deal;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): self
    {
        $this->id = $id;
        return $this;
    }

    public function getFromUserId(): ?int
    {
        return $this->fromUserId;
    }

    public function setFromUserId(int $fromUserId): self
    {
        $this->fromUserId = $fromUserId;
        return $this;
    }

    public function getToUserId(): ?int
    {
        return $this->toUserId;
    }

    public function setToUserId(int $toUserId): self
    {
        $this->toUserId = $toUserId;
        return $this;
    }

    public function getDealId(): ?int
    {
        return $this->dealId;
    }

    public function setDealId(?int $dealId): self
    {
        $this->dealId = $dealId;
        return $this;
    }

    public function getSubject(): ?string
    {
        return $this->subject;
    }

    public function setSubject(?string $subject): self
    {
        $this->subject = $subject;
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

    public function getStatus(): string
    {
        return $this->status ?? 'unread';
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;
        return $this;
    }

    public function getIsRead(): bool
    {
        return (bool) ($this->isRead ?? false);
    }

    public function setIsRead(bool $isRead): self
    {
        $this->isRead = $isRead;
        return $this;
    }

    public function getReadAt(): ?\DateTime
    {
        return $this->readAt;
    }

    public function setReadAt($readAt): self
    {
        if (is_string($readAt)) {
            $this->readAt = $readAt ? new \DateTime($readAt) : null;
        } else {
            $this->readAt = $readAt;
        }
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

    public function getFromUser()
    {
        return $this->fromUser;
    }

    public function setFromUser($fromUser): self
    {
        $this->fromUser = $fromUser;
        return $this;
    }

    public function getToUser()
    {
        return $this->toUser;
    }

    public function setToUser($toUser): self
    {
        $this->toUser = $toUser;
        return $this;
    }

    public function getDeal()
    {
        return $this->deal;
    }

    public function setDeal($deal): self
    {
        $this->deal = $deal;
        return $this;
    }
}

