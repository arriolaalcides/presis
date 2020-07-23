<?php

namespace Presis\GeneralBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class PesoRangoExcedenteType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('rangoHasta')
            ->add('costoRangoExcedente')
            ->add('cliente')
            ->add('servicio')
            ->add('cordonEntrega')
            ->add('cordonRetiro')
            ->add('tipoServicio', 'choice', array(
                'choices'   => array(
                    'PUERTA PUERTA' => 'PUERTA PUERTA',
                    'REDESPACHO'    => 'REDESPACHO',
                ),
                'label'    => 'Tipo Servicio',
                'required' => false
            ))
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Presis\GeneralBundle\Entity\PesoRangoExcedente'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'presis_generalbundle_pesorangoexcedente';
    }
}
