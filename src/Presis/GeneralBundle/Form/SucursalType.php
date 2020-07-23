<?php

namespace Presis\GeneralBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Security\Core\SecurityContext;
use Doctrine\ORM\EntityRepository;

class SucursalType extends AbstractType
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
        $user = $this->securityContext->getToken()->getUser();

        $builder

            ->add('codSuc','text',array('label' => 'C&oacute;digo'))
            ->add('descripcion','text',array('label' => 'Descripci&oacute;n'))
            ->add('calle')
            ->add('altura')
            ->add('piso')
            ->add('dpto')
            ->add('cp')
            ->add('localidad')
            ->add('provincia')
            ->add('cuit')
            ->add('mail')
            ->add('contacto')
            ->add('celular')
            ->add('kms')
            ->add('esPropia')
            ->add('razonSocial')
            ->add('otherInfo','text',array('required'=>false,'label' => 'Otra informaci&oacute;n'));
            if ($user->hasRole('ROLE_VENDEDOR')) {
                $builder->add('cliente', 'entity', array(
                    'class' => 'PresisGeneralBundle:Cliente',
                    'query_builder' => function(EntityRepository $er) {
                            return $er->createQueryBuilder('c')
                                ->where('c.vendedor=:vendedor')
                                ->setParameter('vendedor',$this->securityContext->getToken()->getUser()->getVendedor());

                        },
                ));
            }else{
                $builder->add('cliente');
            }

    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Presis\GeneralBundle\Entity\Sucursal'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'presis_generalbundle_sucursal';
    }
}
