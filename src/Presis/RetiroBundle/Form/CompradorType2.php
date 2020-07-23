<?php

namespace Presis\RetiroBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class CompradorType2 extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('calle')
            ->add('apenom','text',array('label'=>'Apellido y Nombre','required'=>false,))
            ->add('altura')
            ->add('piso')
            ->add('dpto')
            ->add('email','text',array('required'=>false))
            ->add('celular')
            ->add('otherInfo','text',array('required'=>false,'label'=>'Otra Info.'))
            ->add('cp')
            ->add('localidad')
        ;
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Presis\RetiroBundle\Entity\Comprador'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'presis_retirobundle_comprador';
    }
}