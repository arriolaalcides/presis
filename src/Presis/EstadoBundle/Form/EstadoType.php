<?php

namespace Presis\EstadoBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class EstadoType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nombre')
            ->add('codigo')
            ->add('delay')
            ->add('seleccionableParaRecorrido', 'checkbox', array(
                'required'  => false,
            ))
            ->add('seleccionableParaChofer', 'checkbox', array(
                'required'  => false,
            ))
            ->add('seleccionableParaWeb', 'checkbox', array(
                'required'  => false,
            ))
            ->add('paraRetiro', 'checkbox', array(
                'required'  => false,
            ))
            ->add('paraEntrega', 'checkbox', array(
                'required'  => false,
            ))
            ->add('delayretiro')
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Presis\EstadoBundle\Entity\Estado'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'presis_estadobundle_estado';
    }
}
