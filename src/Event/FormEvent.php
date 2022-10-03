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

use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\{Request,Response};
use Symfony\Contracts\EventDispatcher\Event;

final class FormEvent extends Event
{
    /**
     * @var FormInterface
     */
    private FormInterface $form;

    /**
     * @var Request
     */
    private Request $request;

    /**
     * @var Response|null
     */
    private ?Response $response;

    /**
     * @param FormInterface $form
     * @param Request $request
     */
    public function __construct(FormInterface $form, Request $request)
    {
        $this->form = $form;
        $this->request = $request;
    }

    /**
     * @return FormInterface
     */
    public function getForm(): FormInterface
    {
        return $this->form;
    }

    /**
     * @return Request
     */
    public function getRequest(): Request
    {
        return $this->request;
    }

    /**
     * @param Response $response
     * @return void
     */
    public function setResponse(Response $response): void
    {
        $this->response = $response;
    }

    /**
     * @return Response|null
     */
    public function getResponse(): ?Response
    {
        return $this->response;
    }
}
