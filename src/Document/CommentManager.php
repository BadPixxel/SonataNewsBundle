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
use Sonata\Doctrine\Document\BaseDocumentManager;
use Sonata\NewsBundle\Model\{CommentInterface,CommentManagerInterface,PostInterface};
use Sonata\NewsBundle\Pagination\{BasePaginator,MongoDBPaginator};

class CommentManager extends BaseDocumentManager implements CommentManagerInterface
{
    /**
     * Update the comments count.
     *
     * @param PostInterface|null $post
     * @throws MongoDBException
     */
    public function updateCommentsCount(?PostInterface $post = null): void
    {
        $post->setCommentsCount($post->getCommentsCount() + 1);
        $this->getDocumentManager()->persist($post);
        $this->getDocumentManager()->flush();
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
        $qb = $this->getDocumentManager()->getRepository($this->class)
            ->createQueryBuilder()
            ->sort('createdAt', 'desc');

        $criteria['status'] = $criteria['status'] ?? CommentInterface::STATUS_VALID;
        $qb->field('status')->equals($criteria['status']);

        if (isset($criteria['postId'])) {
            $qb->field('post')->equals($criteria['postId']);
        }

        return (new MongoDBPaginator($qb))->paginate($page);
    }
}
