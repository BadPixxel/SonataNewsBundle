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
use Doctrine\Common\Collections\Collection;
use Sonata\ClassificationBundle\Model\{CollectionInterface,TagInterface};
use Sonata\MediaBundle\Model\MediaInterface;
use Symfony\Component\Security\Core\User\UserInterface;

interface PostInterface
{
    /**
     * @return mixed
     */
    public function getId(): mixed;

    /**
     * Set title.
     *
     * @param string $title
     */
    public function setTitle(string $title);

    /**
     * Get title.
     *
     * @return string $title
     */
    public function getTitle(): string;

    /**
     * Set abstract.
     *
     * @param string $abstract
     */
    public function setAbstract(string $abstract);

    /**
     * Get abstract.
     *
     * @return string $abstract
     */
    public function getAbstract(): string;

    /**
     * Set content.
     *
     * @param string $content
     */
    public function setContent(string $content);

    /**
     * Get content.
     *
     * @return string $content
     */
    public function getContent(): string;

    /**
     * Set enabled.
     *
     * @param bool $enabled
     */
    public function setEnabled(bool $enabled);

    /**
     * Get enabled.
     *
     * @return bool $enabled
     */
    public function getEnabled(): bool;

    /**
     * Set slug.
     *
     * @param string $slug
     */
    public function setSlug(string $slug);

    /**
     * Get slug.
     *
     * @return string $slug
     */
    public function getSlug(): string;

    /**
     * Set publication_date_start.
     */
    public function setPublicationDateStart(?DateTime $publicationDateStart = null);

    /**
     * Get publication_date_start.
     *
     * @return DateTime|null $publicationDateStart
     */
    public function getPublicationDateStart(): ?DateTime;

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
     * @return Datetime $updatedAt
     */
    public function getUpdatedAt(): DateTime;

    /**
     * Add comments.
     * @param CommentInterface $comments
     */
    public function addComments(CommentInterface $comments);

    /**
     * @param Collection|CommentInterface[] $comments
     */
    public function setComments(Collection|array $comments);

    /**
     * Get comments.
     *
     * @return Collection|CommentInterface[] $comments
     */
    public function getComments(): Collection|array;

    /**
     * Add tags.
     * @param TagInterface $tags
     */
    public function addTags(TagInterface $tags);

    /**
     * Get tags.
     *
     * @return Collection|TagInterface[] $tags
     */
    public function getTags(): array|Collection;

    /**
     * @param Collection|TagInterface[] $tags
     */
    public function setTags(array|Collection $tags);

    /**
     * @return string
     */
    public function getYear(): string;

    /**
     * @return string
     */
    public function getMonth(): string;

    /**
     * @return string
     */
    public function getDay(): string;

    /**
     * Set comments_enabled.
     *
     * @param bool $commentsEnabled
     */
    public function setCommentsEnabled(bool $commentsEnabled);

    /**
     * Get comments_enabled.
     *
     * @return bool $commentsEnabled
     */
    public function getCommentsEnabled(): bool;

    /**
     * Set comments_close_at.
     */
    public function setCommentsCloseAt(?DateTime $commentsCloseAt = null);

    /**
     * Get comments_close_at.
     *
     * @return DateTime|null $commentsCloseAt
     */
    public function getCommentsCloseAt(): ?DateTime;

    /**
     * Set comments_default_status.
     *
     * @param int $commentsDefaultStatus
     */
    public function setCommentsDefaultStatus(int $commentsDefaultStatus);

    /**
     * Get comments_default_status.
     *
     * @return int $commentsDefaultStatus
     */
    public function getCommentsDefaultStatus(): int;

    /**
     * Set comments_count.
     *
     * @param int $commentscount
     */
    public function setCommentsCount(int $commentscount);

    /**
     * Get comments_count.
     *
     * @return int $commentsCount
     */
    public function getCommentsCount(): ?int;

    /**
     * @return bool
     */
    public function isCommentable(): bool;

    /**
     * @return bool
     */
    public function isPublic(): bool;

    /**
     * @param UserInterface|null $author
     */
    public function setAuthor(?UserInterface $author);

    /**
     * @return UserInterface|null
     */
    public function getAuthor(): ?UserInterface;

    /**
     * @param MediaInterface|null $image
     */
    public function setImage(?MediaInterface $image);

    /**
     * @return MediaInterface|null
     */
    public function getImage(): ?MediaInterface;

    /**
     * @return CollectionInterface|null
     */
    public function getCollection(): ?CollectionInterface;

    public function setCollection(?CollectionInterface $collection = null);

    /**
     * @param string $contentFormatter
     */
    public function setContentFormatter(string $contentFormatter);

    /**
     * @return string
     */
    public function getContentFormatter(): string;

    /**
     * @param string $rawContent
     */
    public function setRawContent(string $rawContent);

    /**
     * @return string
     */
    public function getRawContent(): string;
}
