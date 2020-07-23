<?php

namespace Presis\RetiroBundle\Form;

use Presis\RetiroBundle\Entity\Producto;
use Presis\RetiroBundle\Form\EventListener\AddServicioFieldSubscriber;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Validator\Constraints\Collection;
use Symfony\Component\Validator\Constraints\Valid;
use Presis\DatosEnviosBundle\Form\DatosEnviosType;
class RetiroConfirmType extends AbstractType
{

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {


        $builder
            ->add('fragil', 'checkbox', array('label'=>'Env&iacute;o Fragil','required'=>false,'attr' => array('align_with_widget' => true,'data-size'=>'lg')))
            ->add('comprador', new CompradorType())
            ->add('franja')
            ->add('sucursal')
            ->add('precio',null,array('read_only'=>true))
            ->add('peso')
            ->add("rango")
			->add('cantidad')
            ->add('servicio');


        ;




    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Presis\RetiroBundle\Entity\Retiro'
        ));

    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'presis_retirobundle_retiroConfirm';
    }
}
