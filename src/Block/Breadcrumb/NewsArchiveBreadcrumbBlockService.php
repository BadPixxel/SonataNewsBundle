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

namespace Sonata\NewsBundle\Block\Breadcrumb;

use Knp\Menu\ItemInterface;
use Sonata\BlockBundle\Block\BlockContextInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @final since sonata-project/news-bundle 3.x
 *
 * BlockService for archive breadcrumb.
 *
 * @author Sylvain Deloux <sylvain.deloux@ekino.com>
 */
class NewsArchiveBreadcrumbBlockService extends BaseNewsBreadcrumbBlockService
{
    /**
     * @return string
     */
    public function getName(): string
    {
        return 'sonata.news.block.breadcrumb_archive';
    }

    /**
     * @param OptionsResolver $resolver
     * @return void
     */
    public function configureSettings(OptionsResolver $resolver): void
    {
        parent::configureSettings($resolver);

        $resolver->setDefaults([
            'collection' => false,
            'tag' => false,
        ]);
    }

    /**
     * @param BlockContextInterface $blockContext
     * @return ItemInterface
     */
    protected function getMenu(BlockContextInterface $blockContext): ItemInterface
    {
        $menu = $this->getRootMenu($blockContext);

        if ($collection = $blockContext->getBlock()->getSetting('collection')) {
            $menu->addChild($collection->getName(), [
                'route' => 'sonata_news_collection',
                'routeParameters' => [
                    'collection' => $collection->getSlug(),
                ],
                'extras' => [
                    'translation_domain' => false,
                ],
            ]);
        }

        if ($tag = $blockContext->getBlock()->getSetting('tag')) {
            $menu->addChild($tag->getName(), [
                'route' => 'sonata_news_tag',
                'routeParameters' => [
                    'tag' => $tag->getSlug(),
                ],
                'extras' => [
                    'translation_domain' => false,
                ],
            ]);
        }

        return $menu;
    }
}
