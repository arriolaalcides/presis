<?php
/**
 * Created by IntelliJ IDEA.
 * User: pamtru
 * Date: 17/11/2014
 * Time: 09:51 PM
 */
namespace Presis\UserBundle\Form\Type;

use Presis\GeneralBundle\Entity\VendedorRepository;
use Presis\GeneralBundle\PresisGeneralBundle;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\Validator\Constraints\False;
use Presis\GeneralBundle\Entity\Sucursal;
use Presis\GeneralBundle\Entity\SucursalRepository;
use Presis\GeneralBundle\Entity\ClienteRepository;
use Presis\UserBundle\EventListener\AddSucursalFieldSubscriber;

class RegistrationFormType extends AbstractType
{
    private $securityContext;

    public function __construct(SecurityContext $securityContext)
    {
        $this->securityContext = $securityContext;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        // add your custom field
        $user = $this->securityContext->getToken()->getUser();

        //die($user->getCliente()->getEmpresa());

        $builder->add('submit', 'submit', array('label' => 'Crear','attr' => array('class'=> 'btn btn-success')));
        if(!$user->hasRole("ROLE_ADMIN") AND !$user->hasRole("ROLE_VENDEDOR") AND !$user->hasRole("ROLE_CLIENTE")){

        }else{
            if ($user->hasRole("ROLE_ADMIN")) {
                $builder->add('roles', 'choice', array(
                    'choices' => array(
                        'ROLE_ADMIN' => 'ADMINISTRADOR',
                        'ROLE_CLIENTE' => 'CLIENTE',
                        'ROLE_VENDEDOR' => 'COMERCIAL',
                        'ROLE_DISTRIBUIDOR' => 'DISTRIBUIDOR',
                        'ROLE_SUPERVISOR_OPERATIVO' => 'SUPERVISOR OPERATIVO',
                        'ROLE_OPERATIVO' => 'OPERATIVO',
                        'ROLE_BACK_OFFICE' => 'BACK OFFICE',
                        'ROLE_ADMINISTRACION' => 'ADMINISTRACION',
			            'ROLE_SUCURSAL' => 'SUCURSAL',
                        'ROLE_GALANDER' => 'GALANDER',
                        'ROLE_ANALISTA_ST' => 'ANALISTA ST',
                        'ROLE_ANALISTA_GERENCIA_ST' => 'ANALISTA GERENCIA ST',
                        'ROLE_UNIR' => 'UNIR',
                    ),
                    'label' => 'Roles',
                    'expanded' => false,
                    'multiple' => true,
                    'mapped' => true,
                ));
                $builder->add('vendedor', 'entity', array('label' => 'Comercial', 'required' => false, 'class' => 'PresisGeneralBundle:Vendedor',
                    'property' => 'nombre',));
                $builder->add('cliente', 'entity', array('required' => false, 'class' => 'PresisGeneralBundle:Cliente',
                    'property' => 'empresa',));
                $builder->add('distribuidor', 'entity', array('required' => false, 'class' => 'DistribuidorBundle:Distribuidor'));
                $builder->add('userAdmin');
                //$builder->add('sucursal');
                $builder->addEventSubscriber(new AddSucursalFieldSubscriber());

            }else if($user->hasRole("ROLE_VENDEDOR")){
                $builder->add('roles', 'choice', array(
                    'choices' => array(
                        'ROLE_CLIENTE' => 'CLIENTE'
                    ),
                    'label' => 'Roles',
                    'expanded' => false,
                    'multiple' => true,
                    'mapped' => true,
                ));
                $builder->add('vendedor', 'entity', array('label' => 'Comercial', 'required' => false, 'class' => 'PresisGeneralBundle:Vendedor',
                    'property' => 'nombre',));
                $builder->add('cliente', 'entity', array('required' => false, 'class' => 'PresisGeneralBundle:Cliente',
                    'property' => 'empresa',));
                $builder->add('distribuidor', 'entity', array('required' => false, 'class' => 'DistribuidorBundle:Distribuidor'));
                $builder->add('userAdmin');
                //$builder->add('sucursal');
                $builder->addEventSubscriber(new AddSucursalFieldSubscriber());
            }else{
                $builder->add('roles', 'choice', array(

                    'choices'=> array(
                        'ROLE_CLIENTE' => 'CLIENTE'),
                    'data'=>array('ROLE_CLIENTE'),
                    'label' => 'Roles',
                    'expanded' => false,
                    'multiple' => true,

                    'mapped' => true,

                ));
                $builder->add('vendedor','entity',array('attr'=>array('disabled'=>true),'label'=>'Comercial','required'=>false,'class' => 'PresisGeneralBundle:Vendedor',
                    'property' => 'nombre',));
                /*$builder->add('cliente','entity',array('attr'=>array('disabled'=>true),'data'=>$user->getCliente() ,'required'=>false,'class' => 'PresisGeneralBundle:Cliente',
                    'property' => 'empresa',));*/
                $builder->add('cliente', 'entity', array(
                    'class' => 'PresisGeneralBundle:Cliente',
                    'label' => "Cliente",
                    'property' => 'empresa',
                    'query_builder' => function (ClienteRepository $er) use ($user) {
                        return $er->createQueryBuilder('c')
                            ->where('c.empresa = :empresa')
                            ->setParameter('empresa', $user->getCliente()->getEmpresa());
                    },
                ));
                $builder->add('userAdmin');
                //$builder->add('sucursal');
                $builder->add('sucursal', 'entity', array(
                    'class' => 'PresisGeneralBundle:Sucursal',
                    'label' => "Sucursal",
                    'required' => false,
                    'query_builder' => function (SucursalRepository $er) use ($user) {
                        return $er->createQueryBuilder('c')
                            ->where('c.cliente = :cliente')
                            ->setParameter('cliente', $user->getCliente());
                    },
                ));
            }
        }
    }

    public function getParent()
    {
        return 'fos_user_registration';
    }

    public function getName()
    {
        return 'presis_user_registration';
    }
}