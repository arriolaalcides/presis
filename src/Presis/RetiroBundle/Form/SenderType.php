<?php

namespace Presis\RetiroBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class SenderType extends AbstractType
{
        /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('empresa')
            ->add('calle')
            ->add('altura')
            ->add('piso')
            ->add('dpto')
            ->add('otherInfo')
            ->add('cp', null, array('required' => true))
            ->add('codigo')
            ->add('remitente')
            ->add('localidad')
            ->add('provincia')
            ->add('celular')
            ->add('email')
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Presis\RetiroBundle\Entity\Sender'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'presis_retirobundle_sender';
    }
}
