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

use Sonata\NewsBundle\Model\PostInterface;

final class CollectionPermalink implements PermalinkInterface
{
    /**
     * @param PostInterface $post
     * @return string
     */
    public function generate(PostInterface $post): string
    {
        return null === $post->getCollection()
            ? $post->getSlug()
            : sprintf('%s/%s', $post->getCollection()->getSlug(), $post->getSlug());
    }

    /**
     * @param string $permalink
     * @return array
     */
    public function getParameters(string $permalink): array
    {
        $parameters = explode('/', $permalink);

        if (\count($parameters) > 2 || 0 === \count($parameters)) {
            throw new \InvalidArgumentException('wrong permalink format');
        }

        if (!str_contains($permalink, '/')) {
            $collection = null;
            $slug = $permalink;
        } else {
            [$collection, $slug] = $parameters;
        }

        return [
            'collection' => $collection,
            'slug' => $slug,
        ];
    }
}
