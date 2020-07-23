<?php

namespace Presis\DistribuidorBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ORM\EntityRepository;

class DistribuidorType extends AbstractType
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
            ->add('codigo')
            ->add('nombre')
            ->add('apellido', null, array('required' => false))
            ->add('direccion')
            ->add('cp');
            if($user->hasRole('ROLE_SUCURSAL')){
                $builder
                    ->add('sucursal', 'genemu_jqueryselect2_entity', array(
                        'class' => 'PresisGeneralBundle:Sucursal',
                        'label' => "Sucursal",
                        'required' => true,
                        'query_builder' => function (EntityRepository $er)use($user) {
                            return $er->createQueryBuilder('u')
                                ->where('u.esPropia = TRUE')
                                ->andWhere('u.id = :id')
                                ->setParameter('id', $user->getSucursal()->getId());
                        },
                    ));
            }else{
                $builder
                    ->add('sucursal', 'genemu_jqueryselect2_entity', array(
                        'class' => 'PresisGeneralBundle:Sucursal',
                        'label' => "Sucursal",
                        'required' => false,
                        'query_builder' => function (EntityRepository $er) {
                            return $er->createQueryBuilder('u')
                                ->where('u.esPropia = TRUE');
                        },
                    ));
            }
        $builder
            ->add('localidad', null, array('required' => false))
            ->add('provincia')
            ->add('zona')
            ->add('telefono')
            ->add('dni')
            ->add('tipodni', null, array('label' => 'Tipo'))
            ->add('imei', null, array('required' => false))
            ->add('email', null, array('required' => false))
            ->add('web')
            ->add('observaciones', 'textarea', array('required' => false))
            ->add('fechaAlta', 'date', array(
                'widget' => 'single_text',
                'data' => new \DateTime("now"),
                'format' => 'dd/MM/yyyy',
                'label' => 'Alta',
                'required' => false,
            ))
            ->add('fechaBaja', 'date', array(
                'widget' => 'single_text',
                'format' => 'dd/MM/yyyy',
                'label' => 'Baja',
                'required' => false,
            ))
            ->add('vehNombre', null, array('label' => 'Nombre'))
            ->add('vehTipo', null, array('label' => 'Tipo'))
            ->add('vehMarca', null, array('label' => 'Marca'))
            ->add('vehPatente', null, array('label' => 'Patente'))
            ->add('propNombre', null, array('label' => 'Nombre'))
            ->add('propDireccion', null, array('label' => 'Dirección'))
            ->add('propTelefono', null, array('label' => 'Teléfono'))
            ->add('propEquipo', null, array('label' => 'Equipo'))
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Presis\DistribuidorBundle\Entity\Distribuidor'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'presis_distribuidorbundle_distribuidor';
    }
}
