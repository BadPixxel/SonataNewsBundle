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

use Symfony\Component\HttpFoundation\Response;

final class GetResponseCommentEvent extends CommentEvent
{
    /**
     * @var Response|null
     */
    private ?Response $response;

    /**
     * @return Response|null
     */
    public function getResponse(): ?Response
    {
        return $this->response;
    }

    /**
     * @param Response|null $response
     * @return void
     */
    public function setResponse(?Response $response): void
    {
        $this->response = $response;
    }
}
