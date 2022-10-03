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

use Sonata\NewsBundle\Model\{BlogInterface,CommentManagerInterface};
use Sonata\NewsBundle\Util\HashGeneratorInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

final class ModerateCommentAction
{
    /**
     * @var RouterInterface
     */
    private RouterInterface $router;

    /**
     * @var BlogInterface
     */
    private BlogInterface $blog;

    /**
     * @var CommentManagerInterface
     */
    private CommentManagerInterface $commentManager;

    /**
     * @var HashGeneratorInterface
     */
    private HashGeneratorInterface $hashGenerator;

    /**
     * @param RouterInterface $router
     * @param BlogInterface $blog
     * @param CommentManagerInterface $commentManager
     * @param HashGeneratorInterface $hashGenerator
     */
    public function __construct(RouterInterface $router, BlogInterface $blog, CommentManagerInterface $commentManager,
        HashGeneratorInterface $hashGenerator)
    {
        $this->router = $router;
        $this->blog = $blog;
        $this->commentManager = $commentManager;
        $this->hashGenerator = $hashGenerator;
    }

    /**
     * @param string $commentId
     * @param string $hash
     * @param string $status
     * @return RedirectResponse
     * @throws AccessDeniedException
     *
     */
    public function __invoke(string $commentId, string $hash, string $status): RedirectResponse
    {
        $comment = $this->commentManager->findOneBy(['id' => $commentId]);

        if (!$comment) {
            throw new AccessDeniedException();
        }

        $computedHash = $this->hashGenerator->generate($comment);

        if ($computedHash !== $hash) {
            throw new AccessDeniedException();
        }

        $comment->setStatus($status);

        $this->commentManager->save($comment);

        return new RedirectResponse($this->router->generate('sonata_news_view', [
            'permalink' => $this->blog->getPermalinkGenerator()->generate($comment->getPost()),
        ]));
    }
}
