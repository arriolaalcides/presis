<?php

namespace Presis\FirmasBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class FirmasType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('distribuidor')
            ->add('tracking')
            ->add('img')
            ->add('codEstado')
            ->add('detalleEstado')
            ->add('recibio')
            ->add('documento')
            ->add('obs')
            ->add('fechaCel')
            ->add('fechaBase')
            ->add('distribuidorId')
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Presis\FirmasBundle\Entity\Firmas'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'presis_firmasbundle_firmas';
    }
}
