<?php

namespace Presis\RetiroBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use JMS\Serializer\Annotation\Type;
use Presis\RetiroBundle\PresisRetiroBundle;
use Symfony\Component\Validator\Constraints as Assert;
use Presis\RetiroBundle\Validator\Constraint\CheckGeneralList as CheckRetiroAssert;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;
use JMS\Serializer\Annotation\VirtualProperty;
use JMS\Serializer\Annotation\SerializedName;
use Presis\DatosEnviosBundle\Entity\DatosEnvios;
use Presis\EstadoBundle\Entity\Estado;
use Presis\GestionCelBundle\Entity\GestionCel;

/**
 * Retiro
 *
 * @ORM\Table(name="retiro")
 * @ORM\Entity(repositoryClass="Presis\RetiroBundle\Entity\RetiroRepository")
 * @CheckRetiroAssert
 *
 * @ExclusionPolicy("all")
 */
class Retiro
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     *
     * @Expose
     */
    private $id;

    /*
     * Retorna el comprador si lo tiene. Sino devuelve un objeto vacío, para que al menos
     * pueda consultar sus atributos vacíos
     *
     * @return \Presis\CompradorBundle\Entity\Comprador
     */
    private function getCompradorEntity() {
        return ($this->getComprador())?$this->getComprador():new Comprador();
    }

    /**
     * @VirtualProperty
     * @SerializedName("comprador")
     *
     * @return string
     */
    public function getNombreComprador()
    {
        return $this->getCompradorEntity()->getApenom()." ";
    }

    /**
     * @VirtualProperty
     * @SerializedName("tipoOperacion")
     *
     * @return string
     */
    public function getTipoOperacion()
    {
        return $this->getDatosEnvios()->getTipoOp();
    }

    /**
     * @VirtualProperty
     * @SerializedName("comprador_direccion")
     */
    public function getDireccionComprador()
    {
        $c = $this->getCompradorEntity();
        return $c->getCalle()." ".$c->getAltura()." ".$c->getPiso()." ".$c->getDpto();
    }

    /**
     * @VirtualProperty
     * @SerializedName("comprador_email")
     */
    public function getEmailComprador()
    {
        //TODO: Hacer que todos los campor string tengan "" por defecto
        return $this->getCompradorEntity()->getEmail()."";
    }

    /**
     * @VirtualProperty
     * @SerializedName("comprador_kms")
     */
    public function getCompradorKms()
    {
        //TODO: Hacer que todos los campor string tengan "" por defecto
        return $this->getCompradorEntity()->getKms()."";
    }

    /**
     * @VirtualProperty
     * @SerializedName("comprador_localidad")
     */
    public function getLocalidadComprador()
    {
        $c = $this->getCompradorEntity();
        $s = ($c->getLocalidad() != "" && $c->getProvincia() != "")?", ":" ";
        return $c->getLocalidad() . $s . $c->getProvincia();
    }


    /**
     * @VirtualProperty
     * @SerializedName("comprador_localidad_guia")
     */
    public function getLocalidadCompradorGuia()
    {
        $c = $this->getCompradorEntity();
        return $c->getLocalidad()."";
    }

    /**
     * @VirtualProperty
     * @SerializedName("comprador_provincia_guia")
     */
    public function getProvinciaCompradorGuia()
    {
        $c = $this->getCompradorEntity();
        return $c->getProvincia()."";
    }

    /**
     * @VirtualProperty
     * @SerializedName("comprador_celular")
     */
    public function getCelularCompradorGuia()
    {
        $c = $this->getCompradorEntity();
        return $c->getCelular()."";
    }

    /**
     * @VirtualProperty
     * @SerializedName("comprador_otherinfo")
     */
    public function getOtherInfoCompradorGuia()
    {
        $c = $this->getCompradorEntity();
        return $c->getOtherInfo()."";
    }

    /**
     * @VirtualProperty
     * @SerializedName("comprador_cp_guia")
     */
    public function getCpCompradorGuia()
    {
        $c = $this->getCompradorEntity();
        return $c->getCp()."";
    }


    /**
     * @VirtualProperty
     * @SerializedName("comprador_empresa_guia")
     */
    public function getEmpresaCompradorGuia()
    {
        $c = $this->getCompradorEntity();
        return $c->getEmpresa()."";
    }

    /**
     * @var integer
     * @ORM\Column(name="rango", type="integer",nullable=true)
     * @Assert\Range(
     *      min = 0,
     *      max = 99999,
     *      minMessage = "El rango debe ser positivo",
     *      maxMessage = "El rango no debe ser mayor a 99999")
     */
    private $rango;

    /**
     * @ORM\ManyToOne(targetEntity="Presis\EstadoBundle\Entity\Estado", inversedBy="retiros")
     * @ORM\JoinColumn(name="estado_id", referencedColumnName="id")
     * @Expose
     */
    private $estado;

    /**
     * @ORM\ManyToOne(targetEntity="Presis\RetiroBundle\Entity\Motivo", inversedBy="retiros")
     * @ORM\JoinColumn(name="motivo_id", referencedColumnName="id")
     */
    protected $motivo;

    /**
     * @ORM\OneToMany(targetEntity="Presis\TrackerBundle\Entity\Tracker", mappedBy="retiro", cascade={"persist", "remove"})
     */
    protected $historicos;

    /**
     * @ORM\OneToMany(targetEntity="Presis\ReclamoBundle\Entity\Reclamo", mappedBy="retiro")
     */
    protected $reclamos;

    /**
     * @ORM\OneToMany(targetEntity="Presis\RecorridoBundle\Entity\RecorridoRetiro", mappedBy="retiro", cascade={"persist", "remove"})
     */
    private $recorridos_retiros;


    
    /**
     * @return boolean
     */
    public function getEnPresis()
    {
        return $this->enPresis;
    }

    /**
     * @param boolean $enPresis
     */
    public function setEnPresis($enPresis)
    {
        $this->enPresis = $enPresis;
    }

    /**
     * @return int
     */
    public function getRango()
    {
        return $this->rango;
    }

    /**
     * @param int $rango
     */
    public function setRango($rango)
    {
        $this->rango = $rango;
    }
    
    /**
     * @var datetime
     *
     * @ORM\Column(name="fecha_hora", type="datetime",nullable=true)
     * @Expose
     */
    private $fechHora;
    /**
     * @var decimal
     *
     * @ORM\Column(name="peso", type="decimal", scale=3, nullable=true)
     */
    private $peso;
    /**
     * @var decimal
     *
     * @ORM\Column(name="precio", type="decimal",scale=2,nullable=true)
     */
    private $precio;
    /**
     * @var boolean
     *
     * @ORM\Column(name="is_fragil", type="boolean",nullable=true)
     */
    private $fragil=false;
    /**
     * @var boolean
     *
     * @ORM\Column(name="en_presis", type="boolean",nullable=true)
     */
    private $enPresis=false;
    /**
     * @var boolean
     *
     * @ORM\Column(name="impreso", type="boolean",nullable=true)
     * @Expose
     */
    private $impreso=false;
    /**
     * @var boolean
     *
     * @ORM\Column(name="rendido", type="boolean",nullable=true)
     */
    private $rendido=false;

    /**
     * @ORM\OneToOne(targetEntity="Presis\RetiroBundle\Entity\Comprador",cascade={"persist","remove"})
     * @ORM\JoinColumn(name="comprador_id", referencedColumnName="id",nullable=false)
     *
     */
    private $comprador;

    /**
     * @ORM\OneToOne(targetEntity="Presis\RetiroBundle\Entity\DatosPrestador",cascade={"persist"})
     * @ORM\JoinColumn(name="prestador_id", referencedColumnName="id",nullable=true)
     */
    private $prestador;
    /**
     * @ORM\OneToOne(targetEntity="Presis\RetiroBundle\Entity\Sender",cascade={"persist","remove"})
     * @ORM\JoinColumn(name="sender_id", referencedColumnName="id",nullable=true)
     * @Expose
     */
    private $sender;

    /**
     * @ORM\ManyToOne(targetEntity="Presis\RetiroBundle\Entity\FranjaEntrega")
     * @ORM\JoinColumn(name="franja_id", referencedColumnName="id",nullable=true)
     **/
    private $franja;
    /**
     * @ORM\ManyToOne(targetEntity="Presis\ServicioBundle\Entity\Servicio")
     * @ORM\JoinColumn(name="servicio_id", referencedColumnName="id")
     * @Expose
     **/
    private $servicio;

    /**
     * @ORM\ManyToOne(targetEntity="Presis\GeneralBundle\Entity\Sucursal")
     * @ORM\JoinColumn(name="sucursal_id", referencedColumnName="id",onDelete="cascade")
     **/
    private $sucursal;

    /**
     * @VirtualProperty
     * @SerializedName("sucursal")
     */
    public function getSucursalNombre()
    {
        $name = ($this->getSucursal())?$this->getSucursal()->getDescripcion():"";

        return $name;
    }
    /**
     * @ORM\ManyToOne(targetEntity="Presis\GeneralBundle\Entity\Cliente")
     * @ORM\JoinColumn(name="cliente_id", referencedColumnName="id",onDelete="cascade")
     * @Expose
     **/
    private $cliente;

    /**
     * @VirtualProperty
     * @SerializedName("clientenombre")
     */
    public function getClienteNombre()
    {
        $name = ($this->getCliente())?$this->getCliente()->getEmpresa():"";

        return $name;
    }

    /**
     * @ORM\ManyToOne(targetEntity="Presis\ServicioBundle\Entity\Cordon")
     * @ORM\JoinColumn(name="cordon_entrega_id", referencedColumnName="id")
     **/
    private $cordonEntrega;
    // ...
    /**
     * @ORM\OneToMany(targetEntity="Presis\RetiroBundle\Entity\Producto", mappedBy="retiro",cascade={"persist","remove"})
     *
     * @Assert\Count(
     *      min = "0",
     *      max = "99",
     *      minMessage = "Debe cargar al menos un producto",
     *      maxMessage = "You cannot specify more than {{ limit }} emails"
     * )
     */
    private $productos;

    /**
     * @return boolean
     */
    public function getImpreso()
    {
        return $this->impreso;
    }

    /**
     * @param boolean $impreso
     */
    public function setImpreso($impreso)
    {
        $this->impreso = $impreso;
    }

    /**
     * @return boolean
     */
    public function isRendido()
    {
        return $this->rendido;
    }

    /**
     * @param boolean $rendido
     */
    public function setRendido($rendido)
    {
        $this->rendido = $rendido;
    }

    /**
     * @return mixed
     */
    public function getFranja()
    {
        return $this->franja;
    }

    /**
     * @param mixed $franja
     */
    public function setFranja($franja)
    {
        $this->franja = $franja;
    }


    public function __construct(){
        $this->productos = new ArrayCollection();
        $this->historicos = new ArrayCollection();
        $this->recorridos_retiros = new ArrayCollection();
		$this->datosEnvios=new DatosEnvios();
		$this->gestioncel=new GestionCel();
        //$this->fechHora = new \DateTime();
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
     * @return mixed
     */
    public function getCliente()
    {
        return $this->cliente;
    }

    /**
     * @return mixed
     */
    public function getClienteId()
    {
        return ($this->getCliente())?$this->getCliente()->getId():null;
    }

    /**
     * @param mixed $cliente
     */
    public function setCliente($cliente)
    {
        $this->cliente = $cliente;
    }

    /**
     * @return mixed
     */
    public function getComprador()
    {
        return $this->comprador;
    }

    /**
     * @param mixed $comprador
     */
    public function setComprador($comprador)
    {
        $this->comprador = $comprador;
    }

    /**
     * @return mixed
     */
    public function getCordonEntrega()
    {
        return $this->cordonEntrega;
    }

    /**
     * @param mixed $cordonEntrega
     */
    public function setCordonEntrega($cordonEntrega)
    {
        $this->cordonEntrega = $cordonEntrega;
    }

    /**
     * @return datetime
     */
    public function getFechHora()
    {
        return $this->fechHora;
    }

    /**
     * @param datetime $fechHora
     */
    public function setFechHora($fechHora)
    {
        $this->fechHora = $fechHora;
    }

    /**
     * @return boolean
     */
    public function getFragil()
    {
        return $this->fragil;
    }

    /**
     * @param boolean $fragil
     */
    public function setFragil($fragil)
    {
        $this->fragil = $fragil;
    }

    /**
     * @return decimal
     */
    public function getPrecio()
    {
        return $this->precio;
    }

    /**
     * @param decimal $precio
     */
    public function setPrecio($precio)
    {
        $this->precio = $precio;
    }

    /**
     * @return mixed
     */
    public function getSender()
    {
        return $this->sender;
    }

    /**
     * @param mixed $sender
     */
    public function setSender($sender)
    {
        $this->sender = $sender;
    }

    /**
     * @return mixed
     */
    public function getServicio()
    {
        return $this->servicio;
    }

    /**
     * @VirtualProperty
     * @SerializedName("servicio_nombre")
     *
     * @return string
     */
    public function getServicioNombre()
    {
        return ($this->getServicio())?$this->getServicio()->getDescripcion():"";
    }

    /**
     * @param mixed $servicio
     */
    public function setServicio($servicio)
    {
        $this->servicio = $servicio;
    }

    /**
     * @return mixed
     */
    public function getSucursal()
    {
        return $this->sucursal;
    }

    /**
     * @param mixed $sucursal
     */
    public function setSucursal($sucursal)
    {
        $this->sucursal = $sucursal;
    }

    /**
     * @return decimal
     */
    public function getPeso()
    {
        return $this->peso;
    }

    /**
     * @param decimal $peso
     */
    public function setPeso($peso)
    {
        $this->peso = $peso;
    }

    /**
     * @return mixed
     */
    public function getProductos()
    {
        return $this->productos;
    }

    /**
     * @param mixed $productos
     */
    public function setProductos($productos)
    {
        $this->productos = $productos;
    }

    public function __toString(){
        return $this->getId()."";
    }

    /**
     * @return mixed
     */
    public function getPrestador()
    {
        return $this->prestador;
    }

    /**
     * @param mixed $prestador
     */
    public function setPrestador($prestador)
    {
        $this->prestador = $prestador;
    }


    /**
     * Set estado
     * Actualiza el histórico
     *
     * @param \Presis\EstadoBundle\Entity\Estado $estado
     *
     * @return Retiro
     */
    public function setEstado(\Presis\EstadoBundle\Entity\Estado $estado = null)
    {
        $this->estado = $estado;

        return $this;
    }

    /**
     * Get estado
     *
     * @return \Presis\EstadoBundle\Entity\Estado
     */
    public function getEstado()
    {
        return $this->estado;
    }

    /**
     * @VirtualProperty
     * @SerializedName("estado_codigo")
     *
     * @return string
     */
    public function getEstadoCodigo()
    {
        return ($this->getEstado())?$this->getEstado()->getCodigo():"";
    }

    /**
     * @VirtualProperty
     * @SerializedName("estado_nombre")
     *
     * @return string
     */
    public function getEstadoNombre()
    {
        return ($this->getEstado())?$this->getEstado()->getNombre():"";
    }

    /**
     * Add producto
     *
     * @param \Presis\RetiroBundle\Entity\Producto $producto
     *
     * @return Retiro
     */
    public function addProducto(\Presis\RetiroBundle\Entity\Producto $producto)
    {
        $this->productos[] = $producto;

        return $this;
    }

    /**
     * Remove producto
     *
     * @param \Presis\RetiroBundle\Entity\Producto $producto
     */
    public function removeProducto(\Presis\RetiroBundle\Entity\Producto $producto)
    {
        $this->productos->removeElement($producto);
    }

    /**
     * Set motivo
     *
     * @param \Presis\RetiroBundle\Entity\Motivo $motivo
     *
     * @return Retiro
     */
    public function setMotivo(\Presis\RetiroBundle\Entity\Motivo $motivo = null)
    {
        $this->motivo = $motivo;

        return $this;
    }

    /**
     * Get motivo
     *
     * @return \Presis\RetiroBundle\Entity\Motivo
     */
    public function getMotivo()
    {
        return $this->motivo;
    }

    /**
     * Add historico
     *
     * @param \Presis\TrackerBundle\Entity\Tracker $historico
     *
     * @return Retiro
     */
    public function addHistorico(\Presis\TrackerBundle\Entity\Tracker $historico)
    {
        $this->historicos[] = $historico;

        return $this;
    }

    /**
     * Remove historico
     *
     * @param \Presis\TrackerBundle\Entity\Tracker $historico
     */
    public function removeHistorico(\Presis\TrackerBundle\Entity\Tracker $historico)
    {
        $this->historicos->removeElement($historico);
    }

    /**
     * Get historicos
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getHistoricos()
    {
        return $this->historicos;
    }

    /**
     * Add reclamo
     *
     * @param \Presis\ReclamoBundle\Entity\Reclamo $reclamo
     *
     * @return Retiro
     */
    public function addReclamo(\Presis\ReclamoBundle\Entity\Reclamo $reclamo)
    {
        $this->reclamos[] = $reclamo;

        return $this;
    }

    /**
     * Remove reclamo
     *
     * @param \Presis\ReclamoBundle\Entity\Reclamo $reclamo
     */
    public function removeReclamo(\Presis\ReclamoBundle\Entity\Reclamo $reclamo)
    {
        $this->reclamos->removeElement($reclamo);
    }

    /**
     * Get reclamos
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getReclamos()
    {
        return $this->reclamos;
    }

    /**
     * Retorna si es una pieza que se pueda agregar a una Planilla de recorrido
     *
     * @return boolean
     */
    public function isSeleccionableParaRecorrido()
    {
        $returnValue = true;

        if($this->getEstado() && !$this->getEstado()->isSeleccionableParaRecorrido())
            $returnValue = false;

        return $returnValue;
    }
    
    /*====================PICCINI========================*/



    
    /**
     * @ORM\OneToOne(targetEntity="Presis\DatosEnviosBundle\Entity\DatosEnvios",cascade={"persist","remove"}, inversedBy="retiro")
     * @ORM\JoinColumn(name="datosenvios_id", referencedColumnName="id",nullable=true)
     * @Expose
     */
    private $datosEnvios;
    
    public function getDatosEnvios()
    {
        return ($this->datosEnvios)?$this->datosEnvios:"";
    }

    /**
     * @VirtualProperty
     * @SerializedName("remitente")
     *
     * @return string
     */
    public function getRemitenteNombre()
    {
        return ($this->getSender())?$this->getSender()->getEmpresa():"";
    }

    /**
     * @VirtualProperty
     * @SerializedName("remitente_empresa")
     *
     * @return string
     */
    public function getRemitenteEmpresa()
    {
        return $this->getSender()->getEmpresa()."";
    }

    /**
     * @VirtualProperty
     * @SerializedName("remitente_remitente")
     *
     * @return string
     */
    public function getRemitenteRemitente()
    {
        return $this->getSender()->getRemitente()."";
    }

    /**
     * @VirtualProperty
     * @SerializedName("remitente_direccion")
     *
     * @return string
     */
    public function getRemitenteDireccion()
    {
        return $this->getSender()->getDireccion()."";
    }

    /**
     * @VirtualProperty
     * @SerializedName("remitente_localidad")
     *
     * @return string
     */
    public function getRemitenteLocalidad()
    {
        return $this->getSender()->getLocalidad()."";
    }

    /**
     * @VirtualProperty
     * @SerializedName("remitente_provincia")
     *
     * @return string
     */
    public function getRemitenteProvincia()
    {
        return $this->getSender()->getProvincia()."";
    }

    /**
     * @VirtualProperty
     * @SerializedName("destinatario")
     *
     * @return string
     */
    public function getDestinatarioNombre()
    {
        return ($this->getDatosEnvios()->getDestinatario())?$this->getDatosEnvios()->getDestinatario()->getEmpresa():"";
    }

    /**
     * @VirtualProperty
     * @SerializedName("pagoEn")
     *
     * @return string
     */
    public function getPagoEn()
    {
        return $this->getDatosEnvios()->getPagoEn();
    }

    /**
     * @VirtualProperty
     * @SerializedName("localidad_remitente")
     *
     * @return string
     */
    public function getLocalidadRemitenteNombre()
    {
        return $this->getSender()->getLocalidad()."";
    }

    /**
     * @VirtualProperty
     * @SerializedName("provincia_remitente")
     *
     * @return string
     */
    public function getProvinciaRemitenteNombre()
    {
        return $this->getSender()->getProvincia()."";
    }

    /**
     * @VirtualProperty
     * @SerializedName("cp_remitente")
     *
     * @return string
     */
    public function getCpRemitenteNombre()
    {
        return $this->getSender()->getCp()."";
    }

    /**
     * @VirtualProperty
     * @SerializedName("otherInfo_remitente")
     *
     * @return string
     */
    public function getOtherInfoRemitenteNombre()
    {
        return $this->getSender()->getOtherInfo()."";
    }

    /**
     * @VirtualProperty
     * @SerializedName("celular_remitente")
     *
     * @return string
     */
    public function getCelularRemitenteNombre()
    {
        return $this->getSender()->getCelular()."";
    }


    /**
     * @VirtualProperty
     * @SerializedName("localidad_destinatario")
     *
     * @return string
     */
    public function getLocalidadDestinatarioNombre()
    {
        return $this->getComprador()->getLocalidad();
    }

    /**
     * @VirtualProperty
     * @SerializedName("compradorEmpresa")
     *
     * @return string
     */
    public function getCompradorEmpresa()
    {
        return $this->getComprador()->getEmpresa();
    }

    /**
     * @VirtualProperty
     * @SerializedName("compradorCuit")
     *
     * @return string
     */
    public function getCompradorCuit()
    {
        return $this->getComprador()->getCuit();
    }

    /**
     * @VirtualProperty
     * @SerializedName("cp_destinatario")
     *
     * @return string
     */
    public function getCpDestinatario()
    {
        return $this->getComprador()->getCp();
    }

    /**
     * @VirtualProperty
     * @SerializedName("provincia_destinatario")
     *
     * @return string
     */
    public function getProvinciaDestinatario()
    {
        return $this->getComprador()->getProvincia();
    }

    /**
 * @VirtualProperty
 * @SerializedName("direccion_remitente")
 *
 * @return string
 */
    public function getDireccionRemitenteNombre()
    {
        $direccion = $this->getSender()->getCalle().' '.
            $this->getSender()->getAltura().' '.
            $this->getSender()->getPiso().' '.
            $this->getSender()->getDpto();
        return ($direccion)?$direccion:"";
    }

    /**
     * @VirtualProperty
     * @SerializedName("direccion_destinatario")
     *
     * @return string
     */
    public function getDireccionDestinatarioNombre()
    {
        $direccion = $this->getComprador()->getCalle().' '.
            $this->getComprador()->getAltura().' '.
            $this->getComprador()->getPiso().' '.
            $this->getComprador()->getDpto();
        return ($direccion)?$direccion:"";
    }


    public function getCantidad(){
		if (isset($this->datosEnvios)){
		return $this->datosEnvios->getCantreal();
		
		}else{
			return null;
		}
	}

	public function setCantidad($cantidad){
	if (isset($this->datosEnvios)){
		$this->datosEnvios->setCantreal($cantidad);
		}
	}

    public function setDatosEnvios($datosEnvios)
    {
        $this->datosEnvios = $datosEnvios;

        return $this;
    }

    /**
     * Add recorridosRetiro
     *
     * @param \Presis\RecorridoBundle\Entity\RecorridoRetiro $recorridosRetiro
     *
     * @return Retiro
     */
    public function addRecorridosRetiro(\Presis\RecorridoBundle\Entity\RecorridoRetiro $recorridosRetiro)
    {
        $this->recorridos_retiros[] = $recorridosRetiro;

        return $this;
    }

    /**
     * Remove recorridosRetiro
     *
     * @param \Presis\RecorridoBundle\Entity\RecorridoRetiro $recorridosRetiro
     */
    public function removeRecorridosRetiro(\Presis\RecorridoBundle\Entity\RecorridoRetiro $recorridosRetiro)
    {
        $this->recorridos_retiros->removeElement($recorridosRetiro);
    }

    /**
     * Get recorridosRetiros
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getRecorridosRetiros()
    {
        return $this->recorridos_retiros;
    }

    /*==========================AGREGO PICCINI======================================*/

    /**
     * @var string
     *
     * @ORM\Column(name="remito", type="string", length=45, nullable=true)
     * @Expose
     */
    private $remito;

    /**
     * Set remito
     *
     * @param string $remito
     *
     * @return PresisRetiroBundle
     */
    public function setRemito($remito)
    {
        $this->remito = $remito;

        return $this;
    }

    /**
     * Get remito
     *
     * @return string
     */
    public function getRemito()
    {
        return $this->remito."";
    }

    /*==========================================PICCINI===============================*/

    /**
     * @var integer
     *
     * @ORM\Column(name="nroPlanilla", type="integer", nullable=true)
     * @Expose
     */
    protected $nroPlanilla;

    /**
     * @return mixed
     */
    public function getNroPlanilla()
    {
        return $this->nroPlanilla;
    }

    /**
     * @param mixed $nroPlanilla
     */
    public function setNroPlanilla($nroPlanilla)
    {
        $this->nroPlanilla = $nroPlanilla;
    }

    /**
     * @var date
     *
     * @ORM\Column(name="fechaplanilla", type="date",nullable=true)
     * @Expose
     */
    protected $fechaPlanilla;

    /**
     * @return date
     */
    public function getFechaPlanilla()
    {
        return $this->fechaPlanilla;
    }

    /**
     * @param date $fechaPlanilla
     */
    public function setFechaPlanilla($fechaPlanilla)
    {
        $this->fechaPlanilla = $fechaPlanilla;
    }

    /**
     * @var string
     *
     * @ORM\Column(name="zona", type="string", length=20, nullable=true)
     * @Expose
     */
    protected $zona;

    /**
     * @var string
     *
     * @ORM\Column(name="zonaOrigen", type="string", length=20, nullable=true)
     * @Expose
     */
    protected $zonaOrigen;

    /**
     * @return string
     */
    public function getZona()
    {
        return $this->zona."";
    }

    /**
     * @param string $zona
     */
    public function setZona($zona)
    {
        $this->zona = $zona;
    }

    /**
     * @var date
     *
     * @ORM\Column(name="fechaUltimoEstado", type="date",nullable=true)
     * @Expose
     */
    protected $fechaUltimoEstado;

    /**
     * @return date
     */
    public function getFechaUltimoEstado()
    {
        return $this->fechaUltimoEstado;
    }

    /**
     * @param date $fechaUltimoEstado
     */
    public function setFechaUltimoEstado($fechaUltimoEstado)
    {
        $this->fechaUltimoEstado = $fechaUltimoEstado;
    }

    /**
     * @var string
     *
     * @ORM\Column(name="distribuidor", type="string", length=45, nullable=true)
     * @Expose
     */
    protected $distribuidor;

    /**
     * @return string
     */
    public function getDistribuidor()
    {
        return $this->distribuidor."";
    }

    /**
     * @param string $distribuidor
     */
    public function setDistribuidor($distribuidor)
    {
        $this->distribuidor = $distribuidor;
    }


    /**
     * @VirtualProperty
     * @SerializedName("fu")
     *
     * @return string
     */
    public function getFu()
    {
        return $this->getFechaUltimoEstado();
    }

    /**
     * @VirtualProperty
     * @SerializedName("fOrigen")
     *
     * @return string
     */
    public function getForigen()
    {
        return $this->getFechHora();
    }


    /**
     * @VirtualProperty
     * @SerializedName("numeroplani")
     *
     * @return string
     */
    public function getNumeroPlani()
    {
        return $this->getNroPlanilla();
    }

    /**
     * @VirtualProperty
     * @SerializedName("fechaUPlanilla")
     *
     * @return string
     */
    public function getFup()
    {
        return $this->getFechaPlanilla();
    }


    /*========================================================*/

    //AGREGADO 19-12 A PEDIDO DE FASTTRACK
    /**
     * @var string
     *
     * @ORM\Column(name="detalle_entrega", type="string", length=100, nullable=true)
     * @Expose
     */
    private $detalleEntrega;

    /**
     * @var datetime
     *
     * @ORM\Column(name="fecha_hora_entrega", type="datetime",nullable=true)
     * @Expose
     */
    private $fechaHoraEntrega;

    /**
     * @var string
     *
     * @ORM\Column(name="receptor_nombre", type="string", length=100, nullable=true)
     * @Expose
     */
    private $receptorNombre;

    /**
     * @var string
     *
     * @ORM\Column(name="receptor_apellido", type="string", length=100, nullable=true)
     * @Expose
     */
    private $receptorApellido;

    /**
     * @var string
     *
     * @ORM\Column(name="receptor_dni", type="string", length=15, nullable=true)
     * @Expose
     */
    private $dni;


    /**
     * @return string
     */
    public function getDetalleEntrega()
    {
        return $this->detalleEntrega;
    }

    /**
     * @param string $detalleEntrega
     */
    public function setDetalleEntrega($detalleEntrega)
    {
        $this->detalleEntrega = $detalleEntrega;
    }

    /**
     * @return datetime
     */
    public function getFechaHoraEntrega()
    {
        return $this->fechaHoraEntrega;
    }

    /**
     * @param datetime $fechaHoraEntrega
     */
    public function setFechaHoraEntrega($fechaHoraEntrega)
    {
        $this->fechaHoraEntrega = $fechaHoraEntrega;
    }

    /**
     * @return string
     */
    public function getReceptorNombre()
    {
        return $this->receptorNombre;
    }

    /**
     * @param string $receptorNombre
     */
    public function setReceptorNombre($receptorNombre)
    {
        $this->receptorNombre = $receptorNombre;
    }

    /**
     * @return string
     */
    public function getReceptorApellido()
    {
        return $this->receptorApellido;
    }

    /**
     * @param string $receptorApellido
     */
    public function setReceptorApellido($receptorApellido)
    {
        $this->receptorApellido = $receptorApellido;
    }

    /**
     * @return string
     */
    public function getDni()
    {
        return $this->dni;
    }

    /**
     * @param string $dni
     */
    public function setDni($dni)
    {
        $this->dni = $dni;
    }

    /**
     * @VirtualProperty
     * @SerializedName("fecha_plani")
     */
    public function getFechaPlani()
    {
        return $this->getFechaPlanilla();
    }

    /**
     * @var boolean
     *
     * @ORM\Column(name="sendMail", type="boolean",nullable=true)
     */
    private $sendMail = false;

    /**
     * @return boolean
     */
    public function isSendMail()
    {
        return $this->sendMail;
    }

    /**
     * @param boolean $sendMail
     */
    public function setSendMail($sendMail)
    {
        $this->sendMail = $sendMail;
    }


    /**
     * @VirtualProperty
     * @SerializedName("impresa")
     */
    public function getImpresa()
    {
        if($this->getImpreso()==false){
            $impresa = "NO";
        }else{
            $impresa = "SI";
        }

        return $impresa;
    }

    /**
     * @var string
     *
     * @ORM\Column(name="createGuia", type="string", length=255, nullable=true)
     * @Expose
     */
    private $createguia;

    /**
     * @return mixed
     */
    public function getCreateguia()
    {
        return $this->createguia;
    }

    /**
     * @param mixed $createguia
     */
    public function setCreateguia($createguia)
    {
        $this->createguia = $createguia;
    }

    /**
     * @return string
     */
    public function getZonaOrigen()
    {
        return $this->zonaOrigen;
    }

    /**
     * @param string $zonaOrigen
     */
    public function setZonaOrigen($zonaOrigen)
    {
        $this->zonaOrigen = $zonaOrigen;
    }

    // PICCINI 16-01-17 - CAMPOS PARA PLANILLA CAKTUS

    /**
     * @VirtualProperty
     * @SerializedName("comprador_documento")
     *
     * @return string
     */
    public function getCompradorDocumento()
    {
        $c = $this->getCompradorEntity();
        return $c->getDocumento();
    }

    /**
     * @VirtualProperty
     * @SerializedName("comprador_horario")
     *
     * @return string
     */
    public function getCompradorHorario()
    {
        $c = $this->getCompradorEntity();
        return $c->getHorario();
    }

    /**
     * @VirtualProperty
     * @SerializedName("comprador_obs1")
     *
     * @return string
     */
    public function getCompradorObs1()
    {
        $c = $this->getCompradorEntity();
        return $c->getObs1();
    }

    /**
     * @VirtualProperty
     * @SerializedName("comprador_obs2")
     *
     * @return string
     */
    public function getCompradorObs2()
    {
        $c = $this->getCompradorEntity();
        return $c->getObs2();
    }

    /**
     * @VirtualProperty
     * @SerializedName("comprador_obs3")
     *
     * @return string
     */
    public function getCompradorObs3()
    {
        $c = $this->getCompradorEntity();
        return $c->getObs3();
    }

    /**
     * @VirtualProperty
     * @SerializedName("comprador_obs4")
     *
     * @return string
     */
    public function getCompradorObs4()
    {
        $c = $this->getCompradorEntity();
        return $c->getObs4();
    }

    /**
     * @VirtualProperty
     * @SerializedName("comprador_zona")
     *
     * @return string
     */
    public function getCompradorZona()
    {
        $c = $this->getCompradorEntity();
        return $c->getZona();
    }

    /**
     * @VirtualProperty
     * @SerializedName("comprador_estado")
     *
     * @return string
     */
    public function getCompradorEstado()
    {
        $c = $this->getCompradorEntity();
        return $c->getObsEstado();
    }

    /**
     * @VirtualProperty
     * @SerializedName("comprador_celular")
     *
     * @return string
     */
    public function getCompradorCelular()
    {
        $c = $this->getCompradorEntity();
        return $c->getCelular()."";
    }

    /**
     * @var integer
     *
     * @ORM\Column(name="nro_constancia", type="integer", nullable=true)
     * @Expose
     */
    private $nroConstancia;

    /**
     * @return int
     */
    public function getNroConstancia()
    {
        return $this->nroConstancia;
    }

    /**
     * @param int $nroConstancia
     */
    public function setNroConstancia($nroConstancia)
    {
        $this->nroConstancia = $nroConstancia;
    }

    /**
     * @VirtualProperty
     * @SerializedName("cobrado")
     */
    public function getCobrado()
    {
        return $this->getDatosEnvios()->getCobrado();

    }

    /**
     * @var string
     *
     * @ORM\Column(name="email_message", type="string", length=100, nullable=true)
     * @Expose
     */
    private $emailMessage;

    /**
     * @return string
     */
    public function getEmailMessage()
    {
        return $this->emailMessage;
    }

    /**
     * @param string $emailMessage
     */
    public function setEmailMessage($emailMessage)
    {
        $this->emailMessage = $emailMessage;
    }

    /**
     * @var string
     *
     * @ORM\Column(name="subZonaOrigen", type="string", length=255, nullable=true)
     * @Expose
     */
    private $subZonaOrigen;

    /**
     * @var string
     *
     * @ORM\Column(name="subZonaDestino", type="string", length=255, nullable=true)
     * @Expose
     */
    private $subZonaDestino;

    /**
     * @return string
     */
    public function getSubZonaOrigen()
    {
        return $this->subZonaOrigen;
    }

    /**
     * @param string $subZonaOrigen
     */
    public function setSubZonaOrigen($subZonaOrigen)
    {
        $this->subZonaOrigen = $subZonaOrigen;
    }

    /**
     * @return string
     */
    public function getSubZonaDestino()
    {
        return $this->subZonaDestino;
    }

    /**
     * @param string $subZonaDestino
     */
    public function setSubZonaDestino($subZonaDestino)
    {
        $this->subZonaDestino = $subZonaDestino;
    }

    /**
     * @VirtualProperty
     * @SerializedName("compradorEmail")
     *
     * @return string
     */
    public function getCompradorEmail()
    {
        return $this->getComprador()->getEmail();
    }

    /**
     * @VirtualProperty
     * @SerializedName("compradorObs")
     *
     * @return string
     */
    public function getCompradorObs()
    {
        return $this->getComprador()->getOtherInfo();
    }


    /**
     * @VirtualProperty
     * @SerializedName("bultos")
     *
     * @return string
     */
    public function getBultos()
    {
        return $this->getDatosEnvios()->getBultos();
    }

    /**
     * @VirtualProperty
     * @SerializedName("kg")
     *
     * @return string
     */
    public function getKg()
    {
        return $this->getDatosEnvios()->getPeso();
    }


    /**
     * @VirtualProperty
     * @SerializedName("fecha_pactada")
     *
     * @return string
     */
    public function getFechaPactada()
    {
        if($this->getDatosEnvios()->getFechaPactada()){
            return "";
        }else{
            return $this->getDatosEnvios()->getFechaPactada();
        }

    }



    /**
     * Get rendido
     *
     * @return boolean
     */
    public function getRendido()
    {
        return $this->rendido;
    }

    /**
     * Get sendMail
     *
     * @return boolean
     */
    public function getSendMail()
    {
        return $this->sendMail;
    }

/*=============================== VIRTUAL PROPERTIES PARA GRILLA MOVISTAR =======================================*/

    /**
     * @ORM\OneToOne(targetEntity="Presis\GestionCelBundle\Entity\GestionCel",cascade={"persist","remove"}, inversedBy="retiro")
     * @ORM\JoinColumn(name="gestioncel_id", referencedColumnName="id", nullable=true)
     * @Expose
     */
    private $gestioncel;

    /**
     * @return mixed
     */
    public function getGestioncel()
    {
        return $this->gestioncel;
    }

    /**
     * @param mixed $gestioncel
     */
    public function setGestioncel($gestioncel)
    {
        $this->gestioncel = $gestioncel;
    }

    /**
     * @VirtualProperty
     * @SerializedName("imei")
     */
    public function getImei()
    {
        return $this->getGestionCel()->getNroserie();
    }
    
    /**
     * @VirtualProperty
     * @SerializedName("tipocliente")
     */
    public function getTipoCliente()
    {
        return $this->getGestionCel()->getTipocliente();
    }

    /**
     * @VirtualProperty
     * @SerializedName("fabricante")
     */
    public function getFabricante()
    {
        return $this->getGestionCel()->getFabricante();
    }

    /**
     * @VirtualProperty
     * @SerializedName("modelo")
     */
    public function getModelo()
    {
        return $this->getGestionCel()->getModelo();
    }

    /**
     * @VirtualProperty
     * @SerializedName("nombreApellidoMovistar")
     */
    public function getNombreApellidoMovistar()
    {
        return $this->getGestionCel()->getNomyape();
    }

    /**
     * @VirtualProperty
     * @SerializedName("servicioMovistar")
     */
    public function getServicioMovistar()
    {
        return $this->getGestionCel()->getTiposervicio();
    }

    /**
     * @VirtualProperty
     * @SerializedName("servicioMaslo")
     */
    public function getServicioMaslo()
    {
        return $this->getDatosEnvios()->getServicio()->getDescripcion();
    }

    /**
     * @VirtualProperty
     * @SerializedName("estadoMovistar")
     */
    public function getEstadoMovistar()
    {
        return $this->getGestionCel()->getEstado()->getNombre()."";
    }

    /**
     * @VirtualProperty
     * @SerializedName("nombreSucursal")
     */
    public function getNombreSucursal()
    {
        return $this->getGestionCel()->getSucursal()."";
    }


}
