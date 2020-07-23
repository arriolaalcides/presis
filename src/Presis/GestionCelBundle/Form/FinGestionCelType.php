<?php

namespace Presis\GestionCelBundle\Form;

use Presis\GestionCelBundle\Form\EventListener\AddModelosFieldSubscriber;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormInterface;

class FinGestionCelType extends AbstractType
{
    /*private $securityContext;
    private $accion;
    private $empresa;

    public function __construct($securityContext, $accion, $empresa)
    {
        $this->securityContext = $securityContext;
        $this->accion=$accion;
        $this->empresa=$empresa;
    }*/


    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        //$user = $this->securityContext->getToken()->getUser();

        $builder
            ->add('estadointervencion','choice',array('multiple'=>false,'choices'=>array(
                'REPARADO'=>'REPARADO',
                'NO REPARADO DEVUELTO'=>'NO REPARADO DEVUELTO',
                'IRREPARABLE'=>'IRREPARABLE',
                'NVF NO VERIFICA FALLAS'=>'NVF NO VERIFICA FALLAS',
                'PARA AUDITORIA CONCILIACION'=>'PARA AUDITORIA CONCILIACION',
                'VUELVE CERTIFICADO FALREP EN GARANTIA'=>'VUELVE CERTIFICADO FALREP EN GARANTIA',
                'PRESUPUESTO' => 'PRESUPUESTO'
            ),'label' => 'Estado de la intervención','required' => true))
            ->add('certificadoreparador','text',array('label'=>'Número certificado reparador(10 caracteres)','required' => true))
            ->add('placaswap','choice',array('multiple'=>false,'choices'=>array('NO'=>'NO','SI'=>'SI'),'label' => 'Placa SWAP','required' => true))
            ->add('nroimei','text',array('label'=>'Nuevo número IMEI'))
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Presis\GestionCelBundle\Entity\GestionCel'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'presis_gestioncelbundle_gestioncel';
    }
}
