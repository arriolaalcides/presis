<?php

namespace Presis\ServicioBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Security\Core\SecurityContext;

class ListaType extends AbstractType
{
    private $securityContext;

    public function __construct(SecurityContext $securityContext)
    {
        $this->securityContext = $securityContext;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
            ->add('descripcion','text',array('label'=>'Descripci&oacute;n'))
            ->add('isGeneral',null,array('label'=>'Es lista general','required'=>false))

        ;
        $user = $this->securityContext->getToken()->getUser();
        if ($user->hasRole("ROLE_VENDEDOR")) {
            /*$builder->add('cliente', 'entity', array(
                'class' => 'PresisGeneralBundle:Cliente',
                'choices' => $user->getVendedor()->getClientes(),
            ));*/
        }else{
            //$builder->add('cliente');
        }
        if ($user->hasRole("ROLE_VENDEDOR")) {
            $builder ->add('vendedor','entity',array('data'=>$user->getVendedor(),'class'=>'PresisGeneralBundle:Vendedor','property'=>'nombre','expanded'=>false,'multiple'=>false,'attr'=>array('disabled'=>true)));
        }else{
            $builder->add('vendedor');
        }

    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Presis\ServicioBundle\Entity\Lista'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'presis_serviciobundle_lista';
    }
}
