<?php

namespace Presis\DatosEnviosBundle\Form;

use Presis\DatosEnviosBundle\Form\EventListener\AddCecosFieldSubscriber;
use Presis\DatosEnviosBundle\Form\EventListener\AddDestinatarioFieldSubscriber;
use Presis\DatosEnviosBundle\Form\EventListener\AddRemitenteFieldSubscriber;
use Presis\GestionCelBundle\Form\EventListener\AddModelosFieldSubscriber;
use Presis\ServicioBundle\Entity\Servicio;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormInterface;
use Presis\GeneralBundle\Entity\Cliente;


class DatosEnviosType extends AbstractType
{

    private $securityContext;
    private $accion;
    private $empresa;

    public function __construct($securityContext, $accion, $empresa)
    {
        $this->securityContext = $securityContext;
        $this->accion=$accion;
        $this->empresa=$empresa;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $user = $this->securityContext->getToken()->getUser();

        if ($this->accion=="buscar"){
            if (!$user->hasRole('ROLE_CLIENTE') || !$user->hasRole('ROLE_BACK_OFFICE') || !$user->hasRole('ROLE_ANALISTA_ST') || !$user->hasRole('ROLE_ANALISTA_GERENCIA_ST')) {
                $builder
                    ->add('cliente', 'genemu_jqueryselect2_entity', array(
                        'class' => 'PresisGeneralBundle:Cliente',
                        'label' => "Cliente",
                        'empty_value' => '',
                        'required' => false,
                        'mapped' => false,
                    ));
            } else {
                $builder
                    ->add('cliente', 'genemu_jqueryselect2_entity', array(
                        'class' => 'PresisGeneralBundle:Cliente',
                        'label' => "Cliente",
                        'query_builder' => function (EntityRepository $er) use ($user) {
                            return $er->createQueryBuilder('u')
                                ->where('u.empresa = :empresa')
                                ->setParameter('empresa', $user->getCliente()->getEmpresa());
                        },

                    ))
                    ->add('remitente', 'genemu_jqueryselect2_entity', array(
                        'class' => 'RemitentesBundle:Remitente',
                        /*'choices' => $user->getCliente()->getRemitentes(),*/
                        'label' => "Remitente",
                        'required' => false,
                        'mapped' => false,
                        'query_builder' => function (EntityRepository $er) use ($user) {
                            return $er->createQueryBuilder('u')
                                ->where('u.user = :user')
                                ->setParameter('user', trim($user));
                        },
                    ));
                if($user->getCliente()=='ELEPANTS'){
                    $builder->add('destinatario', 'genemu_jqueryselect2_entity', array(
                        'class' => 'DestinatariosBundle:Destinatarios',
                        'label' => "Destinatarios",
                        'property'=>'comboElepants',
                        'required' => false,
                        'mapped' => false,
                        'query_builder' => function (EntityRepository $er) use ($user) {
                            return $er->createQueryBuilder('u')
                                ->where('u.user = :user')
                                ->setParameter('user', trim($user));
                        },
                    ));
                }else{
                    $builder->add('destinatario', 'genemu_jqueryselect2_entity', array(
                        'class' => 'DestinatariosBundle:Destinatarios',
                        'label' => "Destinatarios",
                        'property'=>'comboDefault',
                        'required' => false,
                        'mapped' => false,
                        'query_builder' => function (EntityRepository $er) use ($user) {
                            return $er->createQueryBuilder('u')
                                ->where('u.user = :user')
                                ->setParameter('user', trim($user));
                        },
                    ));
                }
                    /*->add('destinatario', 'genemu_jqueryselect2_entity', array(
                        'class' => 'DestinatariosBundle:Destinatarios',
                        'label' => "Destinatarios",
                        'required' => false,
                        'mapped' => false,
                        'query_builder' => function (EntityRepository $er) use ($user) {
                            return $er->createQueryBuilder('u')
                                ->where('u.user = :user')
                                ->setParameter('user', trim($user));
                        },
                    ));*/
            }
            $builder
                ->add('fecha', 'date', array(
                    'mapped' => false,
                    'label' => 'F. remito desde',
                    'format' => \IntlDateFormatter::SHORT,
                    'input' => 'datetime',
                    'widget' => 'single_text',
                    //'data' => new \DateTime("now"),
                    'format' => 'dd/MM/yyyy',
                    'required' => false
                ))
                ->add('hasta', 'date', array(
                    'mapped' => false,
                    'label' => 'F. remito hasta',
                    'format' => \IntlDateFormatter::SHORT,
                    'input' => 'datetime',
                    'widget' => 'single_text',
                    //'data' => new \DateTime("now"),
                    'format' => 'dd/MM/yyyy',
                    'required' => false))
                ->add('nroCta', 'text', array('required' => false, 'label' => 'Nro. Cta.'))
                //->add('valorDeclarado', 'text', array('required' => false))
                ->add('guiaAgente', 'text', array('label' => "Guia Agente", 'required' => false, 'mapped' => false))
                ->add('facturado', 'choice', array(
                    'label' => "Facturado",
                    'choices'   => array('1' => 'SI', '0' => 'NO'),
                    'required' => false,
                    'mapped' => false
                ))
                ->add('cordonOrigen', 'entity', array(
                    'class' => 'PresisServicioBundle:Cordon',
                    'label' => "C.Origen",
                    'required' => false,
                    'mapped' => false
                ))
                ->add('cordonDestino', 'entity', array(
                    'class' => 'PresisServicioBundle:Cordon',
                    'label' => "C.Destino",
                    'required' => false,
                    'mapped' => false
                ))
                ->add('contrareembolso', 'choice', array(
                    'label' => "Con Contrareem.",
                    'choices'   => array('1' => 'SI', '0' => 'NO'),
                    'required' => false,
                    'mapped' => false
                ));
        }else{
            //GENERAR GUIAS CON ROL CLIENTE
            if ($user->hasRole('ROLE_CLIENTE') || $user->hasRole('ROLE_BACK_OFFICE') || $user->hasRole('ROLE_ANALISTA_ST') || $user->hasRole('ROLE_ANALISTA_GERENCIA_ST') || $user->hasRole('ROLE_UNIR')) {
                $builder
                    ->add('cliente', 'genemu_jqueryselect2_entity', array(
                        'class' => 'PresisGeneralBundle:Cliente',
                        'label' => "Cliente",
                        'required' => false,
                        'query_builder' => function (EntityRepository $er) use ($user) {
                            return $er->createQueryBuilder('u')
                                ->where('u.empresa = :empresa')
                                ->setParameter('empresa', $user->getCliente()->getEmpresa());
                        },
                    ));
                    // 12-04-2017 PICCINI SACO POR EL EDIT DE MAS LOGISTICA
                   //if($this->empresa!='maslogistica'){
                       $builder->add('remitente', 'genemu_jqueryselect2_entity', array(
                           'class' => 'RemitentesBundle:Remitente',
                           'label' => "Remitente",
                           'required' => false,
                           'mapped' => false,
                           'query_builder' => function (EntityRepository $er) use ($user) {
                               return $er->createQueryBuilder('u')
                                   ->where('u.user = :user')
                                   ->setParameter('user', trim($user));
                           },
                       ));
                if($user->getCliente()=='ELEPANTS'){
                    $builder->add('destinatario', 'genemu_jqueryselect2_entity', array(
                        'class' => 'DestinatariosBundle:Destinatarios',
                        'label' => "Destinatarios",
                        'property'=>'comboElepants',
                        'required' => false,
                        'mapped' => false,
                        'query_builder' => function (EntityRepository $er) use ($user) {
                            return $er->createQueryBuilder('u')
                                ->where('u.user = :user')
                                ->setParameter('user', trim($user));
                        },
                    ));
                }else{
                    $builder->add('destinatario', 'genemu_jqueryselect2_entity', array(
                        'class' => 'DestinatariosBundle:Destinatarios',
                        'label' => "Destinatarios",
                        'property'=>'comboDefault',
                        'required' => false,
                        'mapped' => false,
                        'query_builder' => function (EntityRepository $er) use ($user) {
                            return $er->createQueryBuilder('u')
                                ->where('u.user = :user')
                                ->setParameter('user', trim($user));
                        },
                    ));
                }
                           /*->add('destinatario', 'genemu_jqueryselect2_entity', array(
                               'class' => 'DestinatariosBundle:Destinatarios',
                               'label' => "Destinatarios",
                               'required' => false,
                               'mapped' => false,
                               'query_builder' => function (EntityRepository $er) use ($user) {
                                   return $er->createQueryBuilder('u')
                                       ->where('u.user = :user')
                                       ->setParameter('user', trim($user));
                               },
                           ));*/
                   //}
                    $builder->add('debeRetirarse', 'checkbox', array('label' => 'Debe retirarse', 'required' => false,));
            }else {
                $builder
                    ->add('cliente', 'genemu_jqueryselect2_entity', array(
                        'class' => 'PresisGeneralBundle:Cliente',
                        'label' => "Cliente",
                        'required' => false,
                    ));
            }
            if(($user->hasRole('ROLE_ADMIN'))||($this->empresa=='maslogistica')){
                /****************************************************************/
                $builder->add('remitente', 'genemu_jqueryselect2_entity', array(
                    'class' => 'RemitentesBundle:Remitente',
                    'label' => "Remitente",
                    'required' => false,
                    'mapped' => false,
                    'query_builder' => function (EntityRepository $er) use ($user) {
                        return $er->createQueryBuilder('u')
                            ->where('u.user = :user')
                            ->setParameter('user', trim($user));
                    },
                ));

                    if($user->getCliente()=='ELEPANTS'){
                        $builder->add('destinatario', 'genemu_jqueryselect2_entity', array(
                            'class' => 'DestinatariosBundle:Destinatarios',
                            'label' => "Destinatarios",
                            'property'=>'comboElepants',
                            'required' => false,
                            'mapped' => false,
                            'query_builder' => function (EntityRepository $er) use ($user) {
                                return $er->createQueryBuilder('u')
                                    ->where('u.user = :user')
                                    ->setParameter('user', trim($user));
                            },
                        ));
                    }else{
                        $builder->add('destinatario', 'genemu_jqueryselect2_entity', array(
                            'class' => 'DestinatariosBundle:Destinatarios',
                            'label' => "Destinatarios",
                            'property'=>'comboDefault',
                            'required' => false,
                            'mapped' => false,
                            'query_builder' => function (EntityRepository $er) use ($user) {
                                return $er->createQueryBuilder('u')
                                    ->where('u.user = :user')
                                    ->setParameter('user', trim($user));
                            },
                        ));
                    }

                /****************************************************************/
                $builder
                    ->add('totalFlete', 'text', array('required' => false, 'label' => 'Costo total', 'read_only' => true))
                    ->add('fechaPactada', 'date', array(
                        'format' => \IntlDateFormatter::SHORT,
                        'input' => 'datetime',
                        'widget' => 'single_text',
                        'required' => false,
                        'read_only' => true,
                        'format' => 'dd/MM/yyyy',
                    ))
                    ->add('custodia', 'text', array('required' => false, 'label' => 'Custodia'))
                    ->add('contrareembolso', 'text', array('required' => false))
                    ->add('costoPorContrareembolso', 'text', array('label' => 'Concepto contrareembolso', 'required' => false, 'read_only' => true))
                    ->add('flete', 'text', array('required' => false, 'read_only' => true))
                    ->add('seguro', 'text', array('label' => 'Seguro', 'required' => false, 'read_only' => true))
                    ->add('seguroManual', null, array('label' => 'Editar'))
                    ->add('contrareembolsoManual', null, array('label' => 'Editar'))
                    ->add('src', 'checkbox', array('label' => 'Editar', 'required' => false))
                    ->add('costoPorRemitoConforme', null, array('label' => 'Servicio Remito Conforme', 'read_only' => true))
                    ->add('costoDespachoAExpreso')
                    ->add('costoPorMonitoreoActivo')
                    ->add('costoAdicional1', null, array('label' => 'Costo adicional'))
                    ->add('detalleCostoAdicional1', null, array('label' => 'Detalle'))
                    ->add('costoAdicional2', null, array('label' => 'Costo adicional'))
                    ->add('detalleCostoAdicional2', null, array('label' => 'Detalle'))
                    ->add('costoAdicional3', null, array('label' => 'Costo adicional'))
                    ->add('detalleCostoAdicional3', null, array('label' => 'Detalle'))
                    ->add('custodia')
                    ->add('montoGuiaWeb', 'text', array('required' => false, 'read_only' => true))
                    ->add('debeRetirarse', 'checkbox', array('label' => 'Debe retirarse', 'required' => false,));
                if($this->empresa!='fasttrack'){
                    $builder->add('pesoVolumetrico', 'text', array('required' => false, 'label' => 'Volumetrico', 'read_only' => true));
                    $builder->add('pagoEn', 'choice', array(
                        'label' => " ",
                        'choices'   => array('DESTINO' => 'Destino', 'ORIGEN' => 'Origen'),
                        'required' => true
                    ));
                }
            }
            /*===============================FIN FORM CARGA USUARIO=====================================*/
            $builder
                ->add('debeRetirarse', 'checkbox', array('label' => 'Debe retirarse', 'required' => false,))
                ->add('fecha', 'date', array(
                    'format' => \IntlDateFormatter::SHORT,
                    'input' => 'datetime',
                    'widget' => 'single_text',
                    //'data' => new \DateTime("now"),
                    'format' => 'dd/MM/yyyy',
                ))
                ->add('guiaAgente', 'text', array('label' => "Guia Agente", 'required' => false))
                ->add('sucursalCabecera', 'text', array('label' => "Sucursal", 'required' => false));

            if($this->empresa!='fasttrack') {
                $builder
                    ->add('cobrado', 'checkbox', array('label' => 'Cobrado', 'required' => false, 'attr' => array('disabled' => 'disabled')));

            }
            $builder
                ->add('nroCta', 'text', array('required' => false, 'label' => 'Nro. Cta.', 'read_only' => true))
                ->add('ts', 'choice',
                    array('label' => false,
                        'required' => true,
                        'expanded' => true,
                        'data' => 0,
                        'choices' => array(
                            '0' => 'Express',
                            '1' => 'Cargas')
                    ))
                ->add('tipoOp', 'choice', array('label' => 'Tipo Operacion', 'expanded' => false, 'multiple' => false,
                    'choices' => array(
                        '0' => 'ENVIO',
                        '1' => 'RETIRO',
                    )))

                ->add('docSpx', 'choice', array('label' => 'DOC.SPX', 'expanded' => false, 'multiple' => false,
                    'choices' => array(
                        'DOC' => 'DOC',
                        'SPX' => 'SPX',
                    )))
                ->add('valorDeclarado', 'text', array('required' => false));
            if ($this->accion == "editar") {

                if($this->empresa=='caktus'){
                    $builder
                        ->addEventSubscriber(new AddCecosFieldSubscriber());
                }

                if($this->empresa!='fasttrack'){
                    $builder->add('pesoVolumetrico', 'text', array('required' => false, 'label' => 'Volumetrico', 'read_only' => true));

                    $builder->add('pagoEn', 'choice', array(
                        'label' => " ",
                        'choices'   => array('DESTINO' => 'Destino', 'ORIGEN' => 'Origen'),
                        'required' => true
                    ));
                }

                $builder
                    ->add('contrareembolso', 'text', array('required' => false))
                    ->add('bultos', 'text', array('required' => false))
                    ->add('alto', 'text', array('required' => false, 'label' => 'Alto (cm)'))
                    ->add('ancho', 'text', array('required' => false, 'label' => 'Ancho (cm)'))
                    ->add('largo', 'text', array('required' => false, 'label' => 'Largo (cm)'))
                    ->add('volumen', 'text', array('label' => 'Peso final', 'required' => true, 'read_only' => true))
                    ->add('peso', null, array('required' => true))
                    ->add('formaPago', 'text', array('mapped' => false, 'label' => "Condicion", 'required' => false, 'read_only' => true))
                    ->add('csr', 'checkbox', array('label' => 'CSR', 'required' => false,))
                    ->add('observaciones', 'textarea', array('label' => false, 'required' => false));

                $formModifier = function (FormInterface $form, Cliente $cliente = null) {
                    $servicios = null === $cliente ? array() : $cliente->getServicios();

                    $form->add('servicio', 'entity', array(
                        'class'       => 'PresisServicioBundle:Servicio',
                        'empty_value' => '',
                        'choices'     => $servicios,
                    ));
                };

                $builder->addEventListener(
                    FormEvents::PRE_SET_DATA,
                    function (FormEvent $event) use ($formModifier) {
                        $data = $event->getData();

                        $formModifier($event->getForm(), $data->getCliente());
                    }
                );

                $builder->get('cliente')->addEventListener(
                    FormEvents::POST_SUBMIT,
                    function (FormEvent $event) use ($formModifier) {
                        $cliente = $event->getForm()->getData();

                        $formModifier($event->getForm()->getParent(), $cliente);
                    }
                );
            } else {

                if($this->empresa=='caktus'){
                    $builder
                        ->addEventSubscriber(new AddCecosFieldSubscriber());
                }
                $builder

                    ->add('peso', null, array('required' => true))
                    ->add('contrareembolso', null, array('required' => false))
                    ->add('csr', 'checkbox', array('label' => 'CSR', 'required' => false,))
                    ->add('observaciones', 'textarea', array('label' => false, 'required' => false))
                    ->add('formaPago', 'text', array('mapped' => false, 'label' => "Condicion", 'required' => false, 'read_only' => true))
                    ->add('volumen', 'text', array('label' => 'Peso final', 'required' => true, 'read_only' => true))
                    ->add('bultos', 'text', array('required' => true, 'data' => '0'))
                    ->add('alto', 'text', array('required' => false, 'label' => 'Alto (cm)'))
                    ->add('ancho', 'text', array('required' => false, 'label' => 'Ancho (cm)'))
                    ->add('largo', 'text', array('required' => false, 'label' => 'Largo (cm)'));

                $formModifier = function (FormInterface $form, Cliente $cliente = null) {
                    $servicios = null === $cliente ? array() : $cliente->getServicios();

                    $form->add('servicio', 'entity', array(
                        'class'       => 'PresisServicioBundle:Servicio',
                        'empty_value' => '',
                        'choices'     => $servicios,
                    ));
                };

                $builder->addEventListener(
                    FormEvents::PRE_SET_DATA,
                    function (FormEvent $event) use ($formModifier) {
                        $data = $event->getData();

                        $formModifier($event->getForm(), $data->getCliente());
                    }
                );

                $builder->get('cliente')->addEventListener(
                    FormEvents::POST_SUBMIT,
                    function (FormEvent $event) use ($formModifier) {
                        $cliente = $event->getForm()->getData();

                        $formModifier($event->getForm()->getParent(), $cliente);
                    }
                );

                /*=================================================================*/

            }
        }
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Presis\DatosEnviosBundle\Entity\DatosEnvios'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'presis_datosenviosbundle_datosenvios';
    }
}