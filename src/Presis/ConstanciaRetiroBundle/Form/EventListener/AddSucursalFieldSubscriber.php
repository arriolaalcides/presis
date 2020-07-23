<?php

namespace Presis\UserBundle\EventListener;

use Presis\GeneralBundle\Sucursal;
use Presis\GeneralBundle\Entity\SucursalRepository;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\PropertyAccess\PropertyAccess;



class AddSucursalFieldSubscriber implements EventSubscriberInterface
{


    public static function getSubscribedEvents()
    {
        return array(
            FormEvents::PRE_SET_DATA => 'preSetData',
            FormEvents::PRE_SUBMIT   => 'preSubmit'
        );
    }

    private function addSucuForm($form, $codcli=null,$sucursal=null)
    {

        $formOptions = array(
            'class' => 'PresisGeneralBundle:Sucursal',
            'required'    => false,
            'attr' => array(
                'class' => 'sucursal_selector',
            ),
            'query_builder' => function (SucursalRepository $repository) use ($codcli) {
                $qb = $repository->createQueryBuilder('c')
                    ->where('c.cliente = :codcli')
                    ->setParameter('codcli', $codcli)
                ;
                return $qb;
            }
        );
        $form->add('sucursal', 'entity', $formOptions);
    }

    public function preSetData(FormEvent $event)
    {
        $data = $event->getData();
        $form = $event->getForm();

        if (null === $data) {
            return;
        }

        $accessor = PropertyAccess::createPropertyAccessor();

        $sucursal = $accessor->getValue($data, 'sucursal');
        $cliente = $accessor->getValue($data, 'cliente');

        $cliente_id = ($cliente) ? $cliente->getId() : null;

        $this->addSucuForm($form, $cliente_id);
    }

    public function preSubmit(FormEvent $event)
    {
        $data = $event->getData();
        $form = $event->getForm();

        $cliente_id = array_key_exists('cliente', $data) ? $data['cliente'] : null;

        $this->addSucuForm($form, $cliente_id);
    }
}

