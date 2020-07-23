<?php

namespace Presis\ReclamoBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ReclamoType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('tipo', null, array('required' => true))
            ->add('pendiente', 'choice', array(
                'choices' => array('Si' => 'Si', 'No' => 'No')
            ))
            ->add('nro', null, array('required' => false))
            ->add('fecha', 'date', array(
                'widget' => 'single_text',
                'data' => new \DateTime("now"),
                'format' => 'dd/MM/yyyy',
                'required' => false,
            ))
            ->add('fechaLimite', 'date', array(
                'widget' => 'single_text',
                'data' => new \DateTime("now"),
                'format' => 'dd/MM/yyyy',
                'required' => false,
            ))
            ->add('fechaResuelto', 'date', array(
                'widget' => 'single_text',
                'data' => new \DateTime("now"),
                'format' => 'dd/MM/yyyy',
                'required' => false,
            ))
            ->add('resolvio', null, array('required' => false))
            ->add('direccion', null, array('required' => false))
            ->add('telefono', null, array('required' => false))
            ->add('detalle', null, array('required' => true))
            ->add('observaciones', null, array('required' => false))
            ->add('user_resolvio')
            ->add('retiro')
        ;
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Presis\ReclamoBundle\Entity\Reclamo'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'presis_reclamobundle_reclamo';
    }
}
