<?php

namespace Presis\DistribuidorBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Presis\GeneralBundle\Entity\Sucursal;
use Presis\TipoDNIBundle\Entity\TipoDni;
use JMS\Serializer\Annotation\Exclude;
use JMS\Serializer\Annotation\VirtualProperty;
use JMS\Serializer\Annotation\SerializedName;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * Distribuidor
 *
 * @ORM\Table(name="distribuidor")
 * @ORM\Entity
 * @UniqueEntity("codigo")
 */
class Distribuidor
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
     * @ORM\OneToMany(targetEntity="Presis\TrackerBundle\Entity\Tracker", mappedBy="distribuidor")
     */
    private $trackers;

    /**
     * @return mixed
     */
    public function getTrackers()
    {
        return $this->trackers;
    }

    /**
     * @param mixed $trackers
     */
    public function setTrackers($trackers)
    {
        $this->trackers = $trackers;
    }


    /**
     * @var string
     *
     * @ORM\Column(name="nombre", type="string", length=255)
     *
     * @Exclude
     */
    private $nombre;

    /**
     * @VirtualProperty
     * @SerializedName("nombre")
     */
    public function getNombreCompleto()
    {
        $s = ($this->apellido != "" && $this->nombre != "")?", ":" ";
        return $this->apellido . $s . $this->nombre;
    }

    /**
     * @var string
     *
     * @ORM\Column(name="apellido", type="string", length=255, nullable=true)
     *
     * @Exclude
     */
    private $apellido;

    /**
     * @VirtualProperty
     * @SerializedName("apellido")
     */
    public function getApellidoNombre()
    {
        return ($this->apellido)?$this->apellido:" ";
    }

    /**
     * @var string
     *
     * @ORM\Column(name="direccion", type="string", length=255, nullable=true)
     */
    private $direccion;

    /**
     * @var integer
     *
     * @ORM\Column(name="cp", type="integer", nullable=true)
     */
    private $cp;


    /**
     * @VirtualProperty
     * @SerializedName("sucursal_nombre")
     */
    public function getNombreSucursal()
    {
        $sucursal = $this->getSucursal();
        $name = ($sucursal)?$sucursal->getDescripcion():"";

        return $name;
    }

    /**
     * @var string
     *
     * @ORM\Column(name="localidad", type="string", length=255, nullable=true)
     *
     * @Exclude
     */
    private $localidad;

    /**
     * @VirtualProperty
     * @SerializedName("localidad")
     */
    public function getLocalidadNombre()
    {
        return ($this->localidad)?$this->localidad:" ";
    }

    /**
     * @var string
     *
     * @ORM\Column(name="provincia", type="string", length=255, nullable=true)
     */
    private $provincia;

    /**
     * @var string
     *
     * @ORM\Column(name="zona", type="string", length=255, nullable=true)
     */
    private $zona;

    /**
     * @var string
     *
     * @ORM\Column(name="telefono", type="string", length=255, nullable=true)
     */
    private $telefono;

    /**
     * @var integer
     *
     * @ORM\Column(name="dni", type="integer", nullable=true)
     */
    private $dni;

    /**
     * @ORM\ManyToOne(targetEntity="Presis\TipoDNIBundle\Entity\TipoDni", inversedBy="distribuidores")
     * @ORM\JoinColumn(name="tipodni_id", referencedColumnName="id")
     *
     */
    private $tipodni;

    /**
     * @var string
     *
     * @ORM\Column(name="imei", type="string", length=30, nullable=true)
     *
     * @Exclude
     */
    private $imei;

    /**
     * @VirtualProperty
     * @SerializedName("imei")
     */
    public function getImeiNombre()
    {
        return ($this->imei)?$this->imei:" ";
    }

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=80, nullable=true)
     *
     */
    private $email;

    /**
     * @VirtualProperty
     * @SerializedName("email")
     */
    public function getEmailNombre()
    {
        return ($this->email)?$this->email:" ";
    }

    /**
     * @var string
     *
     * @ORM\Column(name="web", type="string", length=80, nullable=true)
     */
    private $web;

    /**
     * @var string
     *
     * @ORM\Column(name="obs", type="string", nullable=true)
     */
    private $observaciones;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fecha_alta", type="date", nullable=true)
     */
    private $fechaAlta;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fecha_baja", type="date", nullable=true)
     */
    private $fechaBaja;

    /**
     * @var string
     *
     * @ORM\Column(name="veh_nombre", type="string", length=80, nullable=true)
     */
    private $vehNombre;

    /**
     * @var string
     *
     * @ORM\Column(name="veh_tipo", type="string", length=80, nullable=true)
     */
    private $vehTipo;

    /**
     * @var string
     *
     * @ORM\Column(name="veh_marca", type="string", length=80, nullable=true)
     */
    private $vehMarca;

    /**
     * @var string
     *
     * @ORM\Column(name="veh_patente", type="string", length=80, nullable=true)
     */
    private $vehPatente;

    /**
     * @var string
     *
     * @ORM\Column(name="prop_nombre", type="string", length=80, nullable=true)
     */
    private $propNombre;

    /**
     * @var string
     *
     * @ORM\Column(name="prop_direccion", type="string", length=80, nullable=true)
     */
    private $propDireccion;

    /**
     * @var string
     *
     * @ORM\Column(name="prop_telefono", type="string", length=80, nullable=true)
     */
    private $propTelefono;

    /**
     * @var string
     *
     * @ORM\Column(name="prop_equipo", type="string", length=80, nullable=true)
     */
    private $propEquipo;

    /**
     * @var string
     *
     * @ORM\Column(name="codigo", type="string", length=30,unique=true, nullable=true)
     */
    private $codigo;

    /**
     * @ORM\OneToMany(targetEntity="Presis\RecorridoBundle\Entity\Recorrido", mappedBy="distribuidor")
     */
    private $recorridos;

    /**
     * @ORM\OneToMany(targetEntity="Presis\RecorridoBundle\Entity\RecorridoRetiro", mappedBy="distribuidor")
     */
    private $retiros_planillados;

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
     * Set nombre
     *
     * @param string $nombre
     *
     * @return Distribuidor
     */
    public function setNombre($nombre)
    {
        $this->nombre = $nombre;

        return $this;
    }

    /**
     * Get nombre
     *
     * @return string
     */
    public function getNombre()
    {
        return $this->nombre;
    }

    /**
     * Set apellido
     *
     * @param string $apellido
     *
     * @return Distribuidor
     */
    public function setApellido($apellido)
    {
        $this->apellido = $apellido;

        return $this;
    }

    /**
     * Get apellido
     *
     * @return string
     */
    public function getApellido()
    {
        return $this->apellido;
    }

    /**
     * Set direccion
     *
     * @param string $direccion
     *
     * @return Distribuidor
     */
    public function setDireccion($direccion)
    {
        $this->direccion = $direccion;

        return $this;
    }

    /**
     * Get direccion
     *
     * @return string
     */
    public function getDireccion()
    {
        return $this->direccion;
    }

    /**
     * Set cp
     *
     * @param integer $cp
     *
     * @return Distribuidor
     */
    public function setCp($cp)
    {
        $this->cp = $cp;

        return $this;
    }

    /**
     * Get cp
     *
     * @return integer
     */
    public function getCp()
    {
        return $this->cp;
    }

    /**
     * Set localidad
     *
     * @param string $localidad
     *
     * @return Distribuidor
     */
    public function setLocalidad($localidad)
    {
        $this->localidad = $localidad;

        return $this;
    }

    /**
     * Get localidad
     *
     * @return string
     */
    public function getLocalidad()
    {
        return $this->localidad;
    }

    /**
     * Set provincia
     *
     * @param string $provincia
     *
     * @return Distribuidor
     */
    public function setProvincia($provincia)
    {
        $this->provincia = $provincia;

        return $this;
    }

    /**
     * Get provincia
     *
     * @return string
     */
    public function getProvincia()
    {
        return $this->provincia;
    }

    /**
     * Set zona
     *
     * @param string $zona
     *
     * @return Distribuidor
     */
    public function setZona($zona)
    {
        $this->zona = $zona;

        return $this;
    }

    /**
     * Get zona
     *
     * @return string
     */
    public function getZona()
    {
        return $this->zona;
    }

    /**
     * Set telefono
     *
     * @param string $telefono
     *
     * @return Distribuidor
     */
    public function setTelefono($telefono)
    {
        $this->telefono = $telefono;

        return $this;
    }

    /**
     * Get telefono
     *
     * @return string
     */
    public function getTelefono()
    {
        return $this->telefono;
    }

    /**
     * Set dni
     *
     * @param integer $dni
     *
     * @return Distribuidor
     */
    public function setDni($dni)
    {
        $this->dni = $dni;

        return $this;
    }

    /**
     * Get dni
     *
     * @return integer
     */
    public function getDni()
    {
        return $this->dni;
    }

    /**
     * Set imei
     *
     * @param string $imei
     *
     * @return Distribuidor
     */
    public function setImei($imei)
    {
        $this->imei = $imei;

        return $this;
    }

    /**
     * Get imei
     *
     * @return string
     */
    public function getImei()
    {
        return $this->imei;
    }

    /**
     * Set email
     *
     * @param string $email
     *
     * @return Distribuidor
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set web
     *
     * @param string $web
     *
     * @return Distribuidor
     */
    public function setWeb($web)
    {
        $this->web = $web;

        return $this;
    }

    /**
     * Get web
     *
     * @return string
     */
    public function getWeb()
    {
        return $this->web;
    }

    /**
     * Set observaciones
     *
     * @param string $observaciones
     *
     * @return Distribuidor
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
        return $this->observaciones;
    }

    /**
     * Set fechaAlta
     *
     * @param \DateTime $fechaAlta
     *
     * @return Distribuidor
     */
    public function setFechaAlta($fechaAlta)
    {
        $this->fechaAlta = $fechaAlta;

        return $this;
    }

    /**
     * Get fechaAlta
     *
     * @return \DateTime
     */
    public function getFechaAlta()
    {
        return $this->fechaAlta;
    }

    /**
     * Set fechaBaja
     *
     * @param \DateTime $fechaBaja
     *
     * @return Distribuidor
     */
    public function setFechaBaja($fechaBaja)
    {
        $this->fechaBaja = $fechaBaja;

        return $this;
    }

    /**
     * Get fechaBaja
     *
     * @return \DateTime
     */
    public function getFechaBaja()
    {
        return $this->fechaBaja;
    }

    /**
     * Set vehNombre
     *
     * @param string $vehNombre
     *
     * @return Distribuidor
     */
    public function setVehNombre($vehNombre)
    {
        $this->vehNombre = $vehNombre;

        return $this;
    }

    /**
     * Get vehNombre
     *
     * @return string
     */
    public function getVehNombre()
    {
        return $this->vehNombre;
    }

    /**
     * Set vehTipo
     *
     * @param string $vehTipo
     *
     * @return Distribuidor
     */
    public function setVehTipo($vehTipo)
    {
        $this->vehTipo = $vehTipo;

        return $this;
    }

    /**
     * Get vehTipo
     *
     * @return string
     */
    public function getVehTipo()
    {
        return $this->vehTipo;
    }

    /**
     * Set vehMarca
     *
     * @param string $vehMarca
     *
     * @return Distribuidor
     */
    public function setVehMarca($vehMarca)
    {
        $this->vehMarca = $vehMarca;

        return $this;
    }

    /**
     * Get vehMarca
     *
     * @return string
     */
    public function getVehMarca()
    {
        return $this->vehMarca;
    }

    /**
     * Set vehPatente
     *
     * @param string $vehPatente
     *
     * @return Distribuidor
     */
    public function setVehPatente($vehPatente)
    {
        $this->vehPatente = $vehPatente;

        return $this;
    }

    /**
     * Get vehPatente
     *
     * @return string
     */
    public function getVehPatente()
    {
        return $this->vehPatente;
    }

    /**
     * Set propNombre
     *
     * @param string $propNombre
     *
     * @return Distribuidor
     */
    public function setPropNombre($propNombre)
    {
        $this->propNombre = $propNombre;

        return $this;
    }

    /**
     * Get propNombre
     *
     * @return string
     */
    public function getPropNombre()
    {
        return $this->propNombre;
    }

    /**
     * Set propDireccion
     *
     * @param string $propDireccion
     *
     * @return Distribuidor
     */
    public function setPropDireccion($propDireccion)
    {
        $this->propDireccion = $propDireccion;

        return $this;
    }

    /**
     * Get propDireccion
     *
     * @return string
     */
    public function getPropDireccion()
    {
        return $this->propDireccion;
    }

    /**
     * Set propTelefono
     *
     * @param string $propTelefono
     *
     * @return Distribuidor
     */
    public function setPropTelefono($propTelefono)
    {
        $this->propTelefono = $propTelefono;

        return $this;
    }

    /**
     * Get propTelefono
     *
     * @return string
     */
    public function getPropTelefono()
    {
        return $this->propTelefono;
    }

    /**
     * Set propEquipo
     *
     * @param string $propEquipo
     *
     * @return Distribuidor
     */
    public function setPropEquipo($propEquipo)
    {
        $this->propEquipo = $propEquipo;

        return $this;
    }

    /**
     * Get propEquipo
     *
     * @return string
     */
    public function getPropEquipo()
    {
        return $this->propEquipo;
    }


    /**
     * Set tipodni
     *
     * @param \Presis\TipoDNIBundle\Entity\TipoDni $tipodni
     *
     * @return Distribuidor
     */
    public function setTipodni(\Presis\TipoDNIBundle\Entity\TipoDni $tipodni = null)
    {
        $this->tipodni = $tipodni;

        return $this;
    }

    /**
     * Get tipodni
     *
     * @return \Presis\TipoDNIBundle\Entity\TipoDni
     */
    public function getTipodni()
    {
        return $this->tipodni;
    }

    /**
     *
     *
     * @return string
     */
    function __toString()
    {
        return $this->getCodigo() . " - " . $this->getNombreCompleto();
    }
    /**
     * Constructor. Por defecto copia el id en el código, aunque puede ser editado luego.
     */
    public function __construct()
    {
        $this->recorridos = new \Doctrine\Common\Collections\ArrayCollection();
        $this->retiros_planillados = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add recorrido
     *
     * @param \Presis\RecorridoBundle\Entity\Recorrido $recorrido
     *
     * @return Distribuidor
     */
    public function addRecorrido(\Presis\RecorridoBundle\Entity\Recorrido $recorrido)
    {
        $this->recorridos[] = $recorrido;

        return $this;
    }

    /**
     * Remove recorrido
     *
     * @param \Presis\RecorridoBundle\Entity\Recorrido $recorrido
     */
    public function removeRecorrido(\Presis\RecorridoBundle\Entity\Recorrido $recorrido)
    {
        $this->recorridos->removeElement($recorrido);
    }

    /**
     * Get recorridos
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getRecorridos()
    {
        return $this->recorridos;
    }

    /**
     * Add retirosPlanillado
     *
     * @param \Presis\RecorridoBundle\Entity\RecorridoRetiro $retirosPlanillado
     *
     * @return Distribuidor
     */
    public function addRetirosPlanillado(\Presis\RecorridoBundle\Entity\RecorridoRetiro $retirosPlanillado)
    {
        $this->retiros_planillados[] = $retirosPlanillado;

        return $this;
    }

    /**
     * Remove retirosPlanillado
     *
     * @param \Presis\RecorridoBundle\Entity\RecorridoRetiro $retirosPlanillado
     */
    public function removeRetirosPlanillado(\Presis\RecorridoBundle\Entity\RecorridoRetiro $retirosPlanillado)
    {
        $this->retiros_planillados->removeElement($retirosPlanillado);
    }

    /**
     * Get retirosPlanillados
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getRetirosPlanillados()
    {
        return $this->retiros_planillados;
    }

    /**
     * Set codigo
     *
     * @param string $codigo
     *
     * @return Distribuidor
     */
    public function setCodigo($codigo)
    {
        $this->codigo = $codigo;

        return $this;
    }

    /**
     * Get codigo
     *
     * @return string
     */
    public function getCodigo()
    {
        return $this->codigo;
    }


    /**
     * @ORM\ManyToOne(targetEntity="Presis\GeneralBundle\Entity\Sucursal", inversedBy="distribuidor")
     * @ORM\JoinColumn(name="sucursal_id", referencedColumnName="id")
     *
     */
    private $sucursal;

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
     * @var string
     *
     * @ORM\Column(name="token", type="string", length=250, nullable=true)
     */
    private $token;

    /**
     * @var string
     *
     * @ORM\Column(name="password", type="string", length=255, nullable=true)
     *
     * @Exclude
     */
    private $password;

    /**
     * @var booleam
     *
     * @ORM\Column(name="estado", type="boolean", nullable=true)
     */
    private $estado=true;

}
