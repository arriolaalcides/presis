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

class RetiroType2 extends AbstractType
{
    private $securityContext;
    private $edit;
    private $empresa;


    public function __construct($securityContext,$accion,$empresa)
    {
        $this->securityContext = $securityContext;
        $this->edit=$accion;
        $this->empresa=$empresa;

    }

      /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $user = $this->securityContext->getToken()->getUser();

        $builder
            ->add('saveRemitente', 'choice', array(
                'mapped'=>false,
                'label'=>"GUARDAR",
                'expanded' => false, 
                'choices' => array(
                    'NO' => 'NO', 
                    'SI' => 'SI')
                ))
            ->add('saveDestinatario', 'choice', array(
                'mapped'=>false,
                'label'=>"GUARDAR",
                'expanded' => false, 
                'choices' => array(
                    'NO' => 'NO',
                    'SI' => 'SI')
                ));
            
            if($this->empresa=='fasttrack'){
                $builder->add('remito', 'text', array('required' => false, 'label'=> 'Guia Manual'));
            }else{
                $builder->add('remito', 'text', array('required' => false, 'label'=> 'Rto. Cliente'));
            }

            $builder->add('comprador', new CompradorType($this->empresa))
            ->add('sender', new SenderType())
        ;

        if ($this->edit=="editar"){
            $builder
                ->add('datosenvios', new \Presis\DatosEnviosBundle\Form\DatosEnviosType($this->securityContext, "editar", $this->empresa));
        }else{
            $builder
                ->add('datosenvios', new \Presis\DatosEnviosBundle\Form\DatosEnviosType($this->securityContext, "insertar", $this->empresa));
        }

        if ($this->edit=="editar"){
            $builder
                ->add('gestioncel', new \Presis\GestionCelBundle\Form\GestionCelType($this->securityContext, "editar", $this->empresa));
        }else{
            $builder
                ->add('gestioncel', new \Presis\GestionCelBundle\Form\GestionCelType($this->securityContext, "insertar", $this->empresa));
        }

        if ($this->edit=="editar"){
            $builder
                ->add('id', 'text', array('required' => false, 'label'=> 'Remito', 'disabled'=> true ));
        }

    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Presis\RetiroBundle\Entity\Retiro',
            'cascade_validation' => true, // se agrego para que tome las validaciones
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
