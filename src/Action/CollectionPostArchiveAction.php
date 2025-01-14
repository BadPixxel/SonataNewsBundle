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

namespace Sonata\NewsBundle\Action;

use Sonata\ClassificationBundle\Model\CollectionManagerInterface;
use Sonata\NewsBundle\Model\{BlogInterface,PostManagerInterface};
use Symfony\Component\HttpFoundation\{Request,Response};
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Contracts\Translation\TranslatorInterface;

final class CollectionPostArchiveAction extends AbstractPostArchiveAction
{
    /**
     * @var CollectionManagerInterface
     */
    private CollectionManagerInterface $collectionManager;

    /**
     * @param BlogInterface $blog
     * @param PostManagerInterface $postManager
     * @param TranslatorInterface $translator
     * @param CollectionManagerInterface $collectionManager
     */
    public function __construct(BlogInterface $blog, PostManagerInterface $postManager, TranslatorInterface $translator,
        CollectionManagerInterface $collectionManager)
    {
        parent::__construct($blog, $postManager, $translator);

        $this->collectionManager = $collectionManager;
    }

    /**
     * @param Request $request
     * @param string $collection
     * @return Response
     */
    public function __invoke(Request $request, string $collection): Response
    {
        $collection = $this->collectionManager->findOneBy([
            'slug' => $collection,
            'enabled' => true,
        ]);

        if (!$collection || !$collection->getEnabled()) {
            throw new NotFoundHttpException('Unable to find the collection');
        }

        if ($seoPage = $this->getSeoPage()) {
            $seoPage
                ->addTitle($this->trans('archive_collection.meta_title', [
                    '%title%' => $this->getBlog()->getTitle(),
                    '%collection%' => $collection->getName(),
                ]))
                ->addMeta('property', 'og:title', $this->trans('archive_collection.meta_title', [
                    '%title%' => $this->getBlog()->getTitle(),
                    '%collection%' => $collection->getName(),
                ]))
                ->addMeta('name', 'description', $this->trans('archive_collection.meta_description', [
                    '%title%' => $this->getBlog()->getTitle(),
                    '%collection%' => $collection->getName(),
                    '%description%' => $collection->getDescription(),
                ]))
                ->addMeta('property', 'og:description', $this->trans('archive_collection.meta_description', [
                    '%title%' => $this->getBlog()->getTitle(),
                    '%collection%' => $collection->getName(),
                    '%description%' => $collection->getDescription(),
                ]));
        }

        return $this->renderArchive($request, ['collection' => $collection], ['collection' => $collection]);
    }
}
