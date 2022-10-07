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

namespace Sonata\NewsBundle\Mailer;

use Sonata\NewsBundle\Model\{BlogInterface,CommentInterface};
use Sonata\NewsBundle\Util\HashGeneratorInterface;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface as SymfonyMailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Templating\EngineInterface;
use Twig\Environment;
use Twig\Error\{LoaderError,RuntimeError,SyntaxError};

final class Mailer implements MailerInterface
{
    /**
     * @var RouterInterface
     */
    protected RouterInterface $router;

    /**
     * @var Environment
     */
    protected Environment $templating;

    /**
     * @var array
     */
    protected array $emails;

    /**
     * @var HashGeneratorInterface
     */
    protected HashGeneratorInterface $hashGenerator;

    /**
     * NEXT_MAJOR: Remove the support for `\Swift_Mailer` in this property.
     *
     * @var SymfonyMailerInterface
     */
    protected SymfonyMailerInterface $mailer;

    /**
     * @var BlogInterface
     */
    protected BlogInterface $blog;

    /**
     * @param SymfonyMailerInterface $mailer
     * @param BlogInterface $blog
     * @param HashGeneratorInterface $generator
     * @param RouterInterface $router
     * @param Environment $templating
     * @param array $emails
     */
    public function __construct(SymfonyMailerInterface $mailer, BlogInterface $blog, HashGeneratorInterface $generator, RouterInterface $router, Environment $templating, array $emails)
    {
        $this->blog = $blog;
        $this->mailer = $mailer;
        $this->hashGenerator = $generator;
        $this->router = $router;
        $this->templating = $templating;
        $this->emails = $emails;
    }

    /**
     * @param CommentInterface $comment
     * @return mixed
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws TransportExceptionInterface
     */
    public function sendCommentNotification(CommentInterface $comment): mixed
    {
        $rendered = $this->templating->render($this->emails['notification']['template'], [
            'comment' => $comment,
            'post' => $comment->getPost(),
            'hash' => $this->hashGenerator->generate($comment),
            'blog' => $this->blog,
        ]);

        $this->sendEmailMessage(
            $rendered,
            $this->emails['notification']['from'],
            $this->emails['notification']['emails']
        );
    }

    /**
     * @param string $renderedTemplate
     * @param string $fromEmail
     * @param string $toEmail
     * @throws TransportExceptionInterface
     */
    protected function sendEmailMessage(string $renderedTemplate, string $fromEmail, string $toEmail): void
    {
        // Render the email, use the first line as the subject, and the rest as the body
        [$subject, $body] = explode("\n", trim($renderedTemplate), 2);

        $email = (new Email())
                ->from($fromEmail)
                ->subject($subject)
                ->html($body);

        foreach ($this->emails['notification']['emails'] as $address) {
            $email->addTo($address);
        }

        $this->mailer->send($email);
    }
}
