<?php

namespace Presis\GestionCelBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Presis\RetiroBundle\Entity\Retiro;
use Doctrine\Common\Collections\ArrayCollection;
use Presis\EstadoBundle\Entity\Estado;
use Symfony\Component\Serializer\Serializer;
use JMS\Serializer\Annotation\VirtualProperty;

use JMS\Serializer\Annotation\Type;
use Presis\RetiroBundle\PresisRetiroBundle;
use Symfony\Component\Validator\Constraints as Assert;
use Presis\RetiroBundle\Validator\Constraint\CheckGeneralList as CheckRetiroAssert;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;
use JMS\Serializer\Annotation\SerializedName;
use Presis\MovistarBundle\Entity\Fabricante;
use Presis\MovistarBundle\Entity\Modelo;
/**
 * GestionCel
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class GestionCel
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="usuario", type="string", length=255)
     */
    private $usuario;

    /**
     * @var string
     *
     * @ORM\Column(name="ani", type="string", length=10)
     */
    private $ani;

    /**
     * @var string
     *
     * @ORM\Column(name="valordeclaradocel", type="string", length=10)
     */
    private $valordeclaradocel;

    /**
     * @var integer
     *
     * @ORM\Column(name="nroserie", type="string", length=15)
     */
    private $nroserie;

    /**
     * @var string
     *
     * @ORM\Column(name="nomyape", type="string", length=30)
     */
    private $nomyape;

    /**
     * @var string
     *
     * @ORM\Column(name="nrosst",  type="string", length=16)
     */
    private $nrosst;

    /**
     * @var string
     *
     * @ORM\Column(name="aceptacargos", type="string", length=255)
     */
    private $aceptacargos;

    /**
     * @var string
     *
     * @ORM\Column(name="nivelderep", type="string", length=255,nullable=true)
     */
    private $nivelderep;

    /**
     * @var string
     *
     * @ORM\Column(name="muleto", type="string", length=255)
     */
    private $muleto;

    /**
     * @var string
     *
     * @ORM\Column(name="imeimuleto", type="string", length=15,nullable=true)
     */
    private $imeimuleto;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fechaactivacion", type="datetime")
     */
    private $fechaactivacion;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fechafabricacion", type="datetime", nullable=true)
     */
    private $fechafabricacion;

    /**
     * @var string
     *
     * @ORM\Column(name="origendelequipo", type="string", length=255)
     */
    private $origendelequipo;

    /**
     * @var string
     *
     * @ORM\Column(name="sva", type="string", length=255)
     */
    private $sva;

    /**
     * @var string
     *
     * @ORM\Column(name="falla", type="array", length=255)
     */
    private $falla;

    /**
     * @var string
     *
     * @ORM\Column(name="rotura", type="string", length=255)
     */
    private $rotura;

    /**
     * @var string
     *
     * @ORM\Column(name="completitud", type="array", length=255)
     */
    private $completitud;

    /**
     * @var string
     *
     * @ORM\Column(name="estadointervencion", type="string", length=255, nullable=true)
     */
    private $estadointervencion;

    /**
     * @var string
     *
     * @ORM\Column(name="certificadoreparador", type="string", length=10, nullable=true)
     */
    private $certificadoreparador;

    /**
     * @var string
     *
     * @ORM\Column(name="placaswap", type="string", length=255, nullable=true)
     */
    private $placaswap;

    /**
     * @var string
     *
     * @ORM\Column(name="nroimei", type="string", length=15, nullable=true)
     */
    private $nroimei;

    /**
     * @var string
     *
     * @ORM\Column(name="tipocliente", type="string", length=255)
     */
    private $tipocliente;

    /**
     * @var string
     *
     * @ORM\Column(name="tiposervicio", type="string", length=255)
     */
    private $tiposervicio;

    /**
     * @var string
     *
     * @ORM\Column(name="claimassurant", type="string", length=10, nullable=true)
     */
    private $claimassurant;

    /**
     * @var string
     *
     * @ORM\Column(name="precinto", type="string", length=15, nullable=true)
     */
    private $precinto;

    /**
     * @var string
     *
     * @ORM\Column(name="sucursal", type="string", length=45, nullable=true)
     */
    private $sucursal;

    /**
     * @var string
     *
     * @ORM\Column(name="trayecto", type="string", length=15, nullable=true)
     */
    private $trayecto;

    /**
     * @var \Date
     *
     * @ORM\Column(name="fecha_ingreso", type="date", nullable=true)
     *
     */
    private $fecha;

    /**
     * @var string
     *
     * @ORM\Column(name="observaciones", type="string", length=500, nullable=true)
     */
    private $observaciones;


    /**
     * @return string
     */
    public function getClaimassurant()
    {
        return $this->claimassurant;
    }

    /**
     * @param string $claimassurant
     */
    public function setClaimassurant($claimassurant)
    {
        $this->claimassurant = $claimassurant;
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
     * Set usuario
     *
     * @param string $usuario
     *
     * @return GestionCel
     */
    public function setUsuario($usuario)
    {
        $this->usuario = $usuario;

        return $this;
    }

    /**
     * Get usuario
     *
     * @return string
     */
    public function getUsuario()
    {
        return $this->usuario;
    }



    /**
     * Set nroserie
     *
     * @param integer $nroserie
     *
     * @return GestionCel
     */
    public function setNroserie($nroserie)
    {
        $this->nroserie = $nroserie;

        return $this;
    }

    /**
     * Get nroserie
     *
     * @return integer
     */
    public function getNroserie()
    {
        return $this->nroserie;
    }

    /**
     * Set nomyape
     *
     * @param string $nomyape
     *
     * @return GestionCel
     */
    public function setNomyape($nomyape)
    {
        $this->nomyape = $nomyape;

        return $this;
    }

    /**
     * Get nomyape
     *
     * @return string
     */
    public function getNomyape()
    {
        return $this->nomyape;
    }

    /**
     * Set nrosst
     *
     * @param integer $nrosst
     *
     * @return GestionCel
     */
    public function setNrosst($nrosst)
    {
        $this->nrosst = $nrosst;

        return $this;
    }

    /**
     * Get nrosst
     *
     * @return integer
     */
    public function getNrosst()
    {
        return $this->nrosst;
    }

    /**
     * Set aceptacargos
     *
     * @param string $aceptacargos
     *
     * @return GestionCel
     */
    public function setAceptacargos($aceptacargos)
    {
        $this->aceptacargos = $aceptacargos;

        return $this;
    }

    /**
     * Get aceptacargos
     *
     * @return string
     */
    public function getAceptacargos()
    {
        return $this->aceptacargos;
    }

    /**
     * Set nivelderep
     *
     * @param string $nivelderep
     *
     * @return GestionCel
     */
    public function setNivelderep($nivelderep)
    {
        $this->nivelderep = $nivelderep;

        return $this;
    }

    /**
     * Get nivelderep
     *
     * @return string
     */
    public function getNivelderep()
    {
        return $this->nivelderep;
    }

    /**
     * Set muleto
     *
     * @param string $muleto
     *
     * @return GestionCel
     */
    public function setMuleto($muleto)
    {
        $this->muleto = $muleto;

        return $this;
    }

    /**
     * Get muleto
     *
     * @return string
     */
    public function getMuleto()
    {
        return $this->muleto;
    }

    /**
     * Set imeimuleto
     *
     * @param integer $imeimuleto
     *
     * @return GestionCel
     */
    public function setImeimuleto($imeimuleto)
    {
        $this->imeimuleto = $imeimuleto;

        return $this;
    }

    /**
     * Get imeimuleto
     *
     * @return integer
     */
    public function getImeimuleto()
    {
        return $this->imeimuleto;
    }

    /**
     * Set fechaactivacion
     *
     * @param \DateTime $fechaactivacion
     *
     * @return GestionCel
     */
    public function setFechaactivacion($fechaactivacion)
    {
        $this->fechaactivacion = $fechaactivacion;

        return $this;
    }

    /**
     * Get fechaactivacion
     *
     * @return \DateTime
     */
    public function getFechaactivacion()
    {
        return $this->fechaactivacion;
    }

    /**
     * Set fechafabricacion
     *
     * @param \DateTime $fechafabricacion
     *
     * @return GestionCel
     */
    public function setFechafabricacion($fechafabricacion)
    {
        $this->fechafabricacion = $fechafabricacion;

        return $this;
    }

    /**
     * Get fechafabricacion
     *
     * @return \DateTime
     */
    public function getFechafabricacion()
    {
        return $this->fechafabricacion;
    }

    /**
     * Set origendelequipo
     *
     * @param string $origendelequipo
     *
     * @return GestionCel
     */
    public function setOrigendelequipo($origendelequipo)
    {
        $this->origendelequipo = $origendelequipo;

        return $this;
    }

    /**
     * Get origendelequipo
     *
     * @return string
     */
    public function getOrigendelequipo()
    {
        return $this->origendelequipo;
    }

    /**
     * Set sva
     *
     * @param string $sva
     *
     * @return GestionCel
     */
    public function setSva($sva)
    {
        $this->sva = $sva;

        return $this;
    }

    /**
     * Get sva
     *
     * @return string
     */
    public function getSva()
    {
        return $this->sva;
    }

    /**
     * Set falla
     *
     * @param string $falla
     *
     * @return GestionCel
     */
    public function setFalla($falla)
    {

        $this->falla = $falla;

        return $this;
    }

    /**
     * Get falla
     *
     * @return string
     */
    public function getFalla()
    {
        return $this->falla;
    }

    /**
     * Set rotura
     *
     * @param string $rotura
     *
     * @return GestionCel
     */
    public function setRotura($rotura)
    {
        $this->rotura = $rotura;

        return $this;
    }

    /**
     * Get rotura
     *
     * @return string
     */
    public function getRotura()
    {
        return $this->rotura;
    }

    /**
     * Set completitud
     *
     * @param string $completitud
     *
     * @return GestionCel
     */
    public function setCompletitud($completitud)
    {
        $this->completitud = $completitud;

        return $this;
    }

    /**
     * Get completitud
     *
     * @return string
     */
    public function getCompletitud()
    {
        return $this->completitud;
    }

    /**
     * Set estadointervencion
     *
     * @param string $estadointervencion
     *
     * @return GestionCel
     */
    public function setEstadointervencion($estadointervencion)
    {
        $this->estadointervencion = $estadointervencion;

        return $this;
    }

    /**
     * Get estadointervencion
     *
     * @return string
     */
    public function getEstadointervencion()
    {
        return $this->estadointervencion;
    }

    /**
     * Set certificadoreparador
     *
     * @param string $certificadoreparador
     *
     * @return GestionCel
     */
    public function setCertificadoreparador($certificadoreparador)
    {
        $this->certificadoreparador = $certificadoreparador;

        return $this;
    }

    /**
     * Get certificadoreparador
     *
     * @return string
     */
    public function getCertificadoreparador()
    {
        return $this->certificadoreparador;
    }

    /**
     * Set placaswap
     *
     * @param string $placaswap
     *
     * @return GestionCel
     */
    public function setPlacaswap($placaswap)
    {
        $this->placaswap = $placaswap;

        return $this;
    }

    /**
     * Get placaswap
     *
     * @return string
     */
    public function getPlacaswap()
    {
        return $this->placaswap;
    }

    /**
     * Set nroimei
     *
     * @param integer $nroimei
     *
     * @return GestionCel
     */
    public function setNroimei($nroimei)
    {
        $this->nroimei = $nroimei;

        return $this;
    }

    /**
     * Get nroimei
     *
     * @return integer
     */
    public function getNroimei()
    {
        return $this->nroimei;
    }

    /**
     * Set tipocliente
     *
     * @param string $tipocliente
     *
     * @return GestionCel
     */
    public function setTipocliente($tipocliente)
    {
        $this->tipocliente = $tipocliente;

        return $this;
    }

    /**
     * Get tipocliente
     *
     * @return string
     */
    public function getTipocliente()
    {
        return $this->tipocliente;
    }

    /**
     * Set tiposervicio
     *
     * @param string $tiposervicio
     *
     * @return GestionCel
     */
    public function setTiposervicio($tiposervicio)
    {
        $this->tiposervicio = $tiposervicio;

        return $this;
    }

    /**
     * Get tiposervicio
     *
     * @return string
     */
    public function getTiposervicio()
    {
        return $this->tiposervicio;
    }

    /**
     * Set ani
     *
     * @param string $ani
     *
     * @return GestionCel
     */
    public function setAni($ani)
    {
        $this->ani = $ani;

        return $this;
    }

    /**
     * Get ani
     *
     * @return string
     */
    public function getAni()
    {
        return $this->ani;
    }


    /**
     * @ORM\OneToOne(targetEntity="\Presis\RetiroBundle\Entity\Retiro", mappedBy="gestioncel")
     */
    private $retiro;

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
     * @ORM\ManyToOne(targetEntity="Presis\EstadoBundle\Entity\Estado", inversedBy="gestionescel")
     * @ORM\JoinColumn(name="estado_id", referencedColumnName="id")
     *
     */
    private $estado;

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
     * @return string
     */
    public function getPrecinto()
    {
        return $this->precinto;
    }

    /**
     * @param string $precinto
     */
    public function setPrecinto($precinto)
    {
        $this->precinto = $precinto;
    }

    /**
     * @return string
     */
    public function getSucursal()
    {
        return $this->sucursal;
    }

    /**
     * @param string $sucursal
     */
    public function setSucursal($sucursal)
    {
        $this->sucursal = $sucursal;
    }

    /**
     * @return string
     */
    public function getTrayecto()
    {
        return $this->trayecto;
    }

    /**
     * @param string $trayecto
     */
    public function setTrayecto($trayecto)
    {
        $this->trayecto = $trayecto;
    }

    /**
     * @return \DateTime
     */
    public function getFecha()
    {
        return $this->fecha;
    }

    /**
     * @param \DateTime $fecha
     */
    public function setFecha($fecha)
    {
        $this->fecha = $fecha;
    }


    /**
     * @ORM\ManyToOne(targetEntity="Presis\MovistarBundle\Entity\Fabricante")
     * @ORM\JoinColumn(name="fabricante_id", referencedColumnName="id",onDelete="cascade",nullable=true)
     */
    private $fabricante;


    /**
     * @var \Date
     *
     * @ORM\Column(name="fecha_base", type="date", nullable=true)
     * @Expose
     */
    private $fechaBase;

    /**
     * @return mixed
     */
    public function getFabricante()
    {
        return $this->fabricante;
    }

    /**
     * @param mixed $fabricante
     */
    public function setFabricante($fabricante)
    {
        $this->fabricante = $fabricante;
    }

    /**
     * @ORM\ManyToOne(targetEntity="Presis\MovistarBundle\Entity\Modelo")
     * @ORM\JoinColumn(name="modelo_id", referencedColumnName="id",onDelete="cascade",nullable=true)
     */
    private $modelo;

    /**
     * @return mixed
     */
    public function getModelo()
    {
        return $this->modelo;
    }

    /**
     * @param mixed $modelo
     */
    public function setModelo($modelo)
    {
        $this->modelo = $modelo;
    }

    /**
     * @var booleam
     *
     * @ORM\Column(name="sms", type="string", length=200, nullable=true)
     */
    private $sms;

    /**
     * @return booleam
     */
    public function getSms()
    {
        return $this->sms;
    }

    /**
     * @param booleam $sms
     */
    public function setSms($sms)
    {
        $this->sms = $sms;
    }

    /**
     * @return string
     */
    public function getValordeclaradocel()
    {
        return $this->valordeclaradocel;
    }

    /**
     * @param string $valordeclaradocel
     */
    public function setValordeclaradocel($valordeclaradocel)
    {
        $this->valordeclaradocel = $valordeclaradocel;
    }

    /**
     * @return \Date
     */
    public function getFechaBase()
    {
        return $this->fechaBase;
    }

    /**
     * @param \Date $fechaBase
     */
    public function setFechaBase($fechaBase)
    {
        $this->fechaBase = $fechaBase;
    }

    public function __construct()
    {
        $this->fechaBase = new \DateTime();
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
     * Set observaciones
     *
     * @param string $observaciones
     */
    public function setObservaciones($observaciones)
    {
        $this->observaciones = $observaciones;

        return $this;
    }

}
