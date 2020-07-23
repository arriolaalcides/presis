<?php
namespace Presis\RetiroBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Doctrine\ORM\EntityRepository;
use Presis\ServicioBundle\Entity\CpCordon;



class FindGuiaType extends AbstractType
{

    private $securityContext;
    private $empresa;

    public function __construct($securityContext,$empresa)
    {
        $this->securityContext = $securityContext;
        $this->empresa = $empresa;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $user = $this->securityContext->getToken()->getUser();

            if($user->hasRole('ROLE_CLIENTE')) {
                $builder
                    ->add('user', 'entity', array(
                    'class' => 'PresisUserBundle:User',
                    'mapped' => false,
                    'query_builder' => function (EntityRepository $er) use ($user) {
                        return $er->createQueryBuilder('u')
                            ->where('u.username = :user')
                            ->setParameter('user', trim($user));
                    },
                ));
            }
        $builder
            ->add('id', 'text', array('required' => false, 'label'=> 'Nro. Guia Desde', 'mapped'=>false))
            ->add('idHasta', 'text', array('required' => false, 'label'=> 'Nro. Guia Hasta', 'mapped'=>false))
            ->add('remito', 'text', array('required' => false, 'label'=> 'Guia Manual', 'mapped'=>false))
            ->add('planillaDesde', 'text', array('required' => false, 'label'=> 'Planilla desde', 'mapped'=>false))
            ->add('planillaHasta', 'text', array('required' => false, 'label'=> 'Planilla hasta', 'mapped'=>false))
            ->add('sucursal', 'entity', array(
                'class' => 'PresisGeneralBundle:Sucursal',
                'label' => "Sucursales",
                'required' => false,
                'mapped' => false,
            ))
            ->add('zona', 'entity', array(
                'class' => 'PresisServicioBundle:CpCordon',
                'required' => false,
                'mapped' => false,
                'query_builder' => function(EntityRepository $er) {
                    return $er->createQueryBuilder('u')
                        ->groupBy('u.zona')
                        ->orderBy('u.zona', 'ASC');
                },
            ))
            ->add('fechaUdesde', 'text', array('required' => false, 'label'=> 'Fecha U. Estado Desde', 'mapped'=>false))
            ->add('fechaUhasta', 'text', array('required' => false, 'label'=> 'Fecha U. Estado Hasta', 'mapped'=>false))

            ->add('fechaUplanillaDesde', 'text', array('required' => false, 'label'=> 'Fecha U. planilla Desde', 'mapped'=>false))
            ->add('fechaUplanillaHasta', 'text', array('required' => false, 'label'=> 'Fecha U. planilla Hasta', 'mapped'=>false))

            ->add('fechaFacturaDesde', 'text', array('required' => false, 'mapped'=>false))
            ->add('fechaFacturaHasta', 'text', array('required' => false, 'mapped'=>false))

            ->add('nroPlanilla', 'text', array('required' => false, 'label'=> 'Nro. planilla', 'mapped'=>false))
            //->add('distribuidor', 'text', array('required' => false, 'label'=> 'Distribuidor', 'mapped'=>false))
            ->add('distribuidor', 'entity', array(
                'class' => 'DistribuidorBundle:Distribuidor',
//                    'choices' => $user->getCliente()->getRemitente(),
                'label' => "Distribuidor",
                'required' => false,
                'mapped' => false,
            ))
            /*->add('estado', 'text', array('required' => false, 'label'=> 'Estado', 'mapped'=>false))*/
            ->add('estado', 'entity', array(
                'class' => 'EstadoBundle:Estado',
                'multiple' => true,
                'label' => "Estado",
                'required' => false,
                'mapped' => false,
                'attr' => array('style' => 'width:300px'),
                'query_builder' => function(EntityRepository $er) {
                    return $er->createQueryBuilder('e')
                        ->where('e.id != 106');
                },
            ))
            ->add('sinEstado', 'checkbox', array(
                'required' => false,
                'mapped' => false,))
            ->add('sinFactura', 'checkbox', array(
                'required' => false,
                'mapped' => false,))
            ->add('datosenvios', new \Presis\DatosEnviosBundle\Form\DatosEnviosType($this->securityContext, "buscar", $this->empresa));
    }


    /**
     * @return string
     */
    public function getName()
    {
        return 'presis_retirobundle_retiro';
    }
}
