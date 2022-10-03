<?php

declare(strict_types=1);

/*
 * This file is part of the Sonata Project package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\NewsBundle\Model;

use DateTime;

abstract class Comment implements CommentInterface
{
    /**
     * Name of the author.
     *
     * @var string|null
     */
    protected ?string $name;

    /**
     * Email of the author.
     *
     * @var string|null
     */
    protected ?string $email;

    /**
     * Website url of the author.
     *
     * @var string|null
     */
    protected ?string $url;

    /**
     * Comment content.
     *
     * @var string
     */
    protected string $message;

    /**
     * Comment created date.
     *
     * @var DateTime
     */
    protected DateTime $createdAt;

    /**
     * Last update date.
     *
     * @var DateTime
     */
    protected DateTime $updatedAt;

    /**
     * Moderation status.
     *
     * @var int
     */
    protected int $status = self::STATUS_VALID;

    /**
     * Post for which the comment is related to.
     *
     * @var PostInterface
     */
    protected PostInterface $post;

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getName() ?: 'n-a';
    }

    /**
     * @param string|null $name
     * @return void
     */
    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string|null $email
     * @return void
     */
    public function setEmail(?string $email): void
    {
        $this->email = $email;
    }

    /**
     * @return string|null
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * @param string|null $url
     * @return void
     */
    public function setUrl(?string $url): void
    {
        $this->url = $url;
    }

    /**
     * @return string|null
     */
    public function getUrl(): ?string
    {
        return $this->url;
    }

    /**
     * @param string $message
     * @return void
     */
    public function setMessage(string $message): void
    {
        $this->message = $message;
    }

    /**
     * @return string
     */
    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * @param DateTime|null $createdAt
     * @return void
     */
    public function setCreatedAt(?DateTime $createdAt = null): void
    {
        $this->createdAt = $createdAt;
    }

    /**
     * @return DateTime
     */
    public function getCreatedAt(): DateTime
    {
        return $this->createdAt;
    }

    /**
     * @param DateTime|null $updatedAt
     * @return void
     */
    public function setUpdatedAt(?DateTime $updatedAt = null): void
    {
        $this->updatedAt = $updatedAt;
    }

    /**
     * @return DateTime
     */
    public function getUpdatedAt(): DateTime
    {
        return $this->updatedAt;
    }

    /**
     * @return string[]
     */
    public static function getStatusList(): array
    {
        return [
            self::STATUS_MODERATE => 'moderate',
            self::STATUS_INVALID => 'invalid',
            self::STATUS_VALID => 'valid',
        ];
    }

    /**
     * @return string|null
     */
    public function getStatusCode()
    {
        $status = self::getStatusList();

        return $status[$this->getStatus()] ?? null;
    }

    /**
     * @return void
     */
    public function preUpdate(): void
    {
        $this->setUpdatedAt(new DateTime());
    }

    /**
     * @param int $status
     * @return void
     */
    public function setStatus(int $status): void
    {
        $this->status = $status;
    }

    /**
     * @return int
     */
    public function getStatus(): int
    {
        return $this->status;
    }

    /**
     * @param PostInterface $post
     * @return void
     */
    public function setPost(PostInterface $post): void
    {
        $this->post = $post;
    }

    /**
     * @return PostInterface
     */
    public function getPost(): PostInterface
    {
        return $this->post;
    }
}
