<?php

namespace Presis\ConstanciaRetiroBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ORM\EntityRepository;
use Presis\GeneralBundle\Entity\Cliente;
use Presis\UserBundle\EventListener\AddSucursalFieldSubscriber;
class RetirosFijosType extends AbstractType
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
            ->add('usuario','text',array('read_only' => true))
            ->add('fecha', 'date', array(
                'widget' => 'single_text',
                'data' => new \DateTime("now"),
                'format' => 'dd/MM/yyyy',
                'label' => 'Fecha',
                'required' => false,
                'read_only' => true
            ))
            ->add('franja', 'choice', array(
                'label' => "Horario",
                'choices'   => array('TODO EL DIA' => 'TODO EL DIA','POR LA MAÑANA' => 'POR LA MAÑANA', 'POR LA TARDE' => 'POR LA TARDE'),
            ));
            $builder
                ->add('cliente', 'entity', array(
                    'class' => 'PresisGeneralBundle:Cliente',
                    'label' => "Cliente",
                    'empty_value' => '',
                ))
                ->addEventSubscriber(new AddSucursalFieldSubscriber());
        $builder
            ->add('calle','text',array('required' => true))
            ->add('altura', 'text',array('required' => true,))
            ->add('piso','text',array('required' => false))
            ->add('dpto','text',array('required' => false))
            ->add('localidad','text',array('required' => true))
            ->add('provincia','text',array('required' => true))
            ->add('cp','text',array('required' => true))
            ->add('contacto','text',array('required' => true))
            ->add('telefono','text',array('required' => true))
            ->add('mail','text',array('required' => false))
            ->add('observaciones')
            ->add('dias', 'choice', array(
                'required' => true,
                     'choices' => array(
                         'Domingo' => 'Domingo',
                         'Lunes' => 'Lunes',
                         'Martes'=> 'Martes',
                         'Miercoles'=> 'Miercoles',
                         'Jueves'=> 'Jueves',
                         'Viernes'=> 'Viernes',
                         'Sabado'=> 'Sabado'
                     ),
                     'multiple' => true,
                     'expanded' => true,
                 )
             );
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Presis\ConstanciaRetiroBundle\Entity\RetirosFijos'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'presis_constanciaretirobundle_retirosfijos';
    }
}
