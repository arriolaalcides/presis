<?php

namespace Presis\RetiroBundle\Form\EventListener;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\PropertyAccess\PropertyAccess;



class AddServicioFieldSubscriber implements EventSubscriberInterface
{
    private $user;
    public function __construct($user){
        $this->user=$user;
    }

    public static function getSubscribedEvents()
    {
        return array(
            FormEvents::PRE_SET_DATA => 'preSetData',
            FormEvents::PRE_SUBMIT   => 'preSubmit'
        );
    }

    private function addServicioForm($form, $cpe=null,$sucuid=null, $servicio = null)
    {
        $formOptions = array(
            'class'         => 'PresisServicioBundle:Servicio',
            'empty_value'   => 'Servicio',
            'label'         => 'Servicio',
            'query_builder' => function($er) use($cpe,$sucuid) {
                    return $er->findHabilitados($this->user,$cpe,$sucuid);
                },
            'attr'          => array(
                'class' => 'servicio_selector',
            ),

        );

        if ($servicio) {
            $formOptions['data'] = $servicio;
        }

        $form->add('servicio','entity', $formOptions);
    }

    public function preSetData(FormEvent $event)
    {
        $data = $event->getData();
        $form = $event->getForm();

        if (null === $data) {
            return;
        }

        $accessor = PropertyAccess::getPropertyAccessor();
        $comprador=$accessor->getValue($data,'comprador');
        if ($comprador) {
            $cpe = $comprador->getCp();
        }else{
            $cpe=null;
        }
        $sucursal=$accessor->getValue($data,'sucursal');
        if ($sucursal){
            $sucuid=$sucursal->getId();
        }else{
            $sucuid=null;
        }
        $servicio=$accessor->getValue($data,'servicio');


        //$ciudad= $accessor->getValue($data, 'ciudad');
        //$country  = $accessor->getValue($data,'pais');

        $this->addServicioForm($form, $cpe, $sucuid,$servicio);
    }

    public function preSubmit(FormEvent $event)
    {
        $data = $event->getData();
        $form = $event->getForm();
        $sucu_id = array_key_exists('sucursal', $data) ? $data['sucursal'] : null;
        $comprador= array_key_exists('comprador', $data) ? $data['comprador'] : null;
        if ($comprador){
            $cpe=$comprador['cp'];
        }else{
            $cpe=null;
        }



        //var_dump($country_id);

      //  $country_id=null;
        $this->addServicioForm($form,$cpe,$sucu_id);
    }
}    
    
    