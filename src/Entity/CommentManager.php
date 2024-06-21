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

namespace Sonata\NewsBundle\Entity;

use Doctrine\DBAL\Exception;
use Doctrine\Persistence\ManagerRegistry;
use Sonata\Doctrine\Entity\BaseEntityManager;
use Sonata\Doctrine\Model\ManagerInterface;
use Sonata\NewsBundle\Model\{CommentInterface,CommentManagerInterface,PostInterface};
use Sonata\NewsBundle\Pagination\{BasePaginator,ORMPaginator};

class CommentManager extends BaseEntityManager implements CommentManagerInterface
{
    /**
     * @var ManagerInterface
     */
    protected ManagerInterface $postManager;

    /**
     * @param $class
     * @param ManagerRegistry $registry
     * @param ManagerInterface $postManager
     */
    public function __construct($class, ManagerRegistry $registry, ManagerInterface $postManager)
    {
        parent::__construct($class, $registry);

        $this->postManager = $postManager;
    }

    /**
     * @param $comment
     * @param $andFlush
     * @return void
     */
    public function save($comment, $andFlush = true): void
    {
        parent::save($comment, $andFlush);

        $this->updateCommentsCount($comment->getPost());
    }

    /**
     * Update the number of comment for a comment.
     * @param PostInterface|null $post
     * @throws Exception
     */
    public function updateCommentsCount(?PostInterface $post = null): void
    {
        $commentTable = $this->getObjectManager()->getClassMetadata($this->getClass());
        $postTable = $this->getObjectManager()->getClassMetadata($this->postManager->getClass());

        if (!property_exists($commentTable, 'table') || !property_exists($postTable, 'table')) {
            return;
        }

        $em = $this->getEntityManager();

        $em->getConnection()->beginTransaction();
        $em->getConnection()->executeQuery($this->getCommentsCountResetQuery($postTable->table['name']));

        $em->getConnection()->executeQuery($this->getCommentsCountQuery($postTable->table['name'], $commentTable->table['name']));

        $em->getConnection()->commit();
    }

    /**
     * @param $comment
     * @param $andFlush
     * @return void
     * @throws Exception
     */
    public function delete($comment, $andFlush = true): void
    {
        $post = $comment->getPost();

        parent::delete($comment, $andFlush);

        $this->updateCommentsCount($post);
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
        if (!isset($criteria['mode'])) {
            $criteria['mode'] = 'public';
        }

        $parameters = [];

        $query = $this->getRepository()
            ->createQueryBuilder('c')
            ->orderby('c.createdAt', 'DESC');

        if ('public' === $criteria['mode']) {
            $criteria['status'] = $criteria['status'] ?? CommentInterface::STATUS_VALID;
            $query->andWhere('c.status = :status');
            $parameters['status'] = $criteria['status'];
        }

        if (isset($criteria['postId'])) {
            $query->andWhere('c.post = :postId');
            $parameters['postId'] = $criteria['postId'];
        }

        $query->setParameters($parameters);

        return (new ORMPaginator($query))->paginate($page);
    }

    /**
     * @param string $postTableName
     *
     * @return string
     */
    private function getCommentsCountResetQuery(string $postTableName): string
    {
        return sprintf('UPDATE %s SET comments_count = 0', $postTableName);
    }

    /**
     * @param string $postTableName
     * @param string $commentTableName
     *
     * @return string
     */
    private function getCommentsCountQuery(string $postTableName, string $commentTableName): string
    {
        return sprintf(
            'UPDATE %s SET comments_count = (select COUNT(*) from %s where %s.id = %s.post_id)',
            $postTableName,
            $commentTableName,
            $postTableName,
            $commentTableName
        );
    }
}
