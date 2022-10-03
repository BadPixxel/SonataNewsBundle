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

namespace Sonata\NewsBundle\DependencyInjection;

use Exception;
use InvalidArgumentException;
use Sonata\Doctrine\Mapper\Builder\OptionsBuilder;
use Sonata\Doctrine\Mapper\DoctrineCollector;
use Sonata\EasyExtendsBundle\Mapper\DoctrineCollector as DeprecatedDoctrineCollector;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\{ContainerBuilder,Definition};
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 *
 * @author Thomas Rabaix <thomas.rabaix@sonata-project.org>
 */
final class SonataNewsExtension extends Extension
{
    /**
     * @throws \InvalidArgumentException
     * @throws Exception
     */

    /**
     * @param array $configs
     * @param ContainerBuilder $container
     * @return void
     * @throws Exception
     * @throws InvalidArgumentException
     */
    public function load(array $configs, ContainerBuilder $container): void
    {
        $processor = new Processor();
        $configuration = new Configuration();
        $config = $processor->processConfiguration($configuration, $configs);
        $bundles = $container->getParameter('kernel.bundles');

        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('actions.xml');
        $loader->load('twig.xml');
        $loader->load('form.xml');
        $loader->load('core.xml');
        $loader->load('command.xml');

        if (isset($bundles['SonataBlockBundle'])) {
            $loader->load('block.xml');
        }

        $loader->load(sprintf('%s.xml', $config['db_driver']));

        if (isset($bundles['SonataAdminBundle'])) {
            $loader->load(sprintf('%s_admin.xml', $config['db_driver']));
        }

        if (!isset($config['salt'])) {
            throw new \InvalidArgumentException('The configuration node "salt" is not set for the SonataNewsBundle (sonata_news)');
        }

        if (!isset($config['comment'])) {
            throw new \InvalidArgumentException('The configuration node "comment" is not set for the SonataNewsBundle (sonata_news)');
        }

        $container->getDefinition('sonata.news.hash.generator')
            ->replaceArgument(0, $config['salt']);

        $container->getDefinition('sonata.news.permalink.date')
            ->replaceArgument(0, $config['permalink']['date']);

        $container->setAlias('sonata.news.permalink.generator', $config['permalink_generator']);

        $container->setDefinition('sonata.news.blog', new Definition('Sonata\NewsBundle\Model\Blog', [
            $config['title'],
            $config['link'],
            $config['description'],
            new Reference('sonata.news.permalink.generator'),
        ]));

        $container->getDefinition('sonata.news.hash.generator')
            ->replaceArgument(0, $config['salt']);

        $container->getDefinition('sonata.news.mailer')
            ->replaceArgument(5, [
                'notification' => $config['comment']['notification'],
            ]);

        if ('doctrine_orm' === $config['db_driver']) {
            if (isset($bundles['SonataDoctrineBundle'])) {
                $this->registerSonataDoctrineMapping($config);
            } else {
                // NEXT MAJOR: Remove next line and throw error when not registering SonataDoctrineBundle
                $this->registerDoctrineMapping($config);
            }
        }

        $this->configureClass($config, $container);
        $this->configureAdmin($config, $container);
    }

    /**
     * @param array $config
     * @param ContainerBuilder $container
     */
    public function configureClass(array $config, ContainerBuilder $container): void
    {
        // admin configuration
        $container->setParameter('sonata.news.admin.post.entity', $config['class']['post']);
        $container->setParameter('sonata.news.admin.comment.entity', $config['class']['comment']);

        // manager configuration
        $container->setParameter('sonata.news.manager.post.entity', $config['class']['post']);
        $container->setParameter('sonata.news.manager.comment.entity', $config['class']['comment']);
    }

    /**
     * @param array $config
     * @param ContainerBuilder $container
     */
    public function configureAdmin(array $config, ContainerBuilder $container): void
    {
        $container->setParameter('sonata.news.admin.post.class', $config['admin']['post']['class']);
        $container->setParameter('sonata.news.admin.post.controller', $config['admin']['post']['controller']);
        $container->setParameter('sonata.news.admin.post.translation_domain', $config['admin']['post']['translation']);

        $container->setParameter('sonata.news.admin.comment.class', $config['admin']['comment']['class']);
        $container->setParameter('sonata.news.admin.comment.controller', $config['admin']['comment']['controller']);
        $container->setParameter('sonata.news.admin.comment.translation_domain', $config['admin']['comment']['translation']);
    }

    /**
     * @param array $config
     * @return void
     */
    private function registerSonataDoctrineMapping(array $config): void
    {
        foreach ($config['class'] as $type => $class) {
            if (!class_exists($class)) {
                return;
            }
        }

        $collector = DoctrineCollector::getInstance();

        $collector->addAssociation(
            $config['class']['post'],
            'mapOneToMany',
            OptionsBuilder::createOneToMany('comments', $config['class']['comment'])
                ->cascade(['remove', 'persist'])
                ->mappedBy('post')
                ->orphanRemoval()
                ->addOrder('createdAt', 'DESC')
        );

        $collector->addAssociation(
            $config['class']['post'],
            'mapManyToOne',
            OptionsBuilder::createManyToOne('image', $config['class']['media'])
                ->cascade(['remove', 'persist', 'refresh', 'merge', 'detach'])
                ->addJoin([
                    'name' => 'image_id',
                    'referencedColumnName' => 'id',
                ])
        );

        $collector->addAssociation(
            $config['class']['post'],
            'mapManyToOne',
            OptionsBuilder::createManyToOne('author', $config['class']['user'])
                ->cascade(['persist'])
                ->addJoin([
                    'name' => 'author_id',
                    'referencedColumnName' => 'id',
                ])
        );

        $collector->addAssociation(
            $config['class']['post'],
            'mapManyToOne',
            OptionsBuilder::createManyToOne('collection', $config['class']['collection'])
                ->cascade(['persist'])
                ->addJoin([
                    'name' => 'collection_id',
                    'referencedColumnName' => 'id',
                ])
        );

        $collector->addAssociation(
            $config['class']['post'],
            'mapManyToMany',
            OptionsBuilder::createManyToMany('tags', $config['class']['tag'])
                ->cascade(['persist'])
                ->addJoinTable($config['table']['post_tag'], [[
                    'name' => 'post_id',
                    'referencedColumnName' => 'id',
                ]], [[
                    'name' => 'tag_id',
                    'referencedColumnName' => 'id',
                ]])
        );

        $collector->addAssociation(
            $config['class']['comment'],
            'mapManyToOne',
            OptionsBuilder::createManyToOne('post', $config['class']['post'])
                ->inversedBy('comments')
                ->addJoin([
                    'name' => 'post_id',
                    'referencedColumnName' => 'id',
                    'nullable' => false,
                ])
        );
    }
}
