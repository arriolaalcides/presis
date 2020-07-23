<?php

namespace Presis\TrackerBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ORM\EntityRepository;

class TrackerType extends AbstractType
{

    private $securityContext;

    public function __construct($securityContext)
    {
        $this->securityContext = $securityContext;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $user = $this->securityContext->getToken()->getUser();

        $builder
            //->add('distribuidor')
            ->add('distribuidor', 'genemu_jqueryselect2_entity', array(
                'class' => 'DistribuidorBundle:Distribuidor',
                'label' => "Distribuidor",
                'required' => false,
                'query_builder' => function (EntityRepository $er)use($user) {
                    return $er->createQueryBuilder('u')
                        ->where('u.sucursal = :sucursal')
                        ->setParameter('sucursal', $user->getSucursal());
                },
            ))
            ->add('receptorNombre')
            ->add('receptorApellido')
            ->add('receptorFechaHora','datetime', array(
                    'widget' => "single_text",
                    'date_format'=>"dd/mm/yyyy hh:mm",
            ))
            ->add('timestampModificacion')
            ->add('estado')
            ->add('motivo')
            ->add('detalles')
            ->add('dni')
            ->add('sucursal', 'genemu_jqueryselect2_entity', array(
                'class' => 'PresisGeneralBundle:Sucursal',
                'label' => "Sucursal",
                'required' => false,
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('u')
                        ->where('u.esPropia = TRUE');
                },
            ))
        ;
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Presis\TrackerBundle\Entity\Tracker'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'presis_trackerbundle_tracker';
    }
}