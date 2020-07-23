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
use Presis\GeneralBundle\Entity\Cliente;
use Presis\GeneralBundle\Entity\Sucursal;
class GestionCelType extends AbstractType
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

        $builder
            ->add('usuario','text',array('label'=>'Usuario', 'data' => $user, 'read_only'=> true))
            ->add('ani','text',array('label'=>'Ani(10 caracteres)','attr' => array('maxlength' => 10)))
            ->add('nroserie','text',array('label'=>'Número de IMEI(15 caracteres)','attr' => array('maxlength' => 15)))
            ->add('nomyape','text',array('label'=>'Nombre y Apellido','attr' => array('maxlength' => 30)))
            ->add('nrosst','text',array('label'=>'Número SST/Ticket Remedy(13 caracteres)','attr' => array('maxlength' => 13)))
            ->add('aceptacargos','choice',array('multiple'=>false,'choices'=>array('SI'=>'SI', 'NO'=>'NO'),'label' => 'Acepta cargos'))
            ->add('nivelderep','choice',array('multiple'=>false,'choices'=>array('NIVEL 1'=>'NIVEL 1', 'NIVEL 2'=>'NIVEL 2','NIVEL 3'=>'NIVEL 3','NIVEL 4'=>'NIVEL 4'),'label' => 'Nivel máximo reparación'))
            ->add('muleto','choice',array('required' => true, 'multiple'=>false,'choices'=>array('NO'=>'NO','SI'=>'SI'),'label' => 'Muleto','data' => 'NO'))
            ->add('imeimuleto','text',array('label'=>'IMEI muleto(15 caracteres num.)','attr' => array('maxlength' => 15)))
            ->add('fechaactivacion','date',array('label'  => 'Fecha de Activación','format' => \IntlDateFormatter::SHORT,'input' => 'datetime', 'widget' => 'single_text','format' => 'dd/MM/yyyy',))
            ->add('fechafabricacion','date',array('label'  => 'Fecha de Fabricación','format' => \IntlDateFormatter::SHORT,'input' => 'datetime', 'widget' => 'single_text','format' => 'dd/MM/yyyy','required' => false))
            ->add('valordeclaradocel','text',array('label'  => 'Valor declarado','required' => false, 'read_only'=> true))
            ->add('observaciones', 'textarea', array('label' => false, 'required' => false));

            $builder
                ->add('fabricante', 'genemu_jqueryselect2_entity', array(
                    'class' => 'MovistarBundle:Fabricante',
                    'label' => "Fabricante",
                    'required' => true,
                ))
                /*->add('modelo', 'genemu_jqueryselect2_entity', array(
                    'class' => 'MovistarBundle:Modelo',
                    'label' => "Modelo",
                    'required' => false,
                ));*/
                ->addEventSubscriber(new AddModelosFieldSubscriber());

            /*if($this->accion=="insertar"){
                $builder
                    ->add('fabricante', 'genemu_jqueryselect2_entity', array(
                    'class' => 'MovistarBundle:Fabricante',
                    'label' => "Fabricante",
                    'required' => false,
                ))
                    ->addEventSubscriber(new AddModelosFieldSubscriber());
            }else{
                $builder
                    ->add('fabricante', 'text', array('label' => "Fabricante"))
                    ->add('modelo', 'text', array('label' => "Modelo"));
            }*/

            if($this->accion=="confirmar"){
                $builder
                    ->add('estadointervencion','choice',array('multiple'=>false,'choices'=>array('REPARADO'=>'REPARADO', 'NO REPARADO'=>'NO REPARADO','IRREPARABLE'=>'IRREPARABLE'),'label' => 'Estado de la intervención','required' => false))
                    ->add('certificadoreparador','text',array('label'=>'Número certificado reparador(10 caracteres)','required' => true,'attr' => array('maxlength' => 10)))
                    ->add('placaswap','choice',array('multiple'=>false,'choices'=>array('NO'=>'NO','SI'=>'SI'),'label' => 'Placa SWAP','required' => true))
                    ->add('nroimei','text',array('label'=>'Nuevo número IMEI','attr' => array('maxlength' => 15)));
            }
        $builder
            ->add('origendelequipo','choice',array('multiple'=>false,'choices'=>array('MOVISTAR'=>'MOVISTAR','PRIVADO'=>'PRIVADO','MOVISTAR LIBERADO'=>'MOVISTAR LIBERADO','MOVISTAR NO CLIENTE'=>'MOVISTAR NO CLIENTE'),'label' => 'Origen del equipo'))
            //->add('sva','choice',array('multiple'=>false,'choices'=>array('PROTECCION MOVISTAR'=>'PROTECCION MOVISTAR', 'PROTECCION ACCURANT'=>'PROTECCION ACCURANT', 'POLITICA EMPLEADOS'=>'POLITICA EMPLEADOS','NINGUNA'=>'NINGUNA'),'label' => 'SVA'))
            ->add('sva','choice',array('multiple'=>false,'choices'=>array('PROTECCION MOVISTAR'=>'PROTECCION MOVISTAR', 'POLITICA EMPLEADOS'=>'POLITICA EMPLEADOS','NINGUNA'=>'NINGUNA'),'label' => 'SVA'))
            ->add('falla','choice',
                array('choices'=>array(
                    'Mal auricular' => 'Mal auricular',
                    'Mal camara' => 'Mal camara',
                    'Mal campanilla' => 'Mal campanilla',
                    'Mal campanilla' => 'Mal campanilla',
                    'Mal display / pantalla' => 'Mal display / pantalla',
                    'Mal microfono' => 'Mal microfono',
                    'No carga' => 'No carga',
                    'No enciende' => 'No enciende',
                    'No vibra' => 'No vibra',
                    'Poca autonomia' => 'Poca autonomia',
                    'Problema de aplicacion' => 'Problema de aplicacion',
                    'Problema de conectividad (BLUETOOTH)' => 'Problema de conectividad (BLUETOOTH)',
                    'Problema de conectividad (IRDA)' => 'Problema de conectividad (IRDA)',
                    'Problema de conectividad (USB)' => 'Problema de conectividad (USB)',
                    'Problema de conectividad (WLAN)' => 'Problema de conectividad (WLAN)',
                    'Problema de Memory card' => 'Problema de Memory card',
                    'Problema de RF (No trasmite / No recibe)' => 'Problema de RF (No trasmite / No recibe)',
                    'Problema de SIM' => 'Problema de SIM',
                    'Problemas con las teclas del equipo' => 'Problemas con las teclas del equipo',
                    'Problemas de pintura / cosmetica' => 'Problemas de pintura / cosmetica',
                    'Se apaga solo' => 'Se apaga solo',
                    'Camara - No Funciona'=>'Camara - No Funciona',
                    'Campanilla - No Suena'=>'Campanilla - No Suena',
                    'Conector de Cargador (no funciona)'=>'Conector de Cargador (no funciona)',
                    'Conector Manos Libres (no funciona)'=>'Conector Manos Libres (no funciona)',
                    'Consumo'=>'Consumo',
                    'Contacto de Bateria (no funciona)'=>'Contacto de Bateria (no funciona)',
                    'Cortes,Problemas con Emision y Recepcion'=>'Cortes,Problemas con Emision y Recepcion',
                    'Display (no funciona)'=>'Display (no funciona)',
                    'Equipo Doblado / Partido'=>'Equipo Doblado / Partido',
                    'Flash (no funciona)'=>'Flash (no funciona)',
                    'Gabinetes Roto / Quebrado'=>'Gabinetes Roto / Quebrado',
                    'GPS - Problemas'=>'GPS - Problemas',
                    'Indicador de Bateria ( Muestra el simbolo de carga)'=>'Indicador de Bateria ( Muestra el simbolo de carga)',
                    'Luz de Teclado (no funciona)'=>'Luz de Teclado (no funciona)',
                    'Microfono (no funciona)'=>'Microfono (no funciona)',
                    'No Apaga'=>'No Apaga',
                    'No enciende - No completa Ciclo'=>'No enciende - No completa Ciclo',
                    'No Vibra'=>'No Vibra',
                    'Radio / TV'=>'Radio / TV',
                    'Recalienta Equipo'=>'Recalienta Equipo',
                    'Se reinicia - Se Apaga'=>'Se reinicia - Se Apaga',
                    'Se Tilda'=>'Se Tilda',
                    'Sim - No Reconoce'=>'Sim - No Reconoce',
                    'Sin Señal'=>'Sin Señal',
                    'Slot memory Dañado'=>'Slot memory Dañado',
                    'Tecla (no funciona)'=>'Tecla (no funciona)',
                    'Touch (no funciona)'=>'Touch (no funciona)',
                    'Visor'=>'Visor',
                    'WIFI'=>'WIFI',
                    'Desbloqueo Android'=>'Desbloqueo Android',
                    'Desbloqueo SIMLOCK' => 'Desbloqueo SIMLOCK'
                ),'multiple' => true,'expanded' => false,'attr' => array('style' => 'height: 200px')))
            //->add('falla','entity',array('required'=>false,'class'=>'MovistarBundle:Falla','property'=>'descripcion','expanded'=>false,'multiple'=>true,'attr' => array('style' => 'height: 200px')))
            ->add('rotura','choice',array('multiple'=>false,'choices'=>array('SI'=>'SI', 'NO'=>'NO'),'label' => 'Rotura'))
            ->add('completitud','choice',
                array('choices'=>array(
                    'TAPA BATERIA' => 'TAPA BATERIA',
                    'TANAPA' => 'TANAPA',
                    'MANOS LIBRES' => 'MANOS LIBRES',
                    'CARGADOR' => 'CARGADOR',
                    'LÁPIZ ÓPTICO'=>'LÁPIZ ÓPTICO',
                    'CABLE USB'=>'CABLE USB',
                    'PORTA SIM'=>'PORTA SIM'
                ),'multiple' => true,'expanded' => false,'attr' => array('style' => 'height: 200px')))
            //->add('completitud','entity',array('required'=>false,'class'=>'MovistarBundle:Completitud','property'=>'descripcion','expanded'=>false,'multiple'=>true,'attr' => array('style' => 'height: 200px')))
            /*->add('tipocliente', 'entity', array(
                'class' => 'MovistarBundle:TipoClienteMovistar',
                'label' => "Tipo cliente",
                'property' => 'descripcion',
                'required' => true,
            ))*/
            ->add('tipocliente','choice',array('multiple'=>false,'choices'=>array('INDIVIDUO'=>'INDIVIDUO', 'CORPORATIVO'=>'CORPORATIVO'),'label' => 'Tipo Cliente'));

            if($user->getSucursal()->getCp()>1900){
                $builder
                ->add('tiposervicio','choice',array('multiple'=>false,
                    'choices'=>array(
                        '7 Dias'=>'7 Dias',
                        'Vip'=>'Vip',
                        'Doa'=>'Doa',
                        'Falla reiterada'=>'Falla reiterada',
                        'Falla en Chequeo'=>'Falla en Chequeo',
                        'Falla Endémica'=>'Falla Endémica',
                        'Equipo para Reciclar'=>'Equipo para Reciclar'
                    ),'label' => 'Tipo Servicio'));
            }else{
                $builder
                ->add('tiposervicio','choice',array('multiple'=>false,
                    'choices'=>array(
                        '5 Dias'=>'5 Dias',
                        'Vip'=>'Vip',
                        'Doa'=>'Doa',
                        'Falla reiterada'=>'Falla reiterada',
                        'Falla en Chequeo'=>'Falla en Chequeo',
                        'Falla Endémica'=>'Falla Endémica',
                        'Equipo para Reciclar'=>'Equipo para Reciclar'
                    ),'label' => 'Tipo Servicio'));
            }

            /*->add('tiposervicio', 'entity', array(
                'class' => 'MovistarBundle:ServiciosMovistar',
                'label' => "Tipo servicio",
                'required' => true,
            ))*/
        $builder
            ->add('claimassurant', 'text' ,array('label'=>'Claim Assurant(10 caracteres)','attr' => array('maxlength' => 15),'required' => true))
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
