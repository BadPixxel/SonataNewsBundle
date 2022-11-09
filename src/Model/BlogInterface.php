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

interface BlogInterface
{
    /**
     * @return string
     */
    public function getTitle(): string;

    /**
     * @return string
     */
    public function getLink(): string;

    /**
     * @return string
     */
    public function getLogo(): string;

    /**
     * @return string
     */
    public function getDescription(): string;

    /**
     * @param string $title
     */
    public function setTitle(string $title);

    /**
     * @param string $link
     */
    public function setLink(string $link);

    /**
     * @param string $logo
     * @return mixed
     */
    public function setLogo(string $logo);

    /**
     * @param string $description
     */
    public function setDescription(string $description);

    /**
     * @return PermalinkInterface
     */
    public function getPermalinkGenerator(): PermalinkInterface;
}
