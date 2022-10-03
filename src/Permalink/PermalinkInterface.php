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

namespace Sonata\NewsBundle\Permalink;

use InvalidArgumentException;
use Sonata\NewsBundle\Model\PostInterface;

interface PermalinkInterface
{
    /**
     * @param PostInterface $post
     * @return mixed
     */
    public function generate(PostInterface $post): mixed;

    /**
     * @param string $permalink
     * @return array
     * @throws InvalidArgumentException
     *
     */
    public function getParameters(string $permalink): array;
}
