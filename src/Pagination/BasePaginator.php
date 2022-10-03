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

namespace Sonata\NewsBundle\Pagination;

use Traversable;

abstract class BasePaginator
{
    public const PAGE_SIZE = 10;

    /**
     * @var int
     */
    protected int $currentPage;

    /**
     * @var int
     */
    protected int $pageSize;

    /**
     * @var Traversable
     */
    protected Traversable $results;

    /**
     * @var int
     */
    protected int $numResults;

    /**
     * @param int $page
     * @return self
     */
    abstract public function paginate(int $page = 1): BasePaginator;

    /**
     * @return int
     */
    final public function getCurrentPage(): int
    {
        return $this->currentPage;
    }

    /**
     * @return int
     */
    final public function getLastPage(): int
    {
        return (int) ceil($this->numResults / $this->pageSize);
    }

    /**
     * @return int
     */
    final public function getPageSize(): int
    {
        return $this->pageSize;
    }

    /**
     * @return bool
     */
    final public function hasPreviousPage(): bool
    {
        return $this->currentPage > 1;
    }

    /**
     * @return int
     */
    final public function getPreviousPage(): int
    {
        return max(1, $this->currentPage - 1);
    }

    /**
     * @return bool
     */
    final public function hasNextPage(): bool
    {
        return $this->currentPage < $this->getLastPage();
    }

    /**
     * @return int
     */
    final public function getNextPage(): int
    {
        return min($this->getLastPage(), $this->currentPage + 1);
    }

    /**
     * @return bool
     */
    final public function hasToPaginate(): bool
    {
        return $this->numResults > $this->pageSize;
    }

    /**
     * @return int
     */
    final public function getNumResults(): int
    {
        return $this->numResults;
    }

    /**
     * @return Traversable
     */
    final public function getResults(): Traversable
    {
        return $this->results;
    }
}
