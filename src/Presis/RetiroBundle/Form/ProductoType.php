<?php

namespace Presis\RetiroBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ProductoType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder

            ->add('peso')
            ->add('categoria')
            ->add('alto')
            ->add('largo')
            ->add('profundidad')
            ->add('formaCarga','choice',array('required'=>false, 'choices'   => array('2' => 'Peso', '1' => 'Dimensiones','3'=>'Categoria','4'=>'Dimensiones y peso'),))
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Presis\RetiroBundle\Entity\Producto'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'presis_retirobundle_producto';
    }
}
