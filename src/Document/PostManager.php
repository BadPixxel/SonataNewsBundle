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

use Exception;
use Sonata\ClassificationBundle\Model\CollectionInterface;
use Sonata\Doctrine\Document\BaseDocumentManager;
use Sonata\NewsBundle\Model\{BlogInterface,PostInterface,PostManagerInterface};
use Sonata\NewsBundle\Pagination\{BasePaginator,MongoDBPaginator};

class PostManager extends BaseDocumentManager implements PostManagerInterface
{
    /**
     * @param string $permalink
     * @param BlogInterface $blog
     * @return PostInterface|null
     * @throws Exception
     */
    public function findOneByPermalink(string $permalink, BlogInterface $blog): ?PostInterface
    {
        $query = $this->getRepository()->createQueryBuilder('p');

        try {
            $urlParameters = $blog->getPermalinkGenerator()->getParameters($permalink);
        } catch (\InvalidArgumentException $exception) {
            return null;
        }

        $parameters = [];

        if (isset($urlParameters['year'], $urlParameters['month'], $urlParameters['day'])) {
            $dateQueryParts = $this->getPublicationDateQueryParts(
                sprintf('%d-%d-%d', $urlParameters['year'], $urlParameters['month'], $urlParameters['day']),
                'day'
            );

            $parameters = $dateQueryParts['params'];

            $query->andWhere($dateQueryParts['query']);
        }

        if (isset($urlParameters['slug'])) {
            $query->andWhere('p.slug = :slug');
            $parameters['slug'] = $urlParameters['slug'];
        }

        if (isset($urlParameters['collection'])) {
            $collectionQueryParts = $this->getPublicationCollectionQueryParts($urlParameters['collection']);

            $parameters = array_merge($parameters, $collectionQueryParts['params']);

            $query
                ->leftJoin('p.collection', 'c')
                ->andWhere($collectionQueryParts['query']);
        }

        if (0 === \count($parameters)) {
            return null;
        }

        $query->setParameters($parameters);

        return $query->getQuery()->getOneOrNullResult();
    }

    /**
     * @param string $date
     * @param string $step
     * @param string $alias
     * @return array
     * @throws Exception
     */
    public function getPublicationDateQueryParts(string $date, string $step, string $alias = 'p'): array
    {
        return [
            'query' => sprintf('%s.publicationDateStart >= :startDate AND %s.publicationDateStart < :endDate', $alias, $alias),
            'params' => [
                'startDate' => new \DateTime($date),
                'endDate' => new \DateTime($date.'+1 '.$step),
            ],
        ];
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
            ->createQueryBuilder('p')
            ->select('p, t')
            ->leftJoin('p.tags', 't')
            ->orderBy('p.publicationDateStart', 'DESC');

        if (!isset($criteria['enabled']) && 'public' === $criteria['mode']) {
            $criteria['enabled'] = true;
        }
        if (isset($criteria['enabled'])) {
            $query->andWhere('p.enabled = :enabled');
            $parameters['enabled'] = $criteria['enabled'];
        }

        if (isset($criteria['date'], $criteria['date']['query'], $criteria['date']['params'])) {
            $query->andWhere($criteria['date']['query']);
            $parameters = array_merge($parameters, $criteria['date']['params']);
        }

        if (isset($criteria['tag'])) {
            $query->andWhere('t.slug LIKE :tag');
            $parameters['tag'] = (string) $criteria['tag'];
        }

        if (isset($criteria['author'])) {
            if (!\is_array($criteria['author']) && stristr($criteria['author'], 'NULL')) {
                $query->andWhere('p.author IS '.$criteria['author']);
            } else {
                $query->andWhere(sprintf('p.author IN (%s)', implode(',', (array) $criteria['author'])));
            }
        }

        if (isset($criteria['collection']) && $criteria['collection'] instanceof CollectionInterface) {
            $query->andWhere('p.collection = :collectionid');
            $parameters['collectionid'] = $criteria['collection']->getId();
        }

        $query->setParameters($parameters);

        return (new MongoDBPaginator($query))->paginate($page);
    }

    /**
     * @param string $collection
     *
     * @return array
     */
    final protected function getPublicationCollectionQueryParts(string $collection): array
    {
        $queryParts = ['query' => '', 'params' => []];

        if (null === $collection) {
            $queryParts['query'] = 'p.collection IS NULL';
        } else {
            $queryParts['query'] = 'c.slug = :collection';
            $queryParts['params'] = ['collection' => $collection];
        }

        return $queryParts;
    }
}
