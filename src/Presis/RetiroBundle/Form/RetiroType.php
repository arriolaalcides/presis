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

class RetiroType extends AbstractType
{
    private $securityContext;

    public function __construct($securityContext)
    {
        $this->securityContext = $securityContext;
    }
        /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $user = $this->securityContext->getToken()->getUser();
        
        $builder
            ->add('fragil', 'checkbox', array('label'=>'Env&iacute;o Fragil','required'=>false,'attr' => array('align_with_widget' => true,'data-size'=>'lg')))
            ->add('comprador', new CompradorType2())

            ->add('sucursal', 'entity', array(
                'class' => 'PresisGeneralBundle:Sucursal',
                'choices' => $user->getCliente()->getSucursales(),
                'label' => "Sucursal de retiro",
            ))
            ->addEventSubscriber(new AddServicioFieldSubscriber($user))
    
            ->add('franja', 'entity', array(
                'class' => 'PresisRetiroBundle:FranjaEntrega',

            ))
			->add('cantidad')
            ->addEventSubscriber(new AddServicioFieldSubscriber($user))
            ->add('serviDesc','text',array('mapped' => false,'read_only'=>true,'label'=>'Descripci&oacute;n del servicio'))
            ->add('productos', 'collection', array(
                'required'  =>true,
                'label'         =>'',
                'type'           => new ProductoType(),
                'label'          => 'Agregar Productos',
                'by_reference'   => false,
                'prototype' => true,
                'allow_delete'   => true,
                'allow_add'      => true,


            ))

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
        return 'presis_retirobundle_retiro';
    }
}
