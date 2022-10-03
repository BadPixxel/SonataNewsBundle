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

namespace Sonata\NewsBundle\Controller;

use Sonata\AdminBundle\Controller\CRUDController;
use Sonata\AdminBundle\Datagrid\ProxyQueryInterface;
use Sonata\AdminBundle\Exception\ModelManagerThrowable;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class CommentAdminController extends CRUDController
{
    /**
     * @param ProxyQueryInterface $query
     * @return RedirectResponse
     * @throws ModelManagerThrowable
     */
    public function batchActionEnabled(ProxyQueryInterface $query): RedirectResponse
    {
        return $this->commentChangeStatus($query, 1);
    }

    /**
     * @param ProxyQueryInterface $query
     * @return RedirectResponse
     * @throws ModelManagerThrowable
     */
    public function batchActionDisabled(ProxyQueryInterface $query): RedirectResponse
    {
        return $this->commentChangeStatus($query, 0);
    }


    /**
     * @param ProxyQueryInterface $query
     * @param int $status
     * @return RedirectResponse
     * @throws ModelManagerThrowable
     */
    protected function commentChangeStatus(ProxyQueryInterface $query, int $status): RedirectResponse
    {
        if (false === $this->admin->isGranted('EDIT')) {
            throw new AccessDeniedException();
        }

        foreach ($query->execute() as $comment) {
            $comment->setStatus($status);

            $this->admin->getModelManager()->update($comment);
        }

        return new RedirectResponse($this->admin->generateUrl('list', $this->admin->getFilterParameters()));
    }
}
