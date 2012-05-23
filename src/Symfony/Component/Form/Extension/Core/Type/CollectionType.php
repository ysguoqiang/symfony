<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Form\Extension\Core\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\Extension\Core\EventListener\ResizeFormListener;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CollectionType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilder $builder, array $options)
    {
        if ($options['allow_add'] && $options['prototype']) {
            $prototype = $builder->create($options['prototype_name'], $options['type'], array_replace(array(
                'label' => $options['prototype_name'] . 'label__',
            ), $options['options']));
            $builder->setAttribute('prototype', $prototype->getForm());
        }

        $resizeListener = new ResizeFormListener(
            $builder->getFormFactory(),
            $options['type'],
            $options['options'],
            $options['allow_add'],
            $options['allow_delete']
        );

        $builder->addEventSubscriber($resizeListener);
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view
            ->set('allow_add', $options['allow_add'])
            ->set('allow_delete', $options['allow_delete'])
        ;

        if ($form->getConfig()->hasAttribute('prototype')) {
            $view->set('prototype', $form->getConfig()->getAttribute('prototype')->createView($view));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function buildViewBottomUp(FormView $view, FormInterface $form, array $options)
    {
        if ($form->getConfig()->hasAttribute('prototype') && $view->get('prototype')->get('multipart')) {
            $view->set('multipart', true);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'allow_add'      => false,
            'allow_delete'   => false,
            'prototype'      => true,
            'prototype_name' => '__name__',
            'type'           => 'text',
            'options'        => array(),
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'collection';
    }
}
