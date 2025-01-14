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

final class DatePermalink implements PermalinkInterface
{
    /**
     * @var string
     */
    protected string $pattern;

    /**
     * @param string $pattern
     */
    public function __construct(string $pattern = '%1$04d/%2$d/%3$d/%4$s')
    {
        $this->pattern = $pattern;
    }

    /**
     * @param PostInterface $post
     * @return string
     */
    public function generate(PostInterface $post): string
    {
        return sprintf(
            $this->pattern,
            $post->getYear(),
            $post->getMonth(),
            $post->getDay(),
            $post->getSlug()
        );
    }

    /**
     * @param string $permalink
     * @return array
     */
    public function getParameters(string $permalink): array
    {
        $parameters = explode('/', $permalink);

        if (4 !== \count($parameters)) {
            throw new \InvalidArgumentException('wrong permalink format');
        }

        [$year, $month, $day, $slug] = $parameters;

        return [
            'year' => (int) $year,
            'month' => (int) $month,
            'day' => (int) $day,
            'slug' => $slug,
        ];
    }
}
