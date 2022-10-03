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

namespace Sonata\NewsBundle\Event;

use Sonata\NewsBundle\Model\CommentInterface;
use Symfony\Component\HttpFoundation\{Request,Response};

final class FilterCommentResponseEvent extends CommentEvent
{
    /**
     * @var Response
     */
    private Response $response;

    /**
     * @param CommentInterface $comment
     * @param Request $request
     * @param Response $response
     */
    public function __construct(CommentInterface $comment, Request $request, Response $response)
    {
        parent::__construct($comment, $request);

        $this->response = $response;
    }

    /**
     * @return Response
     */
    public function getResponse(): Response
    {
        return $this->response;
    }

    /**
     * @param Response $response
     * @return void
     */
    public function setResponse(Response $response): void
    {
        $this->response = $response;
    }
}
