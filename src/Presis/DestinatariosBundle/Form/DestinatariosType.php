<?php

namespace Presis\DestinatariosBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class DestinatariosType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('destinatarioCombo', 'entity',
                    array(
                        'mapped'=>false,
                        'required' => false,
                        'empty_value' => 'SELECCIONAR',
                        'class' => 'DestinatariosBundle:Destinatarios',
                        'label' => 'Destinatario',
                    ))
            ->add('codigo','text',array('required' => false))
            ->add('empresa','text',array('required' => false))
            ->add('destinatario','text',array('required' => false))
            ->add('calle','text',array('required' => false))
            ->add('altura','integer',array('required' => false))
            ->add('piso','text',array('required' => false))
            ->add('dpto','text',array('required' => false))
            ->add('localidad','text',array('required' => false))
            ->add('provincia','text',array('required' => false))
            ->add('cp','text',array('required' => false))
            ->add('celular')
            ->add('id')
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Presis\DestinatariosBundle\Entity\Destinatarios'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'presis_destinatariosbundle_destinatarios';
    }
}
