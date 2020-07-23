<?php

namespace Presis\ServicioBundle\Form;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Security\Core\SecurityContext;

class PrecioType extends AbstractType
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
            ->add('rango', 'text', array('required' => true, 'label' => 'Rango'))
            ->add('precio')
            ->add('servicio')
            ->add('cordonEntrega','entity',array('class'=>'PresisServicioBundle:Cordon','property'=>'descripcion','label'=>'Cord&oacute;n de Entrega'))
            ->add('cordonRetiro','entity',array('class'=>'PresisServicioBundle:Cordon','property'=>'descripcion','label'=>'Cord&oacute;n de Retiro'));
            if ($user->hasRole("ROLE_VENDEDOR")){
                $builder->add('lista', 'entity', array(
                    'class' => 'PresisServicioBundle:Lista',
                    'query_builder' => function(EntityRepository $er) {
                            return $er->createQueryBuilder('l')
                                ->where('l.vendedor=:vendedor')
                                ->setParameter('vendedor',$this->securityContext->getToken()->getUser()->getVendedor());

                        },
                ));
            }else{
                $builder->add("lista");
            }



    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Presis\ServicioBundle\Entity\Precio'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'presis_serviciobundle_precio';
    }
}
