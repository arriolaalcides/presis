<?php

namespace Presis\GeneralBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ORM\EntityRepository;

class BultoExcedenteType extends AbstractType
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
            ->add('bultoExcedente','text',array('label'=>'Bulto / peso excedente'))
            ->add('costoBultoExcedente','text',array('label'=>'Costo bulto / peso excedente'));
            //->add('cliente')
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
        $builder
            ->add('servicio')
            ->add('cordonEntrega')
            ->add('cordonRetiro')
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Presis\GeneralBundle\Entity\BultoExcedente'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'presis_generalbundle_bultoexcedente';
    }
}
