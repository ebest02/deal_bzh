<?php

namespace User\Model;

class User
{
    protected $id;
    protected $email;
    protected $password;
    protected $firstName;
    protected $lastName;
    protected $phone;
    protected $role;
    protected $rating;
    protected $ratingCount;
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

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;
        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;
        return $this;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(?string $firstName): self
    {
        $this->firstName = $firstName;
        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(?string $lastName): self
    {
        $this->lastName = $lastName;
        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(?string $phone): self
    {
        $this->phone = $phone;
        return $this;
    }

    public function getRole(): string
    {
        return $this->role ?? 'user';
    }

    public function setRole(string $role): self
    {
        $this->role = $role;
        return $this;
    }

    public function getRating(): float
    {
        return (float) ($this->rating ?? 0.0);
    }

    public function setRating(float $rating): self
    {
        $this->rating = $rating;
        return $this;
    }

    public function getRatingCount(): int
    {
        return $this->ratingCount ?? 0;
    }

    public function setRatingCount(int $ratingCount): self
    {
        $this->ratingCount = $ratingCount;
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

    public function getFullName(): string
    {
        $parts = array_filter([$this->firstName, $this->lastName]);
        return implode(' ', $parts) ?: $this->email;
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isModerator(): bool
    {
        return in_array($this->role, ['admin', 'moderator']);
    }
}

