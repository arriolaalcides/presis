<?php

namespace Presis\TrackerBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ORM\EntityRepository;

class TrackerType extends AbstractType
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
            ->add('nroPlanilla')
            ->add('obs')
            ->add('updateTracker', 'genemu_jqueryselect2_entity', array(
                'class' => "Presis\UserBundle\Entity\User",
                'label' => "Usuario Actual",
                'required' => true,
                'query_builder' => function (EntityRepository $er)use($user) {
                    return $er->createQueryBuilder('u')
                        ->where('u.username = :username')
                        ->setParameter('username', $user->getUsername());
                },
            ))
            ->add('distribuidor', 'genemu_jqueryselect2_entity', array(
                'class' => 'DistribuidorBundle:Distribuidor',
                'label' => "Distribuidor",
                'required' => false,
                'query_builder' => function (EntityRepository $er)use($user) {
                    return $er->createQueryBuilder('u')
                        ->where('u.sucursal = :sucursal')
                        ->setParameter('sucursal', $user->getSucursal());
                },
            ))
            ->add('receptorNombre')
            ->add('receptorApellido')
            ->add('receptorFechaHora','datetime', array(
                    'widget' => "single_text",
                    'date_format'=>"yyyy-mm-dd hh:mm",
            ))
            ->add('timestampModificacion')
            ->add('estado')
             /* ->add('estado','choice',array('multiple'=>false,'choices'=>array(
                'NUEVO REMITO GENERADO EN CEC'=>'NUEVO REMITO GENERADO EN CEC',
                'EQUIPO DISPONIBLE PARA RETIRAR POR TRANSPORTE'=>'EQUIPO DISPONIBLE PARA RETIRAR POR TRANSPORTE',
                'EQUIPO EN TRASLADO AL HUB REPARADOR'=>'EQUIPO EN TRASLADO AL HUB REPARADOR',
                'INGRESO AL HUB'=>'INGRESO AL HUB',
                'FINALIZADA LA INTERVENCION'=>'FINALIZADA LA INTERVENCION',
                'EQUIPO DISPONIBLE PARA RETIRAR POR TRANSPORTE A MO'=>'EQUIPO DISPONIBLE PARA RETIRAR POR TRANSPORTE A MO',
                'EQUIPO EN TRASLADO AL CEC DE ORIGEN'=>'EQUIPO EN TRASLADO AL CEC DE ORIGEN',
                'EQUIPO RECEPCIONADO EN CEC'=>'EQUIPO RECEPCIONADO EN CEC',
                'TERMINADA OPERATORIO DE ST EN CEC'=>'TERMINADA OPERATORIO DE ST EN CEC',
                'EQUIPO ENTREGADO AL CLIENTE'=>'EQUIPO ENTREGADO AL CLIENTE'
                
                
            ),'label' => 'Estado de la intervención','required' => true))*/
            ->add('motivo')
            ->add('detalles')
            ->add('dni')
            ->add('sucursal', 'genemu_jqueryselect2_entity', array(
                'class' => 'PresisGeneralBundle:Sucursal',
                'label' => "Sucursal",
                'required' => false,
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('u')
                        ->where('u.esPropia = TRUE');
                },
            ))
        ;
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Presis\TrackerBundle\Entity\Tracker'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'presis_trackerbundle_tracker';
    }
}