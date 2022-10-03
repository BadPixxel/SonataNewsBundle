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

use Sonata\NewsBundle\Permalink\PermalinkInterface;

class Blog implements BlogInterface
{
    /**
     * @var string
     */
    protected string $title;

    /**
     * @var string
     */
    protected string $link;

    /**
     * @var string
     */
    protected string $description;

    /**
     * @var PermalinkInterface
     */
    protected PermalinkInterface $permalinkGenerator;

    /**
     * @param string $title
     * @param string $link
     * @param string $description
     * @param PermalinkInterface $permalinkGenerator
     */
    public function __construct(string $title, string $link, string $description, PermalinkInterface $permalinkGenerator)
    {
        $this->title = $title;
        $this->link = $link;
        $this->description = $description;
        $this->permalinkGenerator = $permalinkGenerator;
    }

    /**
     * @return PermalinkInterface
     */
    public function getPermalinkGenerator(): PermalinkInterface
    {
        return $this->permalinkGenerator;
    }

    /**
     * @param string $description
     * @return void
     */
    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @param string $link
     * @return void
     */
    public function setLink(string $link): void
    {
        $this->link = $link;
    }

    /**
     * @return string
     */
    public function getLink(): string
    {
        return $this->link;
    }

    /**
     * @param string $title
     * @return void
     */
    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }
}
