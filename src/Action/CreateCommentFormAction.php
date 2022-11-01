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

use Sonata\NewsBundle\Form\Type\CommentType;
use Sonata\NewsBundle\Model\{CommentManagerInterface,PostInterface,PostManagerInterface};
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\{FormFactoryInterface,FormInterface};
use Symfony\Component\HttpFoundation\Response;


final class CreateCommentFormAction extends AbstractController
{
    /**
     * @var PostManagerInterface
     */
    private PostManagerInterface $postManager;

    /**
     * @var CommentManagerInterface
     */
    private CommentManagerInterface $commentManager;

    /**
     * @var FormFactoryInterface
     */
    private FormFactoryInterface $formFactory;

    public function __construct(PostManagerInterface $postManager,
        CommentManagerInterface $commentManager, FormFactoryInterface $formFactory)
    {
        $this->postManager = $postManager;
        $this->commentManager = $commentManager;
        $this->formFactory = $formFactory;
    }

    /**
     * @param string $postId
     * @param bool $form
     *
     * @return Response
     */
    public function __invoke(string $postId, bool $form = false): Response
    {
        if (!$form) {
            $post = $this->postManager->findOneBy([
                'id' => $postId,
            ]);

            $form = $this->getCommentForm($post);
        }

        return $this->render('@SonataNews/Post/comment_form.html.twig', [
            'form' => $form->createView(),
            'post_id' => $postId,
        ]);
    }

    /**
     * @param PostInterface $post
     * @return FormInterface
     */
    private function getCommentForm(PostInterface $post): FormInterface
    {
        $comment = $this->commentManager->create();
        $comment->setPost($post);
        $comment->setStatus($post->getCommentsDefaultStatus());

        return $this->formFactory->createNamed('comment', CommentType::class, $comment, [
            'action' => $this->generateUrl('sonata_news_add_comment',[
                'id' => $post->getId()
            ]),
            'method' => 'POST',
        ]);
    }
}
