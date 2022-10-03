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

use Doctrine\DBAL\Connection;
use Sonata\NewsBundle\Exception\NoDriverException;
use Sonata\NewsBundle\Pagination\BasePaginator;

/**
 * @internal
 *
 * @author Christian Gripp <mail@core23.de>
 */
final class NoDriverManager implements PostManagerInterface, CommentManagerInterface
{
    /**
     * @return string
     */
    public function getClass(): string
    {
        throw new NoDriverException();
    }

    /**
     * @return array|object[]
     */
    public function findAll(): array
    {
        throw new NoDriverException();
    }

    /**
     * @param array $criteria
     * @param array|null $orderBy
     * @param $limit
     * @param $offset
     * @return array|object[]
     */
    public function findBy(array $criteria, ?array $orderBy = null, $limit = null, $offset = null): array
    {
        throw new NoDriverException();
    }

    /**
     * @param array $criteria
     * @param array|null $orderBy
     * @return mixed|object|null
     */
    public function findOneBy(array $criteria, ?array $orderBy = null): mixed
    {
        throw new NoDriverException();
    }

    /**
     * @param mixed $id
     */
    public function find($id)
    {
        throw new NoDriverException();
    }

    /**
     * @return mixed|object
     */
    public function create(): mixed
    {
        throw new NoDriverException();
    }

    /**
     * @param object $entity
     * @param bool   $andFlush
     */
    public function save($entity, $andFlush = true)
    {
        throw new NoDriverException();
    }

    /**
     * @param object $entity
     * @param bool   $andFlush
     */
    public function delete($entity, $andFlush = true)
    {
        throw new NoDriverException();
    }

    /**
     * @return string
     */
    public function getTableName(): string
    {
        throw new NoDriverException();
    }

    /**
     * @return Connection
     */
    public function getConnection(): Connection
    {
        throw new NoDriverException();
    }

    /**
     * @param PostInterface|null $post
     * @return void
     */
    public function updateCommentsCount(?PostInterface $post = null): void
    {
    }

    /**
     * @param string $permalink
     * @param BlogInterface $blog
     * @return PostInterface|null
     */
    public function findOneByPermalink(string $permalink, BlogInterface $blog): ?PostInterface
    {
        throw new NoDriverException();
    }

    /**
     * @param string $date
     * @param string $step
     * @param string $alias
     * @return array
     */
    public function getPublicationDateQueryParts(string $date, string $step, string $alias = 'p'): array
    {
        return [];
    }

    /**
     * @param array $criteria
     * @param int $page
     * @param int $limit
     * @param array $sort
     * @return BasePaginator
     */
    public function getPaginator(array $criteria = [], int $page = 1, int $limit = 10, array $sort = []): BasePaginator
    {
        throw new NoDriverException();
    }
}
