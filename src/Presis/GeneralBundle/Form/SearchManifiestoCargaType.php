<?php

namespace Presis\GeneralBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormInterface;


class SearchManifiestoCargaType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('id', 'text', array('label' => 'Nro Manifiesto'))    
            ->add('fecha')
            ->add('cliente')
            ->add('usuario')
            ->add('sucursal')
            ->add('motivo')
            ->add('estado')
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Presis\GeneralBundle\Entity\ManifiestoCarga'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'presis_generalbundle_manifiestocarga';
    }
}
