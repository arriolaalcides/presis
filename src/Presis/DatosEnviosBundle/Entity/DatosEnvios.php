<?php

namespace Presis\DatosEnviosBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Presis\DatosEnviosBundle\DatosEnviosBundle;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;
use JMS\Serializer\Annotation\VirtualProperty;
use JMS\Serializer\Annotation\SerializedName;
use Symfony\Component\Validator\Constraints as Assert;
use Presis\RetiroBundle\Validator\Constraint\CheckGeneralList as CheckRetiroAssert;

/**
 * DatosEnvios
 *
 * @ORM\Table(name="datosenvios")
 * @ORM\Entity(repositoryClass="Presis\DatosEnviosBundle\Entity\DatosEnviosRepository")
 * @ExclusionPolicy("all")
 */
class DatosEnvios
{
    /**
     * En envios de y hacia el interior, el envio se despacha el día siguiente
     *
     */
    const CROSS_DOCKING = 24;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;


    /**
     * @var integer
     * @ORM\Column(name="bultos", type="integer", nullable=true)
     * @Expose
     * @Assert\Range(
     *      min = 0,
     *      max = 99999,
     *      minMessage = "La cantidad debe ser positiva",
     *      maxMessage = "El rango no debe ser mayor a 99999")
     */
    private $bultos;

    /**
     * @var float
     *
     * @ORM\Column(name="peso", type="float", nullable=true, columnDefinition="FLOAT", precision=2, scale=2)
     * @Expose
     */
    private $peso = 0;

    /**
     * @var float
     *
     * @ORM\Column(name="volumen", type="float", nullable=true)
     * @Expose
     */
    private $volumen;

    /**
     * @var float
     *
     * @ORM\Column(name="peso_volumetrico", type="float", nullable=true)
     * @Expose
     */
    private $pesoVolumetrico;

    /**
     * @var string
     *
     * @ORM\Column(name="observaciones", type="string", length=500, nullable=true)
     * @Expose
     */
    private $observaciones;

    /**
     * @var float
     *
     * @ORM\Column(name="valor_declarado", type="float", nullable=true)
     * @Expose
     */
    private $valorDeclarado;

    /**
     * @var float
     *
     * @ORM\Column(name="contrareembolso", type="float", nullable=true)
     * @Expose
     */
    private $contrareembolso;

    /**
     * @var float
     *
     * @ORM\Column(name="flete", type="float", nullable=true)
     * @Expose
     */
    private $flete;

    /**
     * @var float
     *
     * @ORM\Column(name="seguro", type="float", nullable=true)
     * @Expose
     */
    private $seguro;

    /**
     * @var booleam
     *
     * @ORM\Column(name="seguro_manual", type="boolean", nullable=true)
     */
    private $seguroManual;

    /**
     * @var booleam
     *
     * @ORM\Column(name="debe_retirarse", type="boolean", nullable=true)
     */
    private $debeRetirarse = true;

    /**
     * @var booleam
     *
     * @ORM\Column(name="confirmada", type="boolean", nullable=true)
     */
    private $confirmada = false;

    /**
     * @VirtualProperty
     * @SerializedName("debe_retirarse")
     *
     * @return string
     */
    public function getDebeRetirarseText()
    {
        return ($this->getDebeRetirarse())?"Sí":"No";
    }

    /**
     * @var float
     *
     * @ORM\Column(name="costo_por_contrareembolso", type="float", nullable=true)
     * @Expose
     */
    private $costoPorContrareembolso;

    /**
     * @var float
     *
     * @ORM\Column(name="ivain", type="float", nullable=true)
     */
    private $ivain;

    /**
     * @var integer
     *
     * @ORM\Column(name="contado", type="integer", nullable=true)
     */
    private $contado;

    /**
     * @var integer
     *
     * @ORM\Column(name="ccorriente", type="integer", nullable=true)
     */
    private $ccorriente;

    /**
     * @var integer
     *
     * @ORM\Column(name="facobrar", type="integer", nullable=true)
     */
    private $facobrar;

    /**
     * @var boolean
     *
     * @ORM\Column(name="contrareembolso_manual", type="boolean", nullable=true)
     */
    private $contrareembolsoManual;

    /**
     * @var integer
     *
     * @ORM\Column(name="cantreal", type="integer", nullable=true)
     */
    private $cantreal;

    /**
     * @var integer
     *
     * @ORM\Column(name="ts", type="integer", nullable=true)
     */
    private $ts;

    /**
     * @var boolean
     *
     * @ORM\Column(name="src", type="boolean", nullable=true)
     *
     */
    private $src;

    /**
     * @var float
     *
     * @ORM\Column(name="costo_src", type="float", nullable=true)
     * @Expose
     */
    private $costoPorRemitoConforme = 0.0;

    /**
     * @var float
     *
     * @ORM\Column(name="costo_despacho_a_expreso", type="float", nullable=true)
     * @Expose
     */
    private $costoDespachoAExpreso = 0.0;

    /**
     * @var float
     *
     * @ORM\Column(name="costo_por_monitoreo_activo", type="float", nullable=true)
     * @Expose
     */
    private $costoPorMonitoreoActivo = 0.0;

    /**
     * @var float
     *
     * @ORM\Column(name="costo_adicional1", type="float", nullable=true)
     * @Expose
     */
    private $costoAdicional1 = 0.0;

    /**
     * @var string
     *
     * @ORM\Column(name="detalle_costo_adicional1", type="string", length=255, nullable=true)
     * @Expose
     */
    private $detalleCostoAdicional1;

    /**
     * @var float
     *
     * @ORM\Column(name="costo_adicional2", type="float", nullable=true)
     * @Expose
     */
    private $costoAdicional2 = 0.0;

    /**
     * @var string
     *
     * @ORM\Column(name="detalle_costo_adicional2", type="string", length=255, nullable=true)
     * @Expose
     */
    private $detalleCostoAdicional2;

    /**
     * @var float
     *
     * @ORM\Column(name="costo_adicional3", type="float", nullable=true)
     * @Expose
     */
    private $costoAdicional3 = 0.0;

    /**
     * @var string
     *
     * @ORM\Column(name="detalle_costo_adicional3", type="string", length=255, nullable=true)
     * @Expose
     */
    private $detalleCostoAdicional3;

    /**
     * @var boolean
     *
     * @ORM\Column(name="csr", type="boolean", nullable=true)
     */
    private $csr;

    /**
     * @var integer
     *
     * @ORM\Column(name="tipo_op", type="integer", nullable=true)
     */
    private $tipoOp;

    /**
     * @var float
     *
     * @ORM\Column(name="alto", type="float", nullable=true)
     * @Expose
     */
    private $alto;

    /**
     * @var float
     *
     * @ORM\Column(name="ancho", type="float", nullable=true)
     * @Expose
     */
    private $ancho;

    /**
     * @var float
     *
     * @ORM\Column(name="largo", type="float", nullable=true)
     * @Expose
     */
    private $largo;
    
    /**
     * @var string
     *
     * @ORM\Column(name="docSpx", type="string", length=255, nullable=true)
     */
    private $docSpx;
    
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fecha", type="datetime", nullable=true)
     * @Expose
     */
    private $fecha;

    /**
     * @ORM\OneToOne(targetEntity="\Presis\RetiroBundle\Entity\Retiro", mappedBy="datosEnvios")
     */
    private $retiro;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fecha_confirmada", type="date", nullable=true)
     * @Expose
     */
    private $fechaConfirmada;

    /**
     * @return \DateTime
     */
    public function getFechaConfirmada()
    {
        return $this->fechaConfirmada;
    }

    /**
     * @param \DateTime $fechaConfirmada
     */
    public function setFechaConfirmada($fechaConfirmada)
    {
        $this->fechaConfirmada = $fechaConfirmada;
    }


    public function __construct()
    {
        $this->fecha = new \DateTime('now');
    }
    
    /**
     * Set docSpx
     *
     * @param string $docSpx
     *
     * @return EnviosBundle
     */
    public function setDocSpx($docSpx)
    {
        $this->docSpx = $docSpx;

        return $this;
    }

    /**
     * Get docSpx
     *
     * @return string
     */
    public function getDocSpx()
    {
        return $this->docSpx;
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
     * Set cantidad
     *
     * @param integer $bultos
     *
     * @return EnviosBundle
     */
    public function setBultos($bultos)
    {
        $this->bultos = $bultos;

        return $this;
    }

    /**
     * Get cantidad
     *
     * @return integer
     */
    public function getBultos()
    {
        return $this->bultos;
    }

    /**
     * Set peso
     *
     * @param float $peso
     *
     * @return EnviosBundle
     */
    public function setPeso($peso)
    {
        $this->peso = $peso;

        return $this;
    }

    /**
     * Get peso
     *
     * @return float
     */
    public function getPeso()
    {
        return $this->peso;
    }

    /**
     * Set volumen
     *
     * @param float $volumen
     *
     * @return EnviosBundle
     */
    public function setVolumen($volumen)
    {
        $this->volumen = $volumen;

        return $this;
    }

    /**
     * Get volumen
     *
     * @return float
     */
    public function getVolumen()
    {
        return $this->volumen;
    }

    /**
     * Set observaciones
     *
     * @param string $observaciones
     *
     * @return EnviosBundle
     */
    public function setObservaciones($observaciones)
    {
        $this->observaciones = $observaciones;

        return $this;
    }

    /**
     * Get observaciones
     *
     * @return string
     */
    public function getObservaciones()
    {
        return $this->observaciones."";
    }

    /**
     * Set vd
     *
     * @param float $valorDeclarado
     *
     * @return EnviosBundle
     */
    public function setValorDeclarado($valorDeclarado)
    {
        $this->valorDeclarado = $valorDeclarado;

        return $this;
    }

    /**
     * Get vd
     *
     * @return float
     */
    public function getValorDeclarado()
    {
        return $this->valorDeclarado;
    }

    /**
     * Set contrareembolso
     *
     * @param float $contrareembolso
     *
     * @return EnviosBundle
     */
    public function setContrareembolso($contrareembolso)
    {
        $this->contrareembolso = $contrareembolso;

        return $this;
    }

    /**
     * Get contrareembolso
     *
     * @return float
     */
    public function getContrareembolso()
    {
        if ($this->contrareembolso)
            return $this->contrareembolso;

        return 0;
    }

    /**
     * Set flete
     *
     * @param float $flete
     *
     * @return EnviosBundle
     */
    public function setFlete($flete)
    {
        $this->flete = $flete;

        return $this;
    }

    /**
     * Get flete
     *
     * @return float
     */
    public function getFlete()
    {
        return $this->flete;
    }

    /**
     * Set seguro
     *
     * @param float $seguro
     *
     * @return EnviosBundle
     */
    public function setSeguro($seguro)
    {
        $this->seguro = $seguro;

        return $this;
    }

    /**
     * Get seguro
     *
     * @return float
     */
    public function getSeguro()
    {
        return $this->seguro;
    }

    /**
     * Set costoPorContrarembolso
     *
     * @param float $costoPorContrareembolso
     *
     * @return EnviosBundle
     */
    public function setCostoPorContrareembolso($costoPorContrareembolso)
    {
        $this->costoPorContrareembolso = $costoPorContrareembolso;

        return $this;
    }

    /**
     * Get costoPorContrarembolso
     *
     * @return float
     */
    public function getCostoPorContrareembolso()
    {
        return $this->costoPorContrareembolso;
    }

    /**
     * Set ivain
     *
     * @param float $ivain
     *
     * @return EnviosBundle
     */
    public function setIvain($ivain)
    {
        $this->ivain = $ivain;

        return $this;
    }

    /**
     * Get ivain
     *
     * @return float
     */
    public function getIvain()
    {
        return $this->ivain;
    }

    /**
     * Calcular total
     *
     * @return DatosEnvios
     */
    public function calcularTotal()
    {
        $this->setTotalFlete(
            $this->getSeguro() +
            $this->getCostoPorContrareembolso() +
            $this->getCustodia() +
            $this->getFlete() +
            $this->getMontoGuiaWeb() +
            $this->getCostoDespachoAExpreso() +
            $this->getCostoPorRemitoConforme() +
            $this->getCostoPorMonitoreoActivo() +
            $this->getCostoAdicional1() +
            $this->getCostoAdicional2() +
            $this->getCostoAdicional3()
        );

        return $this;
    }

    /**
     * Set contado
     *
     * @param integer $contado
     *
     * @return EnviosBundle
     */
    public function setContado($contado)
    {
        $this->contado = $contado;

        return $this;
    }

    /**
     * Get contado
     *
     * @return integer
     */
    public function getContado()
    {
        return $this->contado;
    }

    /**
     * Set ccorriente
     *
     * @param integer $ccorriente
     *
     * @return EnviosBundle
     */
    public function setCcorriente($ccorriente)
    {
        $this->ccorriente = $ccorriente;

        return $this;
    }

    /**
     * Get ccorriente
     *
     * @return integer
     */
    public function getCcorriente()
    {
        return $this->ccorriente;
    }

    /**
     * Set facobrar
     *
     * @param integer $facobrar
     *
     * @return EnviosBundle
     */
    public function setFacobrar($facobrar)
    {
        $this->facobrar = $facobrar;

        return $this;
    }

    /**
     * Get facobrar
     *
     * @return integer
     */
    public function getFacobrar()
    {
        return $this->facobrar;
    }

    /**
     * Set contrareembolso manual
     *
     * @param integer $contrareembolsoManual
     *
     * @return EnviosBundle
     */
    public function setContrareembolsoManual($contrareembolsoManual)
    {
        $this->contrareembolsoManual = $contrareembolsoManual;

        return $this;
    }

    /**
     * Get contrareembolso manual
     *
     * @return integer
     */
    public function getContrareembolsoManual()
    {
        return $this->contrareembolsoManual;
    }

    /**
     * Set cantreal
     *
     * @param integer $cantreal
     *
     * @return EnviosBundle
     */
    public function setCantreal($cantreal)
    {
        $this->cantreal = $cantreal;

        return $this;
    }

    /**
     * Get cantreal
     *
     * @return integer
     */
    public function getCantreal()
    {
        return $this->cantreal;
    }

    /**
     * Set ts
     *
     * @param integer $ts
     *
     * @return EnviosBundle
     */
    public function setTs($ts)
    {
        $this->ts = $ts;

        return $this;
    }

    /**
     * Get ts
     *
     * @return integer
     */
    public function getTs()
    {
        return $this->ts;
    }

    /**
     * Set src
     *
     * @param integer $src
     *
     * @return EnviosBundle
     */
    public function setSrc($src)
    {
        $this->src = $src;

        return $this;
    }

    /**
     * Get src
     *
     * @return integer
     */
    public function getSrc()
    {
        return $this->src;
    }

    /**
     * Set csr
     *
     * @param integer $csr
     *
     * @return EnviosBundle
     */
    public function setCsr($csr)
    {
        $this->csr = $csr;

        return $this;
    }

    /**
     * Get csr
     *
     * @return integer
     */
    public function getCsr()
    {
        return $this->csr;
    }

    /**
     * Set tipoOp
     *
     * @param integer $tipoOp
     *
     * @return EnviosBundle
     */
    public function setTipoOp($tipoOp)
    {
        $this->tipoOp = $tipoOp;

        return $this;
    }

    /**
     * Get tipoOp
     *
     * @return integer
     */
    public function getTipoOp()
    {
        return $this->tipoOp;
    }

    /**
     * Set alto
     *
     * @param float $alto
     *
     * @return EnviosBundle
     */
    public function setAlto($alto)
    {
        $this->alto = $alto;

        return $this;
    }

    /**
     * Get alto
     *
     * @return float
     */
    public function getAlto()
    {
        return $this->alto;
    }

    /**
     * Set ancho
     *
     * @param float $ancho
     *
     * @return EnviosBundle
     */
    public function setAncho($ancho)
    {
        $this->ancho = $ancho;

        return $this;
    }

    /**
     * Get ancho
     *
     * @return float
     */
    public function getAncho()
    {
        return $this->ancho;
    }

    /**
     * Set largo
     *
     * @param float $largo
     *
     * @return EnviosBundle
     */
    public function setLargo($largo)
    {
        $this->largo = $largo;

        return $this;
    }

    /**
     * Get largo
     *
     * @return float
     */
    public function getLargo()
    {
        return $this->largo;
    }

    /**
     * @ORM\ManyToOne(targetEntity="Presis\DestinatariosBundle\Entity\Destinatarios")
     * @ORM\JoinColumn(name="destinatario_id", referencedColumnName="id",onDelete="cascade")
     */
    private $destinatario;
   
    /**
     * @return mixed
     */
    public function getDestinatario()
    {
        return $this->destinatario;
    }

    /**
     * @param mixed $destinatario
     */
    public function setDestinatario($destinatario)
    {
        $this->destinatario = $destinatario;
    }
    
    /**
     * @ORM\ManyToOne(targetEntity="Presis\RemitentesBundle\Entity\Remitente")
     * @ORM\JoinColumn(name="remitente_id", referencedColumnName="id",onDelete="cascade")
    */
    private $remitente;

    /**
     * @ORM\ManyToOne(targetEntity="Presis\CecosBundle\Entity\Cecos")
     * @ORM\JoinColumn(name="ceco_id", referencedColumnName="id",onDelete="cascade",nullable=true)
     */
    private $ceco;

    /**
     * @ORM\ManyToOne(targetEntity="Presis\ServicioBundle\Entity\Servicio")
     * @ORM\JoinColumn(name="servicio_id", referencedColumnName="id")
     * @Expose
     **/
    private $servicio;
   
    /**
     * @return mixed
     */
    public function getRemitente()
    {
        return $this->remitente;
    }

    /**
     * @param mixed $remitente
     */
    public function setRemitente($remitente)
    {
        $this->remitente = $remitente;
    }
    
    /*========================================================================*/
    /**
     * @var string
     *
     * @ORM\Column(name="tipoServicio", type="string", length=255, nullable=true)
     */
    private $tipoServicio;
    
    /**
     * Set tipoServicio
     *
     * @param string $tipoServicio
     *
     * @return EnviosBundle
     */
    public function setTipoServicio($tipoServicio)
    {
        $this->tipoServicio = $tipoServicio;

        return $this;
    }

    /**
     * Get tipoServicio
     *
     * @return string
     */
    public function getTipoServicio()
    {
        return $this->tipoServicio;
    }
    
    
    
    /**
     * @var string
     *
     * @ORM\Column(name="formaPago", type="string", length=255, nullable=true)
     */
    private $formaPago;
    
    /**
     * Set formaPago
     *
     * @param string $formaPago
     *
     * @return EnviosBundle
     */
    public function setFormaPago($formaPago)
    {
        $this->formaPago = $formaPago;

        return $this;
    }

    /**
     * Get formaPago
     *
     * @return string
     */
    public function getFormaPago()
    {
        return $this->formaPago;
    }
    
    /**
     * Set fecha
     *
     * @param string $fecha
     *
     * @return EnviosBundle
     */
    public function setFecha($fecha)
    {
        $this->fecha = $fecha;

        return $this;
    }

    /**
     * Get fecha
     *
     * @return string
     */
    public function getFecha()
    {
        return $this->fecha;
    }
    
    /**
     * @var string
     *
     * @ORM\Column(name="tipoOperacion", type="string", length=255, nullable=true)
     */
    private $tipoOperacion;
    
    /**
     * Set tipoOperacion
     *
     * @param string $tipoOperacion
     *
     * @return EnviosBundle
     */
    public function setTipoOperacion($tipoOperacion)
    {
        $this->tipoOperacion = $tipoOperacion;

        return $this;
    }

    /**
     * Get formaPago
     *
     * @return string
     */
    public function getTipoOperacion()
    {
        return $this->tipoOperacion;
    }
    
    //==========================================================================
    /**
     * @var string
     *
     * @ORM\Column(name="guiaAgente", type="string", length=45, nullable=true)
     * @Expose
     */
    private $guiaAgente;
    
    /**
     * Set guiaAgente
     *
     * @param string $guiaAgente
     *
     * @return EnviosBundle
     */
    public function setGuiaAgente($guiaAgente)
    {
        $this->guiaAgente = $guiaAgente;

        return $this;
    }

    /**
     * Get guiaAgente
     *
     * @return string
     */
    public function getGuiaAgente()
    {
        return $this->guiaAgente."";
    }
    //==========================================================================
    /**
     * @var string
     *
     * @ORM\Column(name="sucursalCabecera", type="string", length=45, nullable=true)
     */
    private $sucursalCabecera;
    
    /**
     * Set sucursalCabecera
     *
     * @param string $sucursalCabecera
     *
     * @return EnviosBundle
     */
    public function setSucursalCabecera($sucursalCabecera)
    {
        $this->sucursalCabecera = $sucursalCabecera;

        return $this;
    }

    /**
     * Get sucursalCabecera
     *
     * @return string
     */
    public function getSucursalCabecera()
    {
        return $this->sucursalCabecera;
    }
    
    /**
     * @var float
     *
     * @ORM\Column(name="monto_guia_web", type="float", nullable=true)
     * @Expose
     */
    private $montoGuiaWeb;
    
    /**
     * @var nroCta
     *
     * @ORM\Column(name="nroCta", type="integer", nullable=true)
     */
    private $nroCta;
    
    /**
     * Set nroCta
     *
     * @param integer $nroCta
     *
     * @return EnviosBundle
     */
    public function setNroCta($nroCta)
    {
        $this->nroCta = $nroCta;

        return $this;
    }

    /**
     * Get nroCta
     *
     * @return integer
     */
    public function getNroCta()
    {
        return $this->nroCta;
    }
    //==========================================================================
    /**
     * @var totalFlete
     *
     * @ORM\Column(name="totalFlete", type="float", nullable=true)
     * @Expose
     */
    private $totalFlete;
    
    /**
     * Set totalFlete
     *
     * @param float $totalFlete
     *
     * @return EnviosBundle
     */
    public function setTotalFlete($totalFlete)
    {
        $this->totalFlete = $totalFlete;

        return $this;
    }

    /**
     * Get totalFlete
     *
     * @return float
     */
    public function getTotalFlete()
    {
        return $this->totalFlete;
    }
    //==========================================================================
    /**
     * @var custodia
     *
     * @ORM\Column(name="custodia", type="float", nullable=true)
     * @Expose
     */
    private $custodia;

    /**
     * Set custodia
     *
     * @param float $custodia
     *
     * @return EnviosBundle
     */
    public function setCustodia($custodia)
    {
        $this->custodia = $custodia;

        return $this;
    }

    /**
     * Get custodia
     *
     * @return float
     */
    public function getCustodia()
    {
        return $this->custodia;
    }
    
    /**
     * @ORM\ManyToOne(targetEntity="Presis\GeneralBundle\Entity\Cliente")
     * @ORM\JoinColumn(name="cliente_id", referencedColumnName="id",onDelete="cascade")
     **/
    private $cliente;
    
    /**
     * @return mixed
     */
    public function getCliente()
    {
        return $this->cliente;
    }

    /**
     * @param mixed $cliente
     */
    public function setCliente($cliente)
    {
        $this->cliente = $cliente;
    }
    //==========================================================================
    
    /**
     * Set servicio
     *
     * @param \Presis\ServicioBundle\Entity\Servicio $servicio
     *
     * @return DatosEnvios
     */
    public function setServicio(\Presis\ServicioBundle\Entity\Servicio $servicio = null)
    {
        $this->servicio = $servicio;

        return $this;
    }

    /**
     * Get servicio
     *
     * @return \Presis\ServicioBundle\Entity\Servicio
     */
    public function getServicio()
    {
        return $this->servicio;
    }

    /**
     * Set costoPorRemitoConforme
     *
     * @param string $costoPorRemitoConforme
     *
     * @return DatosEnvios
     */
    public function setCostoPorRemitoConforme($costoPorRemitoConforme)
    {
        $this->costoPorRemitoConforme = $costoPorRemitoConforme;

        return $this;
    }

    /**
     * Get costoPorRemitoConforme
     *
     * @return float
     */
    public function getCostoPorRemitoConforme()
    {
        return $this->costoPorRemitoConforme;
    }

    /**
     * Set costoDespachoAExpreso
     *
     * @param string $costoDespachoAExpreso
     *
     * @return DatosEnvios
     */
    public function setCostoDespachoAExpreso($costoDespachoAExpreso)
    {
        $this->costoDespachoAExpreso = $costoDespachoAExpreso;

        return $this;
    }

    /**
     * Get costoDespachoAExpreso
     *
     * @return float
     */
    public function getCostoDespachoAExpreso()
    {
        return $this->costoDespachoAExpreso;
    }

    /**
     * Set costoAdicional1
     *
     * @param string $costoAdicional1
     *
     * @return DatosEnvios
     */
    public function setCostoAdicional1($costoAdicional1)
    {
        $this->costoAdicional1 = $costoAdicional1;

        return $this;
    }

    /**
     * Get costoAdicional1
     *
     * @return float
     */
    public function getCostoAdicional1()
    {
        return $this->costoAdicional1;
    }

    /**
     * Set detalleCostoAdicional1
     *
     * @param string $detalleCostoAdicional1
     *
     * @return DatosEnvios
     */
    public function setDetalleCostoAdicional1($detalleCostoAdicional1)
    {
        $this->detalleCostoAdicional1 = $detalleCostoAdicional1;

        return $this;
    }

    /**
     * Get detalleCostoAdicional1
     *
     * @return string
     */
    public function getDetalleCostoAdicional1()
    {
        return $this->detalleCostoAdicional1;
    }

    /**
     * Set costoAdicional2
     *
     * @param string $costoAdicional2
     *
     * @return DatosEnvios
     */
    public function setCostoAdicional2($costoAdicional2)
    {
        $this->costoAdicional2 = $costoAdicional2;

        return $this;
    }

    /**
     * Get costoAdicional2
     *
     * @return float
     */
    public function getCostoAdicional2()
    {
        return $this->costoAdicional2;
    }

    /**
     * Set detalleCostoAdicional2
     *
     * @param string $detalleCostoAdicional2
     *
     * @return DatosEnvios
     */
    public function setDetalleCostoAdicional2($detalleCostoAdicional2)
    {
        $this->detalleCostoAdicional2 = $detalleCostoAdicional2;

        return $this;
    }

    /**
     * Get detalleCostoAdicional2
     *
     * @return string
     */
    public function getDetalleCostoAdicional2()
    {
        return $this->detalleCostoAdicional2;
    }

    /**
     * Set costoAdicional3
     *
     * @param string $costoAdicional3
     *
     * @return DatosEnvios
     */
    public function setCostoAdicional3($costoAdicional3)
    {
        $this->costoAdicional3 = $costoAdicional3;

        return $this;
    }

    /**
     * Get costoAdicional3
     *
     * @return float
     */
    public function getCostoAdicional3()
    {
        return $this->costoAdicional3;
    }

    /**
     * Set detalleCostoAdicional3
     *
     * @param string $detalleCostoAdicional3
     *
     * @return DatosEnvios
     */
    public function setDetalleCostoAdicional3($detalleCostoAdicional3)
    {
        $this->detalleCostoAdicional3 = $detalleCostoAdicional3;

        return $this;
    }

    /**
     * Get detalleCostoAdicional3
     *
     * @return string
     */
    public function getDetalleCostoAdicional3()
    {
        return $this->detalleCostoAdicional3;
    }

    /**
     * Set seguroManual
     *
     * @param boolean $seguroManual
     *
     * @return DatosEnvios
     */
    public function setSeguroManual($seguroManual)
    {
        $this->seguroManual = $seguroManual;

        return $this;
    }

    /**
     * Get seguroManual
     *
     * @return boolean
     */
    public function getSeguroManual()
    {
        return $this->seguroManual;
    }

    /**
     * Set debeRetirarse
     *
     * @param boolean $debeRetirarse
     *
     * @return DatosEnvios
     */
    public function setDebeRetirarse($debeRetirarse)
    {
        $this->debeRetirarse = $debeRetirarse;

        return $this;
    }

    /**
     * Get debeRetirarse
     *
     * @return boolean
     */
    public function getDebeRetirarse()
    {
        return $this->debeRetirarse;
    }

    /**
     * Set retiro
     *
     * @param \Presis\RetiroBundle\Entity\Retiro $retiro
     *
     * @return DatosEnvios
     */
    public function setRetiro(\Presis\RetiroBundle\Entity\Retiro $retiro = null)
    {
        $this->retiro = $retiro;

        return $this;
    }

    /**
     * Get retiro
     *
     * @return \Presis\RetiroBundle\Entity\Retiro
     */
    public function getRetiro()
    {
        return $this->retiro;
    }

    /**
     * Set montoGuiaWeb
     *
     * @param float $montoGuiaWeb
     *
     * @return DatosEnvios
     */
    public function setMontoGuiaWeb($montoGuiaWeb)
    {
        $this->montoGuiaWeb = $montoGuiaWeb;

        return $this;
    }

    /**
     * Get montoGuiaWeb
     *
     * @return float
     */
    public function getMontoGuiaWeb()
    {
        return $this->montoGuiaWeb;
    }

    /**
     * Set costoPorMonitoreoActivo
     *
     * @param string $costoPorMonitoreoActivo
     *
     * @return DatosEnvios
     */
    public function setCostoPorMonitoreoActivo($costoPorMonitoreoActivo)
    {
        $this->costoPorMonitoreoActivo = $costoPorMonitoreoActivo;

        return $this;
    }

    /**
     * Get costoPorMonitoreoActivo
     *
     * @return float
     */
    public function getCostoPorMonitoreoActivo()
    {
        return $this->costoPorMonitoreoActivo;
    }

    /*==================================AGREGADO PARA MARCAR SI EL REMITO FUE FACTURADO========================*/

    /**
     * @var booleam
     *
     * @ORM\Column(name="facturado", type="boolean", nullable=true)
     */
    private $facturado=0;

    /**
     * @return booleam
     */
    public function getFacturado()
    {
        return $this->facturado;
    }

    /**
     * @param booleam $facturado
     */
    public function setFacturado($facturado)
    {
        $this->facturado = $facturado;
    }

    /**
     * @var booleam
     *
     * @ORM\Column(name="cobrado", type="boolean", nullable=true)
     * @Expose
     */
    private $cobrado=false;

    /**
     * @var string
     *
     * @ORM\Column(name="cordonOrigen", type="string", length=45, nullable=true)
     */
    protected $cordonOrigen;

    /**
     * @var string
     *
     * @ORM\Column(name="cordonDestino", type="string", length=45, nullable=true)
     */
    protected $cordonDestino;

    /**
     * @return string
     */
    public function getCordonOrigen()
    {
        return $this->cordonOrigen."";
    }

    /**
     * @param string $cordonOrigen
     */
    public function setCordonOrigen($cordonOrigen)
    {
        $this->cordonOrigen = $cordonOrigen;
    }

    /**
     * @return string
     */
    public function getCordonDestino()
    {
        return $this->cordonDestino."";
    }

    /**
     * @param string $cordonDestino
     */
    public function setCordonDestino($cordonDestino)
    {
        $this->cordonDestino = $cordonDestino;
    }

    /**
     * @VirtualProperty
     * @SerializedName("tf")
     *
     * @return string
     */
    public function getTf()
    {
        return $this->getTotalFlete();
    }


    /**
     * @VirtualProperty
     * @SerializedName("co")
     *
     * @return string
     */
    public function getCo()
    {
        return $this->getCordonOrigen();
    }

    /**
     * @VirtualProperty
     * @SerializedName("cd")
     *
     * @return string
     */
    public function getCd()
    {
        return $this->getCordonDestino();
    }

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fecha_pactada", type="date", nullable=true)
     * @Expose
     */
    private $fechaPactada;

    /**
     * @return \DateTime
     */
    public function getFechaPactada()
    {
        return $this->fechaPactada;
    }

    /**
     * @param \DateTime $fechaPactada
     */
    public function setFechaPactada($fechaPactada)
    {
        $this->fechaPactada = $fechaPactada;
    }

    /**
     * @var string
     *
     * @ORM\Column(name="nro_factura", type="string", length=255, nullable=true)
     * @Expose
     */
    private $nroFactura;

    /**
     * @return string
     */
    public function getNroFactura()
    {
        return $this->nroFactura;
    }

    /**
     * @param string $nroFactura
     */
    public function setNroFactura($nroFactura)
    {
        $this->nroFactura = $nroFactura;
    }

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fecha_factura", type="date", nullable=true)
     * @Expose
     */
    private $fechaFactura;

    /**
     * @return \DateTime
     */
    public function getFechaFactura()
    {
        return $this->fechaFactura;
    }

    /**
     * @param \DateTime $fechaFactura
     */
    public function setFechaFactura($fechaFactura)
    {
        $this->fechaFactura = $fechaFactura;
    }

    /**
     * @return mixed
     */
    public function getConfirmada()
    {
        return $this->confirmada;

    }

    /**
     * @param mixed $confirmada
     */
    public function setConfirmada($confirmada)
    {
        $this->confirmada = $confirmada;
    }

    /**
     * @return mixed
     */
    public function getCeco()
    {
        return $this->ceco;
    }

    /**
     * @param mixed $ceco
     */
    public function setCeco($ceco)
    {
        $this->ceco = $ceco;
    }

    /*==================================*/

    /**
     * @var \String
     *
     * @ORM\Column(name="cecoDesc", type="string", length=255, nullable=true)
     * @Expose
     */
    private $cecoDesc;

    /**
     * @return \DateTime
     */
    public function getCecoDesc()
    {
        return $this->cecoDesc;
    }

    /**
     * @param \DateTime $cecoDesc
     */
    public function setCecoDesc($cecoDesc)
    {
        $this->cecoDesc = $cecoDesc;
    }

    /**
     * @return booleam
     */
    public function getCobrado()
    {
        return $this->cobrado;
    }

    /**
     * @param booleam $cobrado
     */
    public function setCobrado($cobrado)
    {
        $this->cobrado = $cobrado;
    }


    /**
     * @var \String
     *
     * @ORM\Column(name="pago_en", type="string", length=20, nullable=true)
     * @Expose
     */
    private $pagoEn;

    /**
     * @return String
     */
    public function getPagoEn()
    {
        return $this->pagoEn;
    }

    /**
     * @param String $pagoEn
     */
    public function setPagoEn($pagoEn)
    {
        $this->pagoEn = $pagoEn;
    }

    /**
     * @return float
     */
    public function getPesoVolumetrico()
    {
        return $this->pesoVolumetrico;
    }

    /**
     * @param float $pesoVolumetrico
     */
    public function setPesoVolumetrico($pesoVolumetrico)
    {
        $this->pesoVolumetrico = $pesoVolumetrico;
    }




}
