<?php

namespace Presis\ServicioBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class CpCordonType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('cp')
            ->add('partido')
            ->add('localidad')
            ->add('barrio')
            ->add('zona')
            ->add('subzona')
            ->add('provincia')
            ->add('prestador')
            ->add('kms','text',array('required' => false))
            ->add('kmsAdicionales','text',array('required' => false))
            ->add('tipoServicio', 'choice', array(
                'choices'   => array(
                    'PUERTA PUERTA' => 'PUERTA PUERTA',
                    'REDESPACHO'    => 'REDESPACHO',
                ),
                'label'    => 'Tipo Servicio',
                'required' => false
            ))
            ->add('cordon','entity',array('class'=>'PresisServicioBundle:Cordon','property'=>'descripcion','label'=>'Cord&oacute;n'))
            ->add('tiempoDeEntrega')
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Presis\ServicioBundle\Entity\CpCordon'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'presis_serviciobundle_cpcordon';
    }
}
