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

use App\Entity\SonataClassificationTag;
use Sonata\NewsBundle\Model\{BlogInterface,PostInterface,PostManagerInterface};
use Sonata\Doctrine\Model\ManagerInterface;
use Sonata\SeoBundle\Seo\SeoPageInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Doctrine\ORM\EntityManager;

final class ViewPostAction extends AbstractController
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
     * @var AuthorizationCheckerInterface
     */
    private AuthorizationCheckerInterface $authChecker;

    /**
     * @var SeoPageInterface|null
     */
    private ?SeoPageInterface $seoPage;

    /**
     * @var ManagerRegistry|null
     */
    private ?EntityManager $em;

    /**
     * @param BlogInterface $blog
     * @param PostManagerInterface $postManager
     * @param AuthorizationCheckerInterface $authChecker
     */
    public function __construct(BlogInterface $blog, PostManagerInterface $postManager, AuthorizationCheckerInterface $authChecker,
                                EntityManager  $em)
    {
        $this->blog = $blog;
        $this->postManager = $postManager;
        $this->authChecker = $authChecker;
        $this->em = $em;
    }

    /**
     * @param string $permalink
     * @return Response
     * @throws NotFoundHttpException
     */
    public function __invoke(string $permalink): Response
    {
        $post = $this->postManager->findOneByPermalink($permalink, $this->blog);

        if (!$post || !$this->isVisible($post)) {
            throw new NotFoundHttpException('Unable to find the post');
        }
        if ($seoPage = $this->seoPage) {
            $seoPage
                ->addTitle($post->getTitle())
                ->addMeta('name', 'description', $post->getAbstract())
                ->addMeta('property', 'og:title', $post->getTitle())
                ->addMeta('property', 'og:type', 'blog')
                ->addMeta('property', 'og:url', $this->generateUrl('sonata_news_view', [
                    'permalink' => $this->blog->getPermalinkGenerator()->generate($post),
                ], UrlGeneratorInterface::ABSOLUTE_URL))
                ->addMeta('property', 'og:description', $post->getAbstract());
        }

        return $this->render('@SonataNews/Post/view.html.twig', [
            'post' => $post,
            'form' => false,
            'blog' => $this->blog,
        ]);
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
     * @param PostInterface $post
     * @return bool
     */
    protected function isVisible(PostInterface $post): bool
    {
        return $post->isPublic() ||
            $this->authChecker->isGranted('ROLE_SUPER_ADMIN') ||
            $this->authChecker->isGranted('ROLE_SONATA_NEWS_ADMIN_POST_EDIT');
    }

    public function getTags(int $postId){
        $query = $this->em->getRepository(SonataClassificationTag::class)->createQueryBuilder('t');
        $query->innerJoin('App\Entity\SonataNewsPost','p');
        $query->andWhere('p.id = :id');
        $query->setParameter('id',$postId);
        return $query->getQuery()->enableResultCache()->getArrayResult();
    }

}
