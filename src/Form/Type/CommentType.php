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

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\{EmailType,TextareaType,TextType,UrlType};
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @final since sonata-project/news-bundle 3.x
 */
class CommentType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     * @return void
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'form.comment.name',
            ])
            ->add('email', EmailType::class, [
                'required' => false,
                'label' => 'form.comment.email',
            ])
            ->add('url', UrlType::class, [
                'required' => false,
                'label' => 'form.comment.url',
            ])
            ->add('message', TextareaType::class, [
                'label' => 'form.comment.message',
            ]);
    }

    /**
     * @return string
     */
    public function getBlockPrefix(): string
    {
        return 'sonata_post_comment';
    }

    /**
     * @param OptionsResolver $resolver
     * @return void
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'translation_domain' => 'SonataNewsBundle',
        ]);
    }
}
