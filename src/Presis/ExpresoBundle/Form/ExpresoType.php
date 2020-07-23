<?php

namespace Presis\ExpresoBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ExpresoType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('codigo')
            ->add('nombre')
            ->add('direccion')
            ->add('localidad')
            ->add('provincia')
            ->add('telefono')
            ->add('telefono2')
            ->add('cp')
            ->add('mail')
            ->add('web')
            ->add('cuit')
            ->add('encargado')
            ->add('horario')
            ->add('codigos')
            ->add('alta', null, array(
                'widget' => 'single_text'
            ))
            ->add('estado')
            ->add('medio')
            ->add('descripcionServicio')
            ->add('otrosDatos')
            ->add('fechaAlta', 'date', array(
                'widget' => 'single_text',
                'format' => 'dd/MM/yyyy',
                'label' => 'Alta',
                'required' => false,
            ))
            ->add('const')
            ->add('zonasAtencion')
            ->add('contacto')
            ->add('finishing')
            ->add('ip')
            ->add('codigoEmpleado')
            ->add('empleado')
            ->add('sucursal')
            ->add('esExpreso')
            ->add('zona')
            ->add('cuentaCorriente')
        ;
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Presis\ExpresoBundle\Entity\Expreso'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'presis_expresobundle_expreso';
    }
}
