<?php

namespace Presis\DatosEnviosBundle\Form\EventListener;
use Presis\RemitentesBundle\Entity\RemitenteRepository;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\PropertyAccess\PropertyAccess;



class AddRemitenteFieldSubscriber implements EventSubscriberInterface
{


    public static function getSubscribedEvents()
    {
        return array(
            FormEvents::PRE_SET_DATA => 'preSetData',
            FormEvents::PRE_SUBMIT   => 'preSubmit'
        );
    }

    private function addRemitenteForm($form, $codcli=null,$remitente=null)
    {
        $formOptions = array(
            'class' => 'RemitentesBundle:Remitente',
            'required'    => false,
            'attr' => array(
                'class' => 'remitente_selector',
            ),
            'mapped' => false,
            'query_builder' => function (RemitenteRepository $repository) use ($codcli) {
                $qb = $repository->createQueryBuilder('r')
                    ->where('r.id = :codcli')
                    ->setParameter('codcli', $codcli)
                ;
                return $qb;
            }
        );
        $form->add('remitente', 'entity', $formOptions);
    }

    public function preSetData(FormEvent $event)
    {
        $data = $event->getData();
        $form = $event->getForm();

        if (null === $data) {
            return;
        }

        $accessor = PropertyAccess::createPropertyAccessor();

        $remitente = $accessor->getValue($data, 'remitente');
        $cliente = $accessor->getValue($data, 'cliente');

        $cliente_id = ($cliente) ? $cliente->getId() : null;

        $this->addRemitenteForm($form, $cliente_id);
    }

    public function preSubmit(FormEvent $event)
    {
        $data = $event->getData();
        $form = $event->getForm();

        $cliente_id = array_key_exists('cliente', $data) ? $data['cliente'] : null;

        $this->addRemitenteForm($form, $cliente_id);
    }
}

