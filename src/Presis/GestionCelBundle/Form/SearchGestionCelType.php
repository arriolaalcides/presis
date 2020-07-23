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

class SearchGestionCelType extends AbstractType
{

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
            ->add('ani','text',array('required' => false,'label'=>'Ani(10 caracteres)','attr' => array('maxlength' => 10)))
            ->add('nroserie','text',array('label'=>'Nro. de IMEI(15 caracteres)','required' => false,'attr' => array('maxlength' => 15)))
            ->add('nomyape','text',array('label'=>'Nombre y Apellido','required' => false,'attr' => array('maxlength' => 30)))
            ->add('nrosst','text',array('label'=>'Número SST/Ticket Remedy(13 caracteres)','required' => false,'attr' => array('maxlength' => 13)))
            //->add('nivelderep','choice',array('multiple'=>false,'required' => false,'empty_value' => false,'choices'=>array('NIVEL 1'=>'NIVEL 1', 'NIVEL 2'=>'NIVEL 2','NIVEL 3'=>'NIVEL 3','NIVEL 4'=>'NIVEL 4'),'label' => 'Nivel rep.'))
            //->add('muleto','choice',array('required' => false, 'multiple'=>false,'choices'=>array('NO'=>'NO','SI'=>'SI'),'label' => 'Muleto','data' => 'NO'))
            //->add('imeimuleto','text',array('label'=>'IMEI muleto(15 caracteres num.)','attr' => array('maxlength' => 15)))
            ->add('fabricante', 'genemu_jqueryselect2_entity', array(
                'class' => 'MovistarBundle:Fabricante',
                'label' => "Fabricante",
                'required' => false,
            ))
            ->addEventSubscriber(new AddModelosFieldSubscriber())
            ->add('estadointervencion','choice',array(
                'multiple'=>false,
                'choices'=>array(
                'REPARADO'=>'REPARADO',
                'NO REPARADO'=>'NO REPARADO',
                'IRREPARABLE'=>'IRREPARABLE',
                'EN GARANTIA'=>'EN GARANTIA',
                'VUELVE CERTIFICADO'=>'VUELVE CERTIFICADO',
            ),'label' => 'Estado de la intervención','required' => false))
            ->add('certificadoreparador','text',array('label'=>'Nro. certificado reparador(10 caracteres)','required' => false,'attr' => array('maxlength' => 10)))
            ->add('placaswap','choice',array('multiple'=>false,'required' => false,'choices'=>array('NO'=>'NO','SI'=>'SI'),'label' => 'Placa SWAP'))
            ->add('nroimei','text',array('label'=>'Nuevo número IMEI','required' => false,'attr' => array('maxlength' => 15)))
            ->add('origendelequipo','choice',array('multiple'=>false,'required' => false,'choices'=>array('MOVISTAR'=>'MOVISTAR','PRIVADO'=>'PRIVADO','MOVISTAR LIBERADO'=>'MOVISTAR LIBERADO','MOVISTAR NO CLIENTE'=>'MOVISTAR NO CLIENTE'),'label' => 'Origen del equipo'))
            ->add('sva','choice',array('multiple'=>false,'required' => false,'choices'=>array('PROTECCION MOVISTAR'=>'PROTECCION MOVISTAR', 'PROTECCION ACCURANT'=>'PROTECCION ACCURANT', 'POLITICA EMPLEADOS'=>'POLITICA EMPLEADOS','NINGUNA'=>'NINGUNA'),'label' => 'SVA'))
            /*->add('falla','choice',
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
                    'Se apaga solo' => 'Se apaga solo'
                ),'multiple' => true,'expanded' => false,'attr' => array('style' => 'height: 200px')))*/
                /*->add('completitud','choice',
                array('choices'=>array(
                    'TAPA BATERIA' => 'TAPA BATERIA',
                    'TANAPA' => 'TANAPA',
                    'MANOS LIBRES' => 'MANOS LIBRES',
                    'CARGADOR' => 'CARGADOR',
                    'LÁPIZ ÓPTICO'=>'LÁPIZ ÓPTICO',
                    'CABLE USB'=>'CABLE USB',
                    'PORTA SIM'=>'PORTA SIM'
                ),'multiple' => true,'expanded' => false,'attr' => array('style' => 'height: 200px')))*/
                ->add('rotura','choice',array('multiple'=>false,'required' => false,'choices'=>array('SI'=>'SI', 'NO'=>'NO'),'label' => 'Rotura'))
                ->add('tipocliente','choice',array('multiple'=>false,'required' => false,'choices'=>array('INDIVIDUO'=>'INDIVIDUO', 'CORPORATIVO'=>'CORPORATIVO'),'label' => 'Tipo Cliente'))
                ->add('tiposervicio','choice',array('multiple'=>false,'required' => false,
                    'choices'=>array(
                        '5 Dias'=>'5 Dias',
                        '7 Dias'=>'7 Dias',
                        'Vip'=>'Vip',
                        'Doa'=>'Doa',
                        'Falla reiterada'=>'Falla reiterada',
                        'Falla en Chequeo'=>'Falla en Chequeo',
                        'Desbloqueo SIMLOCK'=>'Desbloqueo SIMLOCK',
                        'Falla Endémica'=>'Falla Endémica',
                        'Equipo para Reciclar'=>'Equipo para Reciclar'
                    ),'label' => 'Tipo Servicio'))
                ->add('claimassurant',null,array('label'=>'Claim Assurant(10 caracteres)','required' => false,'attr' => array('maxlength' => 15),'required' => false))
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
