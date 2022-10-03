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

use Doctrine\Common\Collections\{ArrayCollection,Collection};
use Sonata\ClassificationBundle\Model\{CollectionInterface,Tag,TagInterface};
use DateTime;
use JetBrains\PhpStorm\Pure;
use Sonata\MediaBundle\Model\MediaInterface;
use Symfony\Component\Security\Core\User\UserInterface;

abstract class Post implements PostInterface
{
    /**
     * @var string
     */
    protected string $title;

    /**
     * @var string
     */
    protected string $slug;

    /**
     * @var string
     */
    protected string $abstract;

    /**
     * @var string
     */
    protected string $content;

    /**
     * @var string
     */
    protected string $rawContent;

    /**
     * @var string
     */
    protected string $contentFormatter;

    /**
     * @var Collection|TagInterface[]
     */
    protected array|Collection $tags;

    /**
     * @var Collection|CommentInterface[]
     */
    protected array|Collection $comments;

    /**
     * @var bool
     */
    protected bool $enabled;

    /**
     * @var DateTime|null
     */
    protected ?DateTime $publicationDateStart;

    /**
     * @var DateTime
     */
    protected DateTime $createdAt;

    /**
     * @var DateTime
     */
    protected DateTime $updatedAt;

    /**
     * @var bool
     */
    protected bool $commentsEnabled = true;

    /**
     * @var DateTime|null
     */
    protected ?DateTime $commentsCloseAt;

    /**
     * @var int
     */
    protected int $commentsDefaultStatus;

    /**
     * @var int
     */
    protected int $commentsCount = 0;

    /**
     * @var UserInterface|null
     */
    protected ?UserInterface $author;

    /**
     * @var MediaInterface|null
     */
    protected ?MediaInterface $image;

    /**
     * @var CollectionInterface|null
     */
    protected ?CollectionInterface $collection;

    public function __construct()
    {
        $this->setPublicationDateStart(new DateTime());
    }

    /**
     * @return string
     */
    #[Pure] public function __toString()
    {
        return $this->getTitle() ?: 'n/a';
    }

    /**
     * @param string $title
     * @return void
     */
    public function setTitle(string $title): void
    {
        $this->title = $title;

        $this->setSlug(Tag::slugify($title));
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @param string $abstract
     * @return void
     */
    public function setAbstract(string $abstract): void
    {
        $this->abstract = $abstract;
    }

    /**
     * @return string
     */
    public function getAbstract(): string
    {
        return $this->abstract;
    }

    /**
     * @param string $content
     * @return void
     */
    public function setContent(string $content): void
    {
        $this->content = $content;
    }

    /**
     * @return string
     */
    public function getContent(): string
    {
        return $this->content;
    }

    /**
     * @param bool $enabled
     * @return void
     */
    public function setEnabled(bool $enabled): void
    {
        $this->enabled = $enabled;
    }

    /**
     * @return bool
     */
    public function getEnabled(): bool
    {
        return $this->enabled;
    }

    /**
     * @param string $slug
     * @return void
     */
    public function setSlug(string $slug): void
    {
        $this->slug = $slug;
    }

    /**
     * @return string
     */
    public function getSlug(): string
    {
        return $this->slug;
    }

    /**
     * @param DateTime|null $publicationDateStart
     * @return void
     */
    public function setPublicationDateStart(?DateTime $publicationDateStart = null): void
    {
        $this->publicationDateStart = $publicationDateStart;
    }

    /**
     * @return DateTime|null
     */
    public function getPublicationDateStart(): ?DateTime
    {
        return $this->publicationDateStart;
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
     * @param CommentInterface $comment
     * @return void
     */
    public function addComments(CommentInterface $comment): void
    {
        $this->comments[] = $comment;
        $comment->setPost($this);
    }

    /**
     * @param array|Collection $comments
     * @return void
     */
    public function setComments(Collection|array $comments): void
    {
        $this->comments = new ArrayCollection();

        foreach ($this->comments as $comment) {
            $this->addComments($comment);
        }
    }

    /**
     * @return Collection|CommentInterface[]
     */
    public function getComments(): Collection|array
    {
        return $this->comments;
    }

    /**
     * @param TagInterface $tags
     * @return void
     */
    public function addTags(TagInterface $tags): void
    {
        $this->tags[] = $tags;
    }

    /**
     * @return Collection|TagInterface[]
     */
    public function getTags(): array|Collection
    {
        return $this->tags;
    }

    /**
     * @param array|Collection $tags
     * @return void
     */
    public function setTags(array|Collection $tags): void
    {
        $this->tags = $tags;
    }

    /**
     * @return void
     */
    public function prePersist(): void
    {
        if (!$this->getPublicationDateStart()) {
            $this->setPublicationDateStart(new DateTime());
        }

        $this->setCreatedAt(new DateTime());
        $this->setUpdatedAt(new DateTime());
    }

    /**
     * @return void
     */
    public function preUpdate(): void
    {
        if (!$this->getPublicationDateStart()) {
            $this->setPublicationDateStart(new DateTime());
        }

        $this->setUpdatedAt(new DateTime());
    }

    /**
     * @return string
     */
    public function getYear(): string
    {
        return $this->getPublicationDateStart()->format('Y');
    }

    /**
     * @return string
     */
    public function getMonth(): string
    {
        return $this->getPublicationDateStart()->format('m');
    }

    /**
     * @return string
     */
    public function getDay(): string
    {
        return $this->getPublicationDateStart()->format('d');
    }

    /**
     * @param bool $commentsEnabled
     * @return void
     */
    public function setCommentsEnabled(bool $commentsEnabled): void
    {
        $this->commentsEnabled = $commentsEnabled;
    }

    /**
     * @return bool
     */
    public function getCommentsEnabled(): bool
    {
        return $this->commentsEnabled;
    }

    /**
     * @param DateTime|null $commentsCloseAt
     * @return void
     */
    public function setCommentsCloseAt(?DateTime $commentsCloseAt = null): void
    {
        $this->commentsCloseAt = $commentsCloseAt;
    }

    /**
     * @return DateTime|null
     */
    public function getCommentsCloseAt(): ?DateTime
    {
        return $this->commentsCloseAt;
    }

    /**
     * @param int $commentsDefaultStatus
     * @return void
     */
    public function setCommentsDefaultStatus(int $commentsDefaultStatus): void
    {
        $this->commentsDefaultStatus = $commentsDefaultStatus;
    }

    /**
     * @return int
     */
    public function getCommentsDefaultStatus(): int
    {
        return $this->commentsDefaultStatus;
    }

    /**
     * @param int $commentsCount
     * @return void
     */
    public function setCommentsCount(int $commentsCount): void
    {
        $this->commentsCount = $commentsCount;
    }

    /**
     * @return int
     */
    public function getCommentsCount()
    {
        return $this->commentsCount;
    }

    /**
     * @return bool
     */
    public function isCommentable(): bool
    {
        if (!$this->getCommentsEnabled() || !$this->getEnabled()) {
            return false;
        }

        if ($this->getCommentsCloseAt() instanceof DateTime) {
            return 1 === $this->getCommentsCloseAt()->diff(new DateTime())->invert;
        }

        return true;
    }

    /**
     * @return bool
     */
    public function isPublic(): bool
    {
        if (!$this->getEnabled()) {
            return false;
        }

        return 0 === $this->getPublicationDateStart()->diff(new DateTime())->invert;
    }

    /**
     * @param UserInterface|null $author
     * @return void
     */
    public function setAuthor(?UserInterface $author): void
    {
        $this->author = $author;
    }

    /**
     * @return UserInterface|null
     */
    public function getAuthor(): ?UserInterface
    {
        return $this->author;
    }

    /**
     * @param MediaInterface|null $image
     * @return void
     */
    public function setImage(?MediaInterface $image): void
    {
        $this->image = $image;
    }

    /**
     * @return MediaInterface|null
     */
    public function getImage(): ?MediaInterface
    {
        return $this->image;
    }

    /**
     * @param CollectionInterface|null $collection
     * @return void
     */
    public function setCollection(?CollectionInterface $collection = null): void
    {
        $this->collection = $collection;
    }

    /**
     * @return CollectionInterface|null
     */
    public function getCollection(): ?CollectionInterface
    {
        return $this->collection;
    }

    /**
     * @param string $contentFormatter
     * @return void
     */
    public function setContentFormatter(string $contentFormatter): void
    {
        $this->contentFormatter = $contentFormatter;
    }

    /**
     * @return string
     */
    public function getContentFormatter(): string
    {
        return $this->contentFormatter;
    }

    /**
     * @param string $rawContent
     * @return void
     */
    public function setRawContent(string $rawContent): void
    {
        $this->rawContent = $rawContent;
    }

    /**
     * @return string
     */
    public function getRawContent(): string
    {
        return $this->rawContent;
    }
}
