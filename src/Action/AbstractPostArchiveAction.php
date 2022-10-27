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

use Sonata\NewsBundle\Model\{BlogInterface,PostManagerInterface};
use Sonata\SeoBundle\Seo\SeoPageInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{Request,Response};
use Symfony\Contracts\Translation\TranslatorInterface;
use Psr\Container\ContainerInterface;

abstract class AbstractPostArchiveAction extends AbstractController
{
    /**
     * @var BlogInterface
     */
    private BlogInterface $blog;

    /**
     * @var PostManagerInterface
     */
    private PostManagerInterface $postManager;

    /**
     * @var TranslatorInterface
     */
    private TranslatorInterface $translator;

    /**
     * @var SeoPageInterface|null
     */
    private ?SeoPageInterface $seoPage;

    /**
     * @param BlogInterface $blog
     * @param PostManagerInterface $postManager
     * @param TranslatorInterface $translator
     */
    public function __construct(ContainerInterface $container,BlogInterface $blog, PostManagerInterface $postManager,
                                TranslatorInterface $translator)
    {
        parent::setContainer($container);
        $this->blog = $blog;
        $this->postManager = $postManager;
        $this->translator = $translator;
    }

    /**
     * @internal
     * @param Request $request
     * @param array $criteria
     * @param array $parameters
     * @return Response
     */
    final protected function renderArchive(Request $request, array $criteria = [], array $parameters = []): Response
    {
        $pager = $this->postManager->getPaginator(
            $criteria,
            $request->get('page', 1)
        );

        $parameters = array_merge([
            'pager' => $pager,
            'blog' => $this->blog,
            'tag' => false,
            'collection' => false,
            'route' => $request->get('_route'),
            'route_parameters' => $request->get('_route_params'),
        ], $parameters);

        $response = $this->render(
            sprintf('@SonataNews/Post/archive.%s.twig', $request->getRequestFormat()),
            $parameters
        );

        if ('rss' === $request->getRequestFormat()) {
            $response->headers->set('Content-Type', 'application/rss+xml');
        }

        if ('xml' === $request->getRequestFormat()) {
            $response->headers->set('Content-Type', 'application/xml');
        }

        return $response;
    }

    /**
     * @param SeoPageInterface|null $seoPage
     * @return void
     */
    public function setSeoPage(?SeoPageInterface $seoPage = null): void
    {
        $this->seoPage = $seoPage;
    }

    /**
     * @param string $id
     * @param array $parameters
     * @param string|null $domain
     * @param string|null $locale
     * @return string
     */
    final protected function trans(string $id, array $parameters = [], ?string $domain = 'SonataNewsBundle', string $locale = null): string
    {

        return $this->translator->trans($id, $parameters, $domain, $locale);
    }

    /**
     * @return BlogInterface
     */
    final protected function getBlog(): BlogInterface
    {
        return $this->blog;
    }


    /**
     * @return SeoPageInterface|null
     */
    final protected function getSeoPage(): ?SeoPageInterface
    {
        return $this->seoPage;
    }

    /**
     * @return PostManagerInterface
     */
    final protected function getPostManager(): PostManagerInterface
    {
        return $this->postManager;
    }
}
