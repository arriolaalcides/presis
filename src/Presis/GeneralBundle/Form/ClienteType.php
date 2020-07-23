<?php

namespace Presis\GeneralBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Security\Core\SecurityContext;
use Doctrine\ORM\EntityRepository;

class ClienteType extends AbstractType
{

    private $securityContext;
    private $cliente;

    public function __construct(SecurityContext $securityContext, $cliente)
    {
        $this->securityContext = $securityContext;
        $this->cliente = $cliente;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $cliente = $this->cliente;
        $user = $this->securityContext->getToken()->getUser();

        $builder
            ->add('codcli','text',array('label'=>'C&oacute;digo'))
            ->add('email')
            ->add('importeFijoPuertaPuerta','text',array('attr'=>array('id'=>'pelos', 'input_group' =>array('prepend'=>'$')), 'label'=>'Importe fijo puerta puerta','required'=>false))
            ->add('kgFijoPuertaPuerta','text',array('attr'=>array('input_group' =>array('prepend'=>'Kg')),'label'=>'Kg fijo puerta puerta','required'=>false))
            ->add('celular')
            ->add('empresa')
            ->add('porcentajeSeguro','text',array(
                'label'=>'% Seguro',
                'required'=>false,
            ))
            ->add('seguroMinimo')
            ->add('seguroMaximo')
            ->add('contrareembolsoEfectivo','text',array('attr'=>array('id'=>'pelos', 'input_group' =>array('prepend'=>'$')), 'label'=>'C. Efectivo','required'=>false))
            ->add('contrareembolsoCheque','text',array('attr'=>array('input_group' =>array('prepend'=>'$')),'label'=>'C. Cheque','required'=>false))
            ->add('conRemito')
            ->add('cobroEfectivo', null, array('label'=>'Con cobro'))
            ->add('enviaMail',null,array('label'=>'Enviar mail al destinatario'))
            ->add('enviaMailOrigen',null,array('label'=>'Enviar mail al remitente'))
            ->add('montoServicio','text',array('label'=>'$ Servicio','required'=>false))
            ->add('conGuiaWeb')
            ->add('montoGuiaWeb', 'text', array('label'=>'$ Guia WEB','required'=>false))
            ->add('conTarifario', 'checkbox', array('mapped' => false, 'label' => 'Con Tarifario', 'required' => false))
            ->add('tipoClienteBejerman', null, array(
                'label' => "Tipo C. Bejerman",
                'required' => false))
            //->add('formaPago','text',array('label'=>'Forma de pago','required'=>false))
            ->add('formaPago', 'entity', array(
                'class' => 'PresisGeneralBundle:FormaPago',
                'label' => "Forma de pago",
                'required' => false))
            ->add('tipoIva')
            ->add('categoriaIva')
            ->add('tipoFacturacion', 'choice', array(
                'choices'   => array(
                    'peso'          => 'Peso',
                    'volumen'       => 'Volumen',
                    'pesovolumen'   => 'Peso y Volumen',
                ),
                'label'    => 'Peso/Volumen',
            ))
            //->add('tipoDocumento','entity',array('label'=>'Tipo Documento','required'=>false))
            ->add('tipoDocumento', 'entity', array(
                'class' => 'TipoDNIBundle:TipoDni',
                'label' => "Tipo Documento",
                'required' => false))
            ->add('documento')
            ->add('nroCta','text',array('label'=>'N° Cuenta','required'=>false))
            ->add('fechaAlta', 'date', array(
                'label'=>'Fecha',
                'format' => \IntlDateFormatter::SHORT,
                'input' => 'datetime',
                'widget' => 'single_text',
                'data' => new \DateTime("now"),
                'format' => 'dd/MM/yyyy',
                'read_only' => true,
                'required'=>false,
            ))
            ->add('FechaBaja', null, array(
                'read_only' => true
            ))
            ->add('activo')
            ->add('is_porcentaje', 'checkbox', array('label' => 'Es  porcentaje', 'required' => false))
            ->add('tipoDeCobro', 'choice', array(
                'choices'   => array(
                    'peso'              => 'Peso',
                    'valordeclarado'    => 'Valor declarado',
                    'bulto'             => 'Bulto',
                    'distancia'   => 'Distancia',
                ),
                'label'    => 'Cobro por',
            ))
            ->add('minimoACobrar')
            ->add('maximoACobrar')
            ->add('porcentajeACobrar')
            ->add('valorDeclaradoPorDefecto')
        ;

        if ($user->hasRole("ROLE_VENDEDOR")) {
            $builder ->add('aforo',null,array('attr'=>array('input_group' =>array('prepend'=>'GR')),'read_only'=>true));
        }else{
            $builder ->add('aforo',null,array('attr'=>array('input_group' =>array('prepend'=>'GR'))));
        }

            $builder->add('contacto')       
            ->add('custom_price_list', 'checkbox', array(
                'label'=>'Usa lista propietaria',
                'required'=>false
            ))

            //->add('lista',null,array( 'empty_data'  => null,'required'=>false))
           /* ->add('lista','entity',array(
                'label'=>'Lista',
                'required'=>false,
                'class'=>'PresisServicioBundle:Lista',
                    'query_builder' => function(EntityRepository $er ){
                        return $er->createQueryBuilder('l')
                            ->orderBy('l.descripcion', 'ASC')
                            ->where('l.is_general != TRUE')
                            ->andWhere('l.cliente = NULL');
                    }
                ))*/
            ->add('lista', 'entity', array(
                'required'=>false,
                'class' => 'PresisServicioBundle:Lista',
                'label' => "Lista",
                'query_builder' => function (EntityRepository $er) use($cliente){
                    return $er->createQueryBuilder('l')
                        ->where('l.cliente is NULL or l.cliente = :cliente')
                        ->andWhere('l.isGeneral != true')
                         ->setParameter('cliente', $cliente);
                },

            ))
            ->add('rubro')
            ->add('categorias','entity',array('label'=>'Categor&iacute;as','required'=>false,'class'=>'PresisGeneralBundle:Categoria','property'=>'nombre','expanded'=>false,'multiple'=>true,))
            ->add('servicios','entity',array('required'=>false,'class'=>'PresisServicioBundle:Servicio','property'=>'descripcion','expanded'=>false,'multiple'=>true,));

        if (!$user->hasRole("ROLE_VENDEDOR")) {
            $builder ->add('vendedor',null,array('label'=>'Comercial','read_only'=>false));
        }else{
            $builder ->add('vendedor','entity',array('label'=>'Comercial','data'=>$user->getVendedor(),'required'=>true,'class'=>'PresisGeneralBundle:Vendedor','property'=>'nombre','expanded'=>false,'multiple'=>false,'attr'=>array('disabled'=>true)));
        }




    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {

        $resolver->setDefaults(array(
            'data_class' => 'Presis\GeneralBundle\Entity\Cliente'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'presis_generalbundle_cliente';
    }
}
