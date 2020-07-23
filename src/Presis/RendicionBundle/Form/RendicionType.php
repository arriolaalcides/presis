<?php

namespace Presis\RendicionBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class RendicionType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('id', null, array(
                'disabled' => true,
                'label' => '# Planilla'))
            ->add('fecha', null , array(
                'widget' => 'single_text',
                'data' => new \DateTime("now"),
                'format' => 'dd/MM/yyyy',
            ))
            ->add('detalles')
            ->add('cliente')
            ->add('cerrada', 'hidden')
        ;
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Presis\RendicionBundle\Entity\Rendicion'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'presis_rendicionbundle_rendicion';
    }
}
