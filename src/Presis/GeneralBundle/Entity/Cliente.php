<?php

namespace Presis\GeneralBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;
use JMS\Serializer\Annotation\VirtualProperty;
use JMS\Serializer\Annotation\SerializedName;
/**
 * Cliente
 *
 * @ORM\Table(name="cliente")
 * @ORM\Entity(repositoryClass="Presis\GeneralBundle\Entity\ClienteRepository")
 * @UniqueEntity("empresa")
 * @ExclusionPolicy("all")
 */
class Cliente
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @Expose
     */
    private $id;
    /**
     * @var integer
     *
     * @ORM\Column(name="aforo", type="integer")

     */
    private $aforo = 0;

    /**
    * @var boolean
    *
    * @ORM\Column(name="cobroEfectivo", type="boolean", nullable=true)

    */
    private $cobroEfectivo;

    /**
     * @var boolean
     *
     * @ORM\Column(name="enviaMail", type="boolean", nullable=true)

     */
    private $enviaMail;



    /**
     * @var integer
     *
     * @ORM\Column(name="precio_aforo", type="decimal", scale=2, nullable=true)

     */
    private $precioAforo = 0;

    /**
     * @var integer
     *
     * @ORM\Column(name="aforo_minimo", type="decimal", scale=2, nullable=true)

     */
    private $aforoMinimo = 0;

    /**
     * @var integer
     *
     * @ORM\Column(name="aforo_a_cobrar", type="decimal",scale=2, nullable=true)

     */
    private $aforoACobrar = 0.0;

    /**
     * @var integer
     *
     * @ORM\Column(name="porcentaje_aforo", type="decimal",scale=2, nullable=true)

     */
    private $porcentajeAforo = 0.0;

    /**
     * @var string
     *
     * @ORM\Column(name="codigo", type="string",length=25)

     */
    private $codcli;
    /**
     * @var string
     *
     * @ORM\Column(name="empresa", type="string",length=254)
     * @Expose
     */
    private $empresa;
    /**
     * @var string
     *
     * @ORM\Column(name="contacto", type="string",length=254)
     *

     */
    private $contacto;

    /**
     * @var string
     *
     * @ORM\Column(name="nroCta", type="string", length=254, nullable=true)

     */
    private $nroCta;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=254,nullable=false)
     */
    private $email;
    /**
     * @var string
     *
     * @ORM\Column(name="celular", type="string", length=254,nullable=false)
     */
    private $celular;

    /**
     * @var boolean
     *
     * @ORM\Column(name="isCustomPriceList", type="boolean",nullable=true)
     */
    private $custom_price_list;

    /**
     * @var boolean
     *
     * @ORM\Column(name="con_guia_web", type="boolean", nullable=true)
     */
    private $conGuiaWeb;

    /**
     * @ORM\OneToMany(targetEntity="Presis\GeneralBundle\Entity\Sucursal", mappedBy="cliente",cascade={"remove"})
     */
    protected $sucursales;

    /**
     * @ORM\OneToMany(targetEntity="Presis\CecosBundle\Entity\Cecos", mappedBy="ceco",cascade={"remove"})
     */
    protected $cecos;

    /**
     * @ORM\OneToOne(targetEntity="Presis\ServicioBundle\Entity\Lista",mappedBy="cliente",cascade={"persist","remove"})
     */
    private $lista;

    /**
     * @ORM\ManyToMany(targetEntity="Presis\GeneralBundle\Entity\Categoria", inversedBy="clientes")
     * @ORM\JoinTable(name="cliente_categorias")
     **/
    private $categorias;
    /**
     * @ORM\ManyToMany(targetEntity="Presis\ServicioBundle\Entity\Servicio")
     * @ORM\JoinTable(name="cliente_servicios")
     **/
    private $servicios;
    /**
     * @ORM\ManyToOne(targetEntity="Presis\GeneralBundle\Entity\Vendedor",inversedBy="clientes")
     * @ORM\JoinColumn(name="vendedor_id", referencedColumnName="id",nullable=false)
     *
     **/
    private $vendedor;
    /**
     * @ORM\ManyToOne(targetEntity="Presis\GeneralBundle\Entity\Rubro")
     * @ORM\JoinColumn(name="rubro_id", referencedColumnName="id",nullable=true)
     *
     **/
    private $rubro;

    /**
     * @var boolean
     *
     * @ORM\Column(name="seguro_fijo", type="boolean", nullable=true)

     */
    private $seguroFijo = false;

    /**
     * @var integer
     *
     * @ORM\Column(name="porcentaje_seguro", type="decimal",scale=2, nullable=true)

     */
    private $porcentajeSeguro = 0;

    /**
     * @var integer
     *
     * @ORM\Column(name="seguro_minimo", type="decimal", scale=2, nullable=true)

     */
    private $seguroMinimo = 0.0;

    /**
     * @var integer
     *
     * @ORM\Column(name="seguro_maximo", type="decimal", scale=2, nullable=true)
     */
    private $seguroMaximo = 0.0;

    /**
     * @var boolean
     *
     * @ORM\Column(name="con_remito", type="boolean", nullable=true)

     */
    private $conRemito;

    /**
     * @var integer
     *
     * @ORM\Column(name="monto_servicio", type="decimal", scale=2)

     */
    private $montoServicio = 0.0;

    /**
     * @var integer
     *
     * @ORM\Column(name="monto_guia_web", type="decimal", scale=2)

     */
    private $montoGuiaWeb = 0.0;

    /**
     * @var boolean
     *
     * @ORM\Column(name="con_tarifario", type="boolean", nullable=true)

     */
    private $conTarifario;

    /**
     * @var string
     *
     * @ORM\Column(name="tipo_cliente_bejerman", type="string", length=254, nullable=true)e=true)
     **/
    private $tipoClienteBejerman;

    /**
     * @ORM\ManyToOne(targetEntity="Presis\GeneralBundle\Entity\FormaPago")
     * @ORM\JoinColumn(name="forma_pago_id", referencedColumnName="id", nullable=true)
     **/
    private $formaPago;

    /**
     * @ORM\ManyToOne(targetEntity="Presis\GeneralBundle\Entity\TipoIva")
     * @ORM\JoinColumn(name="tipo_iva_id", referencedColumnName="id", nullable=true)
     **/
    private $tipoIva;

    /**
     * @ORM\ManyToOne(targetEntity="Presis\GeneralBundle\Entity\CategoriaIva")
     * @ORM\JoinColumn(name="categoria_iva_id", referencedColumnName="id", nullable=true)
     **/
    private $categoriaIva;

    /**
     * @ORM\ManyToOne(targetEntity="Presis\TipoDNIBundle\Entity\TipoDni")
     * @ORM\JoinColumn(name="tipo_documento_id", referencedColumnName="id", nullable=true)
     **/
    private $tipoDocumento;

    /**
     * @var string
     *
     * @ORM\Column(name="documento", type="string", length=254, nullable=true)
     */
    private $documento;

    /**
     * @var string
     *
     * @ORM\Column(name="tipo_cobro", type="string")
     */
    private $tipoDeCobro = 'peso';

    /**
     * @var float
     *
     * @ORM\Column(name="minimo_a_cobrar", type="decimal", scale = 2, nullable=true)
     */
    private $minimoACobrar = 0;

    /**
     * @var float
     *
     * @ORM\Column(name="maximo_a_cobrar", type="decimal", scale = 2, nullable=true)
     */
    private $maximoACobrar = 0;

    /**
     * @var float
     *
     * @ORM\Column(name="porcentaje_a_cobrar", type="decimal", scale = 2, nullable=true)
     */
    private $porcentajeACobrar = 0;

    /**
     * @var float
     *
     * @ORM\Column(name="valor_por_defecto", type="decimal", scale = 2, nullable=true)
     */
    private $valorDeclaradoPorDefecto = 0;

    /**
     * @var string
     *
     * @ORM\Column(name="tipo_facturacion", type="string", nullable=true)
     */
    private $tipoFacturacion;

    /**
     * @ORM\OneToMany(targetEntity="Presis\RendicionBundle\Entity\Rendicion", mappedBy="cliente")
     */
    private $rendiciones;

    /**
     * @ORM\OneToMany(targetEntity="Presis\GeneralBundle\Entity\BultoExcedente", mappedBy="cliente")
     */
    private $bultos_excedentes;

    /**
     * @ORM\OneToMany(targetEntity="Presis\ConstanciaRetiroBundle\Entity\ConstanciaRetiro", mappedBy="cliente",cascade={"remove"})
     */
    protected $constanciasRetiro;


    public function __construct()
    {
        $this->sucursales = new ArrayCollection();
        $this->categorias=new ArrayCollection();
        $this->bultos_excedentes=new ArrayCollection();
        $this->remitentes=new ArrayCollection();
        $this->destinatarios=new ArrayCollection();
        $this->cecos=new ArrayCollection();
        $this->constanciasRetiro=new ArrayCollection();
    }
    /**
     * Get id
     *
     * @return integer
     */

    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getCelular()
    {
        return $this->celular;
    }

    /**
     * @param string $celular
     */
    public function setCelular($celular)
    {
        $this->celular = $celular;
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param string $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }


    /**
     * @return int
     */
    public function getAforo()
    {
        return $this->aforo;
    }

    /**
     * @param int $aforo
     */
    public function setAforo($aforo)
    {
        $this->aforo = $aforo;
    }

    /**
     * @return mixed
     */
    public function getCategorias()
    {
        return $this->categorias;
    }

    /**
     * @param mixed $categorias
     */
    public function setCategorias($categorias)
    {
        $this->categorias = $categorias;
    }

    /**
     * @return string
     */
    public function getContacto()
    {
        return $this->contacto;
    }

    /**
     * @param string $contacto
     */
    public function setContacto($contacto)
    {
        $this->contacto = $contacto;
    }

    /**
     * @return boolean
     */
    public function getCustomPriceList()
    {
        return $this->custom_price_list;
    }

    /**
     * @param boolean $custom_price_list
     */
    public function setCustomPriceList($custom_price_list)
    {
        $this->custom_price_list = $custom_price_list;
    }

    /**
     * @return string
     */
    public function getEmpresa()
    {
        return $this->empresa;
    }

    /**
     * @param string $empresa
     */
    public function setEmpresa($empresa)
    {
        $this->empresa = $empresa;
    }

    /**
     * @return mixed
     */
    public function getLista()
    {
        return $this->lista;
    }

    /**
     * @param mixed $lista
     */
    public function setLista($lista)
    {
        if (is_null($lista)){
            $this->lista = null;
            //$lista->setCliente($this);
        }else{
            $this->lista = $lista;
            $lista->setCliente($this);
        }

    }

    /**
     * @return mixed
     */
    public function getSucursales()
    {
        return $this->sucursales;
    }

    /**
     * @param mixed $sucursales
     */
    public function setSucursales($sucursales)
    {
        $this->sucursales = $sucursales;
    }

    /**
     * @return mixed
     */
    public function getVendedor()
    {
        return $this->vendedor;
    }

    /**
     * @param mixed $vendedor
     */
    public function setVendedor($vendedor)
    {
        $this->vendedor = $vendedor;
    }
    public function __toString(){
        return $this->getEmpresa();
    }

    /**
     * @return mixed
     */
    public function getRubro()
    {
        return $this->rubro;
    }

    /**
     * @param mixed $rubro
     */
    public function setRubro($rubro)
    {
        $this->rubro = $rubro;
    }

    /**
     * @return mixed
     */
    public function getRemitentes()
    {
        return $this->remitentes;
    }

    public function getServicios()
    {
        return $this->servicios;
    }

    /**
     * @param mixed $servicios
     */
    public function setServicios($servicios)
    {
        $this->servicios = $servicios;
    }

    /**
     * @return string
     */
    public function getCodcli()
    {
        return $this->codcli;
    }

    /**
     * @param string $codcli
     */
    public function setCodcli($codcli)
    {
        $this->codcli = $codcli;
    }

    /**
     * Add constancia
     *
     * @param \Presis\ConstanciaRetiroBundle\Entity\ConstanciaRetiro $constancia
     *
     * @return Cliente
     */
    public function addConstancia(\Presis\ConstanciaRetiroBundle\Entity\ConstanciaRetiro $constancia)
    {
        $this->constanciasRetiro[] = $constancia;

        return $this;
    }

    /**
     * Remove constancia
     *
     * @param \Presis\GeneralBundle\Entity\Sucursal $constancia
     */
    public function removeConstancia(\Presis\ConstanciaRetiroBundle\Entity\ConstanciaRetiro $constancia)
    {
        $this->constanciasRetiro->removeElement($constancia);
    }



    /**
     * Add sucursale
     *
     * @param \Presis\GeneralBundle\Entity\Sucursal $sucursale
     *
     * @return Cliente
     */
    public function addSucursale(\Presis\GeneralBundle\Entity\Sucursal $sucursale)
    {
        $this->sucursales[] = $sucursale;

        return $this;
    }

    /**
     * Remove sucursale
     *
     * @param \Presis\GeneralBundle\Entity\Sucursal $sucursale
     */
    public function removeSucursale(\Presis\GeneralBundle\Entity\Sucursal $sucursale)
    {
        $this->sucursales->removeElement($sucursale);
    }

    /**
     * Add categoria
     *
     * @param \Presis\GeneralBundle\Entity\Categoria $categoria
     *
     * @return Cliente
     */
    public function addCategoria(\Presis\GeneralBundle\Entity\Categoria $categoria)
    {
        $this->categorias[] = $categoria;

        return $this;
    }

    /**
     * Remove categoria
     *
     * @param \Presis\GeneralBundle\Entity\Categoria $categoria
     */
    public function removeCategoria(\Presis\GeneralBundle\Entity\Categoria $categoria)
    {
        $this->categorias->removeElement($categoria);
    }

    /**
     * Add servicio
     *
     * @param \Presis\ServicioBundle\Entity\Servicio $servicio
     *
     * @return Cliente
     */
    public function addServicio(\Presis\ServicioBundle\Entity\Servicio $servicio)
    {
        $this->servicios[] = $servicio;

        return $this;
    }
    public function addRemitente(\Presis\RemitentesBundle\Entity\Remitente $remitente)
    {
        $this->remitentes[] = $remitente;

        return $this;
    }

    /**
     * Remove servicio
     *
     * @param \Presis\ServicioBundle\Entity\Servicio $servicio
     */
    public function removeServicio(\Presis\ServicioBundle\Entity\Servicio $servicio)
    {
        $this->servicios->removeElement($servicio);
    }

    /**
     * Set porcentajeSeguro
     *
     * @param integer $porcentajeSeguro
     *
     * @return Cliente
     */
    public function setPorcentajeSeguro($porcentajeSeguro)
    {
        $this->porcentajeSeguro = $porcentajeSeguro;

        return $this;
    }

    /**
     * Get porcentajeSeguro
     *
     * @return integer
     */
    public function getPorcentajeSeguro()
    {
        return ($this->porcentajeSeguro)?$this->porcentajeSeguro:0;
    }

    /**
     * Set conRemito
     *
     * @param boolean $conRemito
     *
     * @return Cliente
     */
    public function setConRemito($conRemito)
    {
        $this->conRemito = $conRemito;

        return $this;
    }

    /**
     * Get conRemito
     *
     * @return boolean
     */
    public function getConRemito()
    {
        return $this->conRemito;
    }

    /**
     * Set montoServicio
     *
     * @param string $montoServicio
     *
     * @return Cliente
     */
    public function setMontoServicio($montoServicio)
    {
        $this->montoServicio = $montoServicio;

        return $this;
    }

    /**
     * Get montoServicio
     *
     * @return string
     */
    public function getMontoServicio()
    {
        return $this->montoServicio;
    }

    /**
     * Set montoGuiaWeb
     *
     * @param string $montoGuiaWeb
     *
     * @return Cliente
     */
    public function setMontoGuiaWeb($montoGuiaWeb)
    {
        $this->montoGuiaWeb = $montoGuiaWeb;

        return $this;
    }

    /**
     * Get montoGuiaWeb
     *
     * @return string
     */
    public function getMontoGuiaWeb()
    {
        return $this->montoGuiaWeb;
    }

    /**
     * Set conTarifario
     *
     * @param boolean $conTarifario
     *
     * @return Cliente
     */
    public function setConTarifario($conTarifario)
    {
        $this->conTarifario = $conTarifario;

        return $this;
    }

    /**
     * Get conTarifario
     *
     * @return boolean
     */
    public function getConTarifario()
    {
        return $this->conTarifario;
    }

    /**
     * Set tipoClienteBejerman
     *
     * @param string $tipoClienteBejerman
     *
     * @return Cliente
     */
    public function setTipoClienteBejerman($tipoClienteBejerman = null)
    {
        $this->tipoClienteBejerman = $tipoClienteBejerman;

        return $this;
    }

    /**
     * Get tipoClienteBejerman
     *
     * @return string
     */
    public function getTipoClienteBejerman()
    {
        return $this->tipoClienteBejerman;
    }

    /**
     * Set formaPago
     *
     * @param \Presis\GeneralBundle\Entity\FormaPago $formaPago
     *
     * @return Cliente
     */
    public function setFormaPago(\Presis\GeneralBundle\Entity\FormaPago $formaPago = null)
    {
        $this->formaPago = $formaPago;

        return $this;
    }

    /**
     * Get formaPago
     *
     * @return \Presis\GeneralBundle\Entity\FormaPago
     */
    public function getFormaPago()
    {
        return $this->formaPago;
    }

    /**
     * Set documento
     *
     * @param string $documento
     *
     * @return Cliente
     */
    public function setDocumento($documento)
    {
        $this->documento = $documento;

        return $this;
    }

    /**
     * Get documento
     *
     * @return string
     */
    public function getDocumento()
    {
        return $this->documento;
    }

    /**
     * Set tipoIva
     *
     * @param \Presis\GeneralBundle\Entity\TipoIva $tipoIva
     *
     * @return Cliente
     */
    public function setTipoIva(\Presis\GeneralBundle\Entity\TipoIva $tipoIva = null)
    {
        $this->tipoIva = $tipoIva;

        return $this;
    }

    /**
     * Get tipoIva
     *
     * @return \Presis\GeneralBundle\Entity\TipoIva
     */
    public function getTipoIva()
    {
        return $this->tipoIva;
    }

    /**
     * Set categoriaIva
     *
     * @param \Presis\GeneralBundle\Entity\CategoriaIva $categoriaIva
     *
     * @return Cliente
     */
    public function setCategoriaIva(\Presis\GeneralBundle\Entity\CategoriaIva $categoriaIva = null)
    {
        $this->categoriaIva = $categoriaIva;

        return $this;
    }

    /**
     * Get categoriaIva
     *
     * @return \Presis\GeneralBundle\Entity\CategoriaIva
     */
    public function getCategoriaIva()
    {
        return $this->categoriaIva;
    }

    /**
     * Set tipoDocumento
     *
     * @param \Presis\TipoDNIBundle\Entity\TipoDNI $tipoDocumento
     *
     * @return Cliente
     */
    public function setTipoDocumento(\Presis\TipoDNIBundle\Entity\TipoDNI $tipoDocumento = null)
    {
        $this->tipoDocumento = $tipoDocumento;

        return $this;
    }

    /**
     * Get tipoDocumento
     *
     * @return \Presis\TipoDNIBundle\Entity\TipoDNI
     */
    public function getTipoDocumento()
    {
        return $this->tipoDocumento;
    }

    public function getFormaPagoNombre(){
        if(is_null($this->getFormaPago())){
            return "";
        }else{
            return $this->getFormaPago()->getNombre();
        }
    }

    /**
     * @var integer
     *
     * @ORM\Column(name="contrareembolsoEfectivo", type="decimal",scale=2, nullable=true)

     */
    private $contrareembolsoEfectivo = 0.0;

    /**
     * @return mixed
     */
    public function getContrareembolsoEfectivo()
    {
        return $this->contrareembolsoEfectivo;
    }

    /**
     * @param mixed $contrareembolsoEfectivo
     */
    public function setContrareembolsoEfectivo($contrareembolsoEfectivo)
    {
        $this->contrareembolsoEfectivo = $contrareembolsoEfectivo;
    }



    /**
     * @var integer
     *
     * @ORM\Column(name="contrareembolsoCheque", type="decimal",scale=2, nullable=true)

     */
    private $contrareembolsoCheque = 0.0;

    /**
     * @return mixed
     */
    public function getContrareembolsoCheque()
    {
        return $this->contrareembolsoCheque;
    }

    /**
     * @param mixed $contrareembolsoCheque
     */
    public function setContrareembolsoCheque($contrareembolsoCheque)
    {
        $this->contrareembolsoCheque = $contrareembolsoCheque;
    }
    /**
     * @return string
     */
    public function getNroCta()
    {
        return $this->nroCta;
    }

    /**
     * @param string $nroCta
     */
    public function setNroCta($nroCta)
    {
        $this->nroCta = $nroCta;
    }
    /*========================================================================*/
    /**
     * @ORM\OneToMany(targetEntity="Presis\RemitentesBundle\Entity\Remitente", mappedBy="cliente",cascade={"remove"})
     */
    private $remitentes;

    /**
     * @return mixed
     */


    /*========================================================================*/
    /**
     * @ORM\OneToMany(targetEntity="Presis\DestinatariosBundle\Entity\Destinatarios", mappedBy="cliente",cascade={"remove"})
     */
    private $destinatarios;

    /**
     * @return mixed
     */
    public function getDestinatarios()
    {
        return $this->destinatarios;
    }

    /**
     * @param mixed $destinatarios
     */
    public function setDestinatarios($destinatarios)
    {
        $this->destinatarios = $destinatarios;
    }



    /*=============================================================================*/

    /**
     * @ORM\Column(type="date")
     */
    private $fechaAlta;

    public function setFechaAlta($fechaAlta){
        $this->fechaAlta = $fechaAlta;
    }

    public function getFechaAlta()
    {
        return $this->fechaAlta;
    }


    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $fechaBaja;

    public function setFechaBaja($fechaBaja){
        $this->fechaBaja = $fechaBaja;
    }

    public function getFechaBaja()
    {
        return $this->fechaBaja;
    }

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $activo = true;

    public function setActivo($activo){
        $this->activo = $activo;
    }

    public function getActivo()
    {
        return $this->activo;
    }

    /**
     * @ORM\Column(type="boolean")
     */
    private $is_porcentaje;

    public function setIsPorcentaje($is_porcentaje){
        $this->is_porcentaje = $is_porcentaje;
    }

    public function getIsPorcentaje()
    {
        return $this->is_porcentaje;
    }

    /**
     * Add rendicione
     *
     * @param \Presis\RendicionBundle\Entity\Rendicion $rendicione
     *
     * @return Cliente
     */
    public function addRendicione(\Presis\RendicionBundle\Entity\Rendicion $rendicione)
    {
        $this->rendiciones[] = $rendicione;

        return $this;
    }

    /**
     * Remove rendicione
     *
     * @param \Presis\RendicionBundle\Entity\Rendicion $rendicione
     */
    public function removeRendicione(\Presis\RendicionBundle\Entity\Rendicion $rendicione)
    {
        $this->rendiciones->removeElement($rendicione);
    }

    /**
     * Get rendiciones
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getRendiciones()
    {
        return $this->rendiciones;
    }


    /**
     * Add destinatario
     *
     * @param \Presis\DestinatariosBundle\Entity\Destinatarios $destinatario
     *
     * @return Cliente
     */
    public function addDestinatario(\Presis\DestinatariosBundle\Entity\Destinatarios $destinatario)
    {
        $this->destinatario[] = $destinatario;

        return $this;
    }

    /**
     * Remove destinatario
     *
     * @param \Presis\DestinatariosBundle\Entity\Destinatarios $destinatario
     */
    public function removeDestinatario(\Presis\DestinatariosBundle\Entity\Destinatarios $destinatario)
    {
        $this->destinatario->removeElement($destinatario);
    }

    /**
     * Set minimoACobrar
     *
     * @param string $minimoACobrar
     *
     * @return Cliente
     */
    public function setMinimoACobrar($minimoACobrar)
    {
        $this->minimoACobrar = $minimoACobrar;

        return $this;
    }

    /**
     * Get minimoACobrar
     *
     * @return string
     */
    public function getMinimoACobrar()
    {
        return $this->minimoACobrar;
    }

    /**
     * Set maximoACobrar
     *
     * @param string $maximoACobrar
     *
     * @return Cliente
     */
    public function setMaximoACobrar($maximoACobrar)
    {
        $this->maximoACobrar = $maximoACobrar;

        return $this;
    }

    /**
     * Get maximoACobrar
     *
     * @return string
     */
    public function getMaximoACobrar()
    {
        return $this->maximoACobrar;
    }

    /**
     * Set porcentajeACobrar
     *
     * @param string $porcentajeACobrar
     *
     * @return Cliente
     */
    public function setPorcentajeACobrar($porcentajeACobrar)
    {
        $this->porcentajeACobrar = $porcentajeACobrar;

        return $this;
    }

    /**
     * Get porcentajeACobrar
     *
     * @return string
     */
    public function getPorcentajeACobrar()
    {
        return $this->porcentajeACobrar;
    }

    /**
     * Set valorDeclaradoPorDefecto
     *
     * @param string $valorDeclaradoPorDefecto
     *
     * @return Cliente
     */
    public function setValorDeclaradoPorDefecto($valorDeclaradoPorDefecto)
    {
        $this->valorDeclaradoPorDefecto = $valorDeclaradoPorDefecto;

        return $this;
    }

    /**
     * Get valorDeclaradoPorDefecto
     *
     * @return string
     */
    public function getValorDeclaradoPorDefecto()
    {
        return $this->valorDeclaradoPorDefecto;
    }

    /**
     * Set cobroPorValor
     *
     * @param string $tipoDeCobro
     *
     * @return Cliente
     */
    public function setTipoDeCobro($tipoDeCobro)
    {
        $this->tipoDeCobro = $tipoDeCobro;

        return $this;
    }

    /**
     * Get cobroPorValor
     *
     * @return string
     */
    public function getTipoDeCobro()
    {
        return $this->tipoDeCobro;
    }

    /**
     * Add bultosExcedente
     *
     * @param \Presis\GeneralBundle\Entity\BultoExcedente $bultosExcedente
     *
     * @return Cliente
     */
    public function addBultosExcedente(\Presis\GeneralBundle\Entity\BultoExcedente $bultosExcedente)
    {
        $this->bultos_excedentes[] = $bultosExcedente;

        return $this;
    }

    /**
     * Remove bultosExcedente
     *
     * @param \Presis\GeneralBundle\Entity\BultoExcedente $bultosExcedente
     */
    public function removeBultosExcedente(\Presis\GeneralBundle\Entity\BultoExcedente $bultosExcedente)
    {
        $this->bultos_excedentes->removeElement($bultosExcedente);
    }

    /**
     * Get bultosExcedentes
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getBultosExcedentes()
    {
        return $this->bultos_excedentes;
    }

    /**
     * Set seguroMinimo
     *
     * @param string $seguroMinimo
     *
     * @return Cliente
     */
    public function setSeguroMinimo($seguroMinimo)
    {
        $this->seguroMinimo = $seguroMinimo;

        return $this;
    }

    /**
     * Get seguroMinimo
     *
     * @return string
     */
    public function getSeguroMinimo()
    {
        return ($this->seguroMinimo)?$this->seguroMinimo:0;
    }

    /**
     * Set seguroPorDefecto
     *
     * @param string $seguroMaximo
     *
     * @return Cliente
     */
    public function setSeguroMaximo($seguroMaximo)
    {
        $this->seguroMaximo = $seguroMaximo;

        return $this;
    }

    /**
     * Get seguroPorDefecto
     *
     * @return string
     */
    public function getSeguroMaximo()
    {
        return ($this->seguroMaximo)?$this->seguroMaximo:0;
    }

    /**
     * Set precioAforo
     *
     * @param string $precioAforo
     *
     * @return Cliente
     */
    public function setPrecioAforo($precioAforo)
    {
        $this->precioAforo = $precioAforo;

        return $this;
    }

    /**
     * Get precioAforo
     *
     * @return string
     */
    public function getPrecioAforo()
    {
        return $this->precioAforo;
    }

    /**
     * Set aforoMinimo
     *
     * @param string $aforoMinimo
     *
     * @return Cliente
     */
    public function setAforoMinimo($aforoMinimo)
    {
        $this->aforoMinimo = $aforoMinimo;

        return $this;
    }

    /**
     * Get aforoMinimo
     *
     * @return string
     */
    public function getAforoMinimo()
    {
        return $this->aforoMinimo;
    }

    /**
     * Set aforoACobrar
     *
     * @param string $aforoACobrar
     *
     * @return Cliente
     */
    public function setAforoACobrar($aforoACobrar)
    {
        $this->aforoACobrar = $aforoACobrar;

        return $this;
    }

    /**
     * Get aforoACobrar
     *
     * @return string
     */
    public function getAforoACobrar()
    {
        return $this->aforoACobrar;
    }

    /**
     * Set porcentajeAforo
     *
     * @param string $porcentajeAforo
     *
     * @return Cliente
     */
    public function setPorcentajeAforo($porcentajeAforo)
    {
        $this->porcentajeAforo = $porcentajeAforo;

        return $this;
    }

    /**
     * Get porcentajeAforo
     *
     * @return string
     */
    public function getPorcentajeAforo()
    {
        return $this->porcentajeAforo;
    }

    /**
     * Set seguroFijo
     *
     * @param boolean $seguroFijo
     *
     * @return Cliente
     */
    public function setSeguroFijo($seguroFijo)
    {
        $this->seguroFijo = $seguroFijo;

        return $this;
    }

    /**
     * Get seguroFijo
     *
     * @return boolean
     */
    public function getSeguroFijo()
    {
        return $this->seguroFijo;
    }

    /**
     * Set tipoFacturacion
     *
     * @param string $tipoFacturacion
     *
     * @return Cliente
     */
    public function setTipoFacturacion($tipoFacturacion)
    {
        $this->tipoFacturacion = $tipoFacturacion;

        return $this;
    }

    /**
     * Get tipoFacturacion
     *
     * @return string
     */
    public function getTipoFacturacion()
    {
        return $this->tipoFacturacion;
    }

    /**
     * Set conGuiaWeb
     *
     * @param boolean $conGuiaWeb
     *
     * @return Cliente
     */
    public function setConGuiaWeb($conGuiaWeb)
    {
        $this->conGuiaWeb = $conGuiaWeb;

        return $this;
    }

    /**
     * Get conGuiaWeb
     *
     * @return boolean
     */
    public function getConGuiaWeb()
    {
        return $this->conGuiaWeb;
    }

    /**
     * @return mixed
     */
    public function getCecos()
    {
        return $this->cecos;
    }

    /**
     * @param mixed $cecos
     */
    public function setCecos($cecos)
    {
        $this->cecos = $cecos;
    }

    /**
     * @return boolean
     */
    public function getCobroEfectivo()
    {
        return $this->cobroEfectivo;
    }

    /**
     * @param boolean $cobroEfectivo
     */
    public function setCobroEfectivo($cobroEfectivo)
    {
        $this->cobroEfectivo = $cobroEfectivo;
    }

    /**
     * @return mixed
     */
    public function getConstanciasRetiro()
    {
        return $this->constanciasRetiro;
    }

    /**
     * @param mixed $constanciasRetiro
     */
    public function setConstanciasRetiro($constanciasRetiro)
    {
        $this->constanciasRetiro = $constanciasRetiro;
    }

    /**
     * @return boolean
     */
    public function isEnviaMail()
    {
        return $this->enviaMail;
    }

    /**
     * @param boolean $enviaMail
     */
    public function setEnviaMail($enviaMail)
    {
        $this->enviaMail = $enviaMail;
    }

    /**
     * @var integer
     *
     * @ORM\Column(name="kgFijoPuertaPuerta", type="decimal", scale=2, nullable=true)

     */
    private $kgFijoPuertaPuerta = 0;

    /**
     * @var integer
     *
     * @ORM\Column(name="importeFijoPuertaPuerta", type="decimal", scale=2, nullable=true)

     */
    private $importeFijoPuertaPuerta = 0;

    /**
     * @return int
     */
    public function getKgFijoPuertaPuerta()
    {
        return $this->kgFijoPuertaPuerta;
    }

    /**
     * @param int $kgFijoPuertaPuerta
     */
    public function setKgFijoPuertaPuerta($kgFijoPuertaPuerta)
    {
        $this->kgFijoPuertaPuerta = $kgFijoPuertaPuerta;
    }

    /**
     * @return int
     */
    public function getImporteFijoPuertaPuerta()
    {
        return $this->importeFijoPuertaPuerta;
    }

    /**
     * @param int $importeFijoPuertaPuerta
     */
    public function setImporteFijoPuertaPuerta($importeFijoPuertaPuerta)
    {
        $this->importeFijoPuertaPuerta = $importeFijoPuertaPuerta;
    }


    /**
     * @var boolean
     *
     * @ORM\Column(name="enviaMailOrigen", type="boolean", nullable=true)

     */
    private $enviaMailOrigen;

    /**
     * @return boolean
     */
    public function isEnviaMailOrigen()
    {
        return $this->enviaMailOrigen;
    }

    /**
     * @param boolean $enviaMailOrigen
     */
    public function setEnviaMailOrigen($enviaMailOrigen)
    {
        $this->enviaMailOrigen = $enviaMailOrigen;
    }



}
