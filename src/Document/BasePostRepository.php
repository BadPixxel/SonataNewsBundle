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

namespace Sonata\NewsBundle\Document;

use Doctrine\ODM\MongoDB\MongoDBException;
use Doctrine\ODM\MongoDB\Repository\DocumentRepository;

class BasePostRepository extends DocumentRepository
{
    /**
     * @param int $limit
     *
     * @return mixed
     * @throws MongoDBException
     */
    public function findLastPostQueryBuilder(int $limit): mixed
    {
        return $this->createQueryBuilder()
            ->field('enabled')->equals(true)
            ->sort('createdAt', 'desc')
            ->limit($limit)
            ->getQuery()
            ->execute();
    }
}
