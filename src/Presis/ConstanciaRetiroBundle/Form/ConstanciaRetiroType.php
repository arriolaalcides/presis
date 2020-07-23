<?php

namespace Presis\ConstanciaRetiroBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ORM\EntityRepository;
use Presis\GeneralBundle\Entity\Cliente;
use Presis\UserBundle\EventListener\AddSucursalFieldSubscriber;

class ConstanciaRetiroType extends AbstractType
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
                'label' => 'Fecha Retiro',
                'required' => false,
            ))
            ->add('franja', 'choice', array(
                'label' => "Horario",
                'choices'   => array('TODO EL DIA' => 'TODO EL DIA','POR LA MAÑANA' => 'POR LA MAÑANA', 'POR LA TARDE' => 'POR LA TARDE'),
                'required' => true,
            ));
        if (!$user->hasRole('ROLE_CLIENTE')) {
            $builder
                ->add('cliente', 'entity', array(
                    'class' => 'PresisGeneralBundle:Cliente',
                    'label' => "Cliente",
                    'empty_value' => '',
                ))
                ->addEventSubscriber(new AddSucursalFieldSubscriber());
        }else{
            $builder
                ->add('cliente', 'entity', array(
                    'class' => 'PresisGeneralBundle:Cliente',
                    'label' => "Cliente",
                    'empty_value' => '',
                    'query_builder' => function (EntityRepository $er) use ($user) {
                        return $er->createQueryBuilder('u')
                            ->where('u.empresa = :empresa')
                            ->setParameter('empresa', $user->getCliente()->getEmpresa());
                    },

                ));
                if($user->isUserAdmin()==false){
                    $builder->add('sucursal','text',array('required' => true, 'read_only' => true));
                }else {
                    $builder->add('sucursal', 'entity', array(
                        'class' => 'PresisGeneralBundle:Sucursal',
                        'label' => "Sucursal",
                        'empty_value' => '',
                        'query_builder' => function (EntityRepository $er) use ($user) {
                            return $er->createQueryBuilder('u')
                                ->where('u.cliente = :cliente')
                                ->setParameter('cliente', $user->getCliente());
                        },
                    ));
                }
        }
        $builder
            ->add('calle','text',array('required' => true))
            ->add('altura', 'text',array('required' => true,))
            ->add('piso', 'text',array('required' => false,))
            ->add('dpto', 'text',array('required' => false,))
            ->add('localidad', 'text',array('required' => true,))
            ->add('provincia', 'text',array('required' => true,))
            ->add('cp', 'text',array('required' => true,))
            ->add('contacto', 'text',array('required' => true,))
            ->add('telefono', 'text',array('required' => true,))
            ->add('mail', 'text',array('required' => false,))
            ->add('bultos', 'text',array('required' => true,))
            ->add('peso','text',array('required' => true,))
            ->add('observaciones', 'text',array('required' => true,))
            ->add('end', 'text',array('label'=>"Hora de corte",'required' => true,'read_only' => true))
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Presis\ConstanciaRetiroBundle\Entity\ConstanciaRetiro'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'presis_constanciaretirobundle_constanciaretiro';
    }
}
