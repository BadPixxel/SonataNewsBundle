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

use Sonata\NewsBundle\Event\{FilterCommentResponseEvent, FormEvent, GetResponseCommentEvent};
use Sonata\NewsBundle\Form\Type\CommentType;
use Sonata\NewsBundle\Mailer\MailerInterface;
use Sonata\NewsBundle\Model\{BlogInterface,
    CommentInterface,
    CommentManagerInterface,
    PostInterface,
    PostManagerInterface};
use Sonata\NewsBundle\SonataNewsEvents;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\{FormFactoryInterface, FormInterface};
use Symfony\Component\HttpFoundation\{RedirectResponse, Request, Response};
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

final class CreateCommentAction extends AbstractController
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
     * @var CommentManagerInterface
     */
    private CommentManagerInterface $commentManager;

    /**
     * @var MailerInterface
     */
    private MailerInterface $mailer;

    /**
     * @var EventDispatcherInterface
     */
    private EventDispatcherInterface $eventDispatcher;

    /**
     * @param BlogInterface $blog
     * @param PostManagerInterface $postManager
     * @param CommentManagerInterface $commentManager
     * @param MailerInterface $mailer
     * @param EventDispatcherInterface|null $eventDispatcher
     */
    public function __construct(BlogInterface $blog, PostManagerInterface $postManager, CommentManagerInterface  $commentManager,
                                MailerInterface $mailer, EventDispatcherInterface $eventDispatcher
    )
    {
        $this->blog = $blog;
        $this->postManager = $postManager;
        $this->commentManager = $commentManager;
        $this->mailer = $mailer;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @param Request $request
     * @param string $id
     * @return RedirectResponse|Response
     * @throws NotFoundHttpException
     */
    public function __invoke(Request $request, string $id): RedirectResponse|Response
    {
        $post = $this->postManager->findOneBy([
            'id' => $id,
        ]);

        if (!$post instanceof PostInterface) {
            throw new NotFoundHttpException(sprintf('Post (%d) not found', $id));
        }

        if (!$post->isCommentable()) {
            // todo : add notice in event listener
            return new RedirectResponse($this->generateUrl('sonata_news_view',[
                'permalink' => $this->blog->getPermalinkGenerator()->generate($post)
            ]));
        }

        $comment = $this->createComment($post);

        $event = new GetResponseCommentEvent($comment, $request);
        $this->eventDispatcher->dispatch($event, SonataNewsEvents::COMMENT_INITIALIZE);

        if (null !== $event->getResponse()) {
            return $event->getResponse();
        }
        $form = $this->getCommentForm($comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $event = new FormEvent($form, $request);
            $this->eventDispatcher->dispatch($event, SonataNewsEvents::COMMENT_SUCCESS);

            $comment = $form->getData();

            $this->commentManager->save($comment);
            $this->mailer->sendCommentNotification($comment);

            // todo : add notice in event listener
            $response = new RedirectResponse($this->generateUrl('sonata_news_view',[
                'permalink' => $this->blog->getPermalinkGenerator()->generate($post)
            ]));
            $this->eventDispatcher->dispatch(
                new FilterCommentResponseEvent($comment, $request, $response),
                SonataNewsEvents::COMMENT_COMPLETED
            );

            return $response;
        }

        return $this->render('@SonataNews/Post/view.html.twig', [
            'post' => $post,
            'form' => $form,
        ]);
    }

    /**
     * @param CommentInterface $comment
     * @return FormInterface
     */
    private function getCommentForm(CommentInterface $comment): FormInterface
    {
        return $this->container->get('form.factory')->createNamed('comment', CommentType::class, $comment, [
            'action' => $this->generateUrl('sonata_news_add_comment',[
                'id' => $comment->getPost()->getId()
            ]),
            'method' => 'POST'
        ]);
    }

    /**
     * @param PostInterface $post
     * @return CommentInterface
     */
    private function createComment(PostInterface $post): CommentInterface
    {
        $comment = $this->commentManager->create();
        $comment->setPost($post);
        $comment->setStatus($post->getCommentsDefaultStatus());

        return $comment;
    }
}
