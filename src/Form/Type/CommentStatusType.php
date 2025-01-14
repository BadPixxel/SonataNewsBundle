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

namespace Sonata\NewsBundle\Form\Type;

use Sonata\Form\Type\BaseStatusType;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @final since sonata-project/news-bundle 3.x
 */
class CommentStatusType extends BaseStatusType
{
    /**
     * @param OptionsResolver $resolver
     * @return void
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);

        $resolver->setDefaults([
            'choice_translation_domain' => 'SonataNewsBundle',
        ]);
    }
}
