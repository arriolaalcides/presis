<?php

namespace Presis\DatosEnviosBundle\Form\EventListener;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\PropertyAccess\PropertyAccess;


class AddRemitenteFieldSubscriber implements EventSubscriberInterface
{
    private $cliente;

    public function __construct($cliente){
        $this->cliente=$cliente;
    }

    public static function getSubscribedEvents()
    {
        return array(
            FormEvents::PRE_SET_DATA => 'preSetData',
            FormEvents::PRE_SUBMIT   => 'preSubmit'
        );
    }

    private function addServicioForm($form, $cliente = null)
    {
        $formOptions = array(
            'class'         => 'RemitentesBundle:Remitente',
            'empty_value'   => 'Remitente',
            'label'         => 'Remitente',
            'query_builder' => function($er) use($cliente) {
                return $er->find($this->cliente);
            },
        );

        $form->add('remitente','entity', $formOptions);
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
