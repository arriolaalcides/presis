<?php

namespace Presis\DatosEnviosBundle\Form\EventListener;

use Presis\CecosBundle\CecosBundle;
use Presis\CecosBundle\Entity\CecosRepository;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\PropertyAccess\PropertyAccess;



class AddCecosFieldSubscriber implements EventSubscriberInterface
{


    public static function getSubscribedEvents()
    {
        return array(
            FormEvents::PRE_SET_DATA => 'preSetData',
            FormEvents::PRE_SUBMIT   => 'preSubmit'
        );
    }

    private function addCecoForm($form, $codcli=null,$ceco=null)
    {
        $formOptions = array(
            'class' => 'CecosBundle:Cecos',
            'required'    => false,
            'attr' => array(
                'class' => 'cecos_selector',
            ),
            'query_builder' => function (CecosRepository $repository) use ($codcli) {
                $qb = $repository->createQueryBuilder('c')
                    ->where('c.cliente = :codcli')
                    ->setParameter('codcli', $codcli)
                ;
                return $qb;
            }
        );
        $form->add('ceco', 'entity', $formOptions);
    }

    public function preSetData(FormEvent $event)
    {
        $data = $event->getData();
        $form = $event->getForm();

        if (null === $data) {
            return;
        }

        $accessor = PropertyAccess::createPropertyAccessor();

        $ceco = $accessor->getValue($data, 'ceco');
        $cliente = $accessor->getValue($data, 'cliente');

        $cliente_id = ($cliente) ? $cliente->getId() : null;

        $this->addCecoForm($form, $cliente_id);
    }

    public function preSubmit(FormEvent $event)
    {
        $data = $event->getData();
        $form = $event->getForm();

        $cliente_id = array_key_exists('cliente', $data) ? $data['cliente'] : null;

        $this->addCecoForm($form, $cliente_id);
    }
}

