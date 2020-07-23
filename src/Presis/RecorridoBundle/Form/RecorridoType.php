<?php

namespace Presis\RecorridoBundle\Form;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ORM\EntityRepository;

class RecorridoType extends AbstractType
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
        ->add('sucursal', 'genemu_jqueryselect2_entity', array(
        'class' => 'PresisGeneralBundle:Sucursal',
        'label' => "Sucursal",
        'required' => false,
        'query_builder' => function (EntityRepository $er) {
            return $er->createQueryBuilder('u')
                ->where('u.esPropia = TRUE');
        },
        ))

            ->add('id', null, array(
                'disabled' => true,
                'label' => '# Planilla'))
            ->add('fecha', null , array(
                'widget' => 'single_text',
                'format' => 'dd/MM/yyyy',
            ))
            ->add('detalles')
           //->add('distribuidor', null, array('required' => true,))
           ->add('distribuidor', 'genemu_jqueryselect2_entity', array(
               'required' => true,
               'class' => 'DistribuidorBundle:Distribuidor',
               'label' => "Distribuidor",
               'query_builder' => function (EntityRepository $er) use ($user) {
                   return $er->createQueryBuilder('u')
                       ->where('u.sucursal = :sucursal')
                       ->setParameter('sucursal', $user->getSucursal());
               },

           ))
            ->add('expreso')
            ->add('colectora')
            ->add('guiaExpreso', null, array(
                'label' => '# Guía Expreso'
            ))
            ->add('cerrada', 'hidden')
            ->add('esEntrega', 'hidden')
        ;
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Presis\RecorridoBundle\Entity\Recorrido'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'presis_recorridobundle_recorrido';
    }
}
