<?php

namespace Presis\ServicioBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\DateTime;

class HabilitacionesType extends AbstractType
{
        /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder

            ->add('horaDesde')
            ->add('horaHasta')
            ->add('cordonEntrega','entity',array('class'=>'PresisServicioBundle:Cordon','property'=>'descripcion','label'=>'Cord&oacute;n de Entrega'))
            ->add('cordonRetiro','entity',array('class'=>'PresisServicioBundle:Cordon','property'=>'descripcion','label'=>'Cord&oacute;n de Retiro'))
            ->add('servicio')
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Presis\ServicioBundle\Entity\Habilitaciones'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'presis_serviciobundle_habilitaciones';
    }
}
