<?php

namespace Presis\GestionCelBundle\Form\EventListener;

use Presis\MovistarBundle\MovistarBundle;
use Presis\MovistarBundle\Entity\ModeloRepository;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\PropertyAccess\PropertyAccess;



class AddModelosFieldSubscriber implements EventSubscriberInterface
{

    //NO SE
    public static function getSubscribedEvents()
    {
        return array(
            FormEvents::PRE_SET_DATA => 'preSetData',
            FormEvents::PRE_SUBMIT   => 'preSubmit'
        );
    }

    private function addModeloForm($form, $codfabri=null, $modelo=null)
    {
        $formOptions = array(
            'class' => 'MovistarBundle:Modelo',
            'required'    => false,
            'attr' => array(
                'class' => 'modelos_selector',
            ),
            'query_builder' => function (ModeloRepository $repository) use ($codfabri) {
                $true = '1';
                $qb = $repository->createQueryBuilder('c')
                    ->where('c.activo = :activo')
                    ->andWhere('c.fabricante = :fabricante')
                    ->setParameter('activo', $true)
                    ->setParameter('fabricante', trim($codfabri))
                ;
                return $qb;
            }
        );
        $form->add('modelo', 'entity', $formOptions);
    }

    public function preSetData(FormEvent $event)
    {
        $data = $event->getData();
        $form = $event->getForm();

        if (null === $data) {
            return;
        }

        $accessor = PropertyAccess::createPropertyAccessor();

        $modelo = $accessor->getValue($data, 'modelo');

        $fabricante = $accessor->getValue($data, 'fabricante');

        $fabricante_id = ($fabricante) ? $fabricante->getId() : null;

        $this->addModeloForm($form, $fabricante_id);
    }

    public function preSubmit(FormEvent $event)
    {

        $data = $event->getData();
        $form = $event->getForm();

        if (null === $data) {
            return;
        }

        $fabricante_id = array_key_exists('fabricante', $data) ? $data['fabricante'] : null;

        $this->addModeloForm($form, $fabricante_id);
    }
}

