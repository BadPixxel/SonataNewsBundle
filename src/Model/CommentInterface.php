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

interface CommentInterface
{
    public const STATUS_INVALID = 0;
    public const STATUS_VALID = 1;
    public const STATUS_MODERATE = 2;

    /**
     * @return mixed
     */
    public function getId(): mixed;

    /**
     * Set name.
     *
     * @param string|null $name
     */
    public function setName(?string $name);

    /**
     * Get name.
     *
     * @return string|null $name
     */
    public function getName(): ?string;

    /**
     * Set email.
     *
     * @param string|null $email
     */
    public function setEmail(?string $email);

    /**
     * Get email.
     *
     * @return string|null $email
     */
    public function getEmail(): ?string;

    /**
     * Set url.
     *
     * @param string|null $url
     */
    public function setUrl(?string $url);

    /**
     * Get url.
     *
     * @return string|null $url
     */
    public function getUrl(): ?string;

    /**
     * Set message.
     *
     * @param string $message
     */
    public function setMessage(string $message);

    /**
     * Get message.
     *
     * @return string $message
     */
    public function getMessage(): string;

    /**
     * Set created_at.
     *
     * @param DateTime|null $createdAt
     */
    public function setCreatedAt(?DateTime $createdAt = null);

    /**
     * Get created_at.
     *
     * @return DateTime $createdAt
     */
    public function getCreatedAt(): DateTime;

    /**
     * Set updated_at.
     *
     * @param DateTime|null $updatedAt
     */
    public function setUpdatedAt(?DateTime $updatedAt = null);

    /**
     * Get updated_at.
     *
     * @return DateTime $updatedAt
     */
    public function getUpdatedAt(): DateTime;

    /**
     * Get text version of comment status.
     *
     * @return string|null
     */
    public function getStatusCode(): ?string;

    /**
     * Set status.
     *
     * @param int $status
     */
    public function setStatus(int $status);

    /**
     * Get status.
     *
     * @return int $status
     */
    public function getStatus(): int;

    /**
     * Set post.
     *
     * @param PostInterface $post
     */
    public function setPost(PostInterface $post);

    /**
     * Get post.
     *
     * @return PostInterface $post
     */
    public function getPost(): PostInterface;
}
