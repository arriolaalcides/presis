<?php

namespace Presis\GeneralBundle\Form;

use Presis\GeneralBundle\Entity\ImpoTest;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ImpoTestType extends AbstractType
{
    /**
     * Used to populate from the constructor
     * @var ImpoTest
     */
    private $hole = null;

    public function __construct(ImpoTest $hole = null) {
        $this->hole = $hole;
    }
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('apellido')
            ->add('nombre')
            ->add('calle')
            ->add('sube','checkbox',array('mapped'=>false))
        ;
        if (!is_null($this->hole)){
            $builder->setData($this->hole);
        }
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Presis\GeneralBundle\Entity\ImpoTest'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'presis_generalbundle_impoTest';
    }
}
