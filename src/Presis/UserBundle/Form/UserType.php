<?php

namespace Presis\UserBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class UserType extends AbstractType
{

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

        if ($user->hasRole('ROLE_ADMIN')) {
            $builder
                ->add('vendedor','text', array('required' => false))
                ->add('cliente','text', array('required' => false))
                ->add('distribuidor','text', array('required' => false))
                ->add('sucursal','text', array('required' => false));
        }else{
            $builder
                ->add('cliente','text', array('required' => false))
                ->add('sucursal','text', array('required' => false));
        }
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Presis\UserBundle\Entity\User'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'presis_userbundle_user';
    }
}
