<?php

namespace Presis\DatosEnviosBundle\Form\EventListener;
use Presis\DestinatariosBundle\Entity\DestinatariosRepository;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\PropertyAccess\PropertyAccess;



class AddDestinatarioFieldSubscriber implements EventSubscriberInterface
{


    public static function getSubscribedEvents()
    {
        return array(
            FormEvents::PRE_SET_DATA => 'preSetData',
            FormEvents::PRE_SUBMIT   => 'preSubmit'
        );
    }

    private function addDestinatarioForm($form, $codcli=null,$remitente=null)
    {
        $formOptions = array(
            'class' => 'DestinatariosBundle:Destinatarios',
            'required'    => false,
            'attr' => array(
                'class' => 'destinatario_selector',
            ),
            'mapped' => false,
            'query_builder' => function (DestinatariosRepository $repository) use ($codcli) {
                $qb = $repository->createQueryBuilder('d')
                    ->where('d.id = :codcli')
                    ->setParameter('codcli', $codcli)
                ;
                return $qb;
            }
        );
        $form->add('destinatario', 'entity', $formOptions);
    }

    public function preSetData(FormEvent $event)
    {
        $data = $event->getData();
        $form = $event->getForm();

        if (null === $data) {
            return;
        }

        $accessor = PropertyAccess::createPropertyAccessor();

        $destinatario = $accessor->getValue($data, 'destinatario');
        $cliente = $accessor->getValue($data, 'cliente');
        $cliente_id = ($cliente) ? $cliente->getId() : null;
        $this->addDestinatarioForm($form, $cliente_id);
    }

    public function preSubmit(FormEvent $event)
    {
        $data = $event->getData();
        $form = $event->getForm();
        $cliente_id = array_key_exists('cliente', $data) ? $data['cliente'] : null;
        $this->addDestinatarioForm($form, $cliente_id);
    }
}

