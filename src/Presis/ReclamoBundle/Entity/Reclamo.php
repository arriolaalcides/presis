<?php

namespace Presis\ReclamoBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;
use JMS\Serializer\Annotation\VirtualProperty;
use JMS\Serializer\Annotation\SerializedName;

/**
 * Reclamo
 *
 * @ORM\Table(name="reclamo")
 * @ORM\Entity(repositoryClass="Presis\ReclamoBundle\Entity\ReclamoRepository")
 *
 * @ExclusionPolicy("all")
 */
class Reclamo
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

    /**
     * @ORM\ManyToOne(targetEntity="Presis\RetiroBundle\Entity\Retiro", inversedBy="reclamos")
     * @ORM\JoinColumn(name="retiro_id", referencedColumnName="id")
     */
    protected $retiro;

    /**
     * @ORM\ManyToOne(targetEntity="Presis\ReclamoBundle\Entity\TipoReclamo", inversedBy="reclamos")
     * @ORM\JoinColumn(name="tipo_reclamo_id", referencedColumnName="id")
     */
    private $tipo;

    /**
     * @VirtualProperty
     * @SerializedName("tipo")
     */
    public function getNombreTipo()
    {
        $tipo = $this->getTipo();
        $name = ($tipo)?$tipo->getNombre():"";

        return $name;
    }

    /**
     * @var array
     *
     * @ORM\Column(name="pendiente", type="string", length=10)
     *
     * @Expose
     */
    private $pendiente;

    /**
     * @var integer
     *
     * @ORM\Column(name="nro", type="string", length=20, nullable=true)
     */
    private $nro = '';

    /**
     * @VirtualProperty
     * @SerializedName("nro")
     */
    public function getNroText()
    {
        return ($this->getNro())?$this->getNro():'';
    }

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fecha", type="date")
     *
     * @Expose
     */
    private $fecha;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fecha_limite", type="date")
     *
     * @Expose
     */
    private $fechaLimite;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fecha_resuelto", type="date", nullable=true)
     */
    private $fechaResuelto;

    /**
     * @VirtualProperty
     * @SerializedName("fecha_resuelto")
     * 
     * @return string
     */
    public function getFechaReclamoResuelto()
    {
        return ($this->getFechaResuelto())?$this->getFechaResuelto():" ";
    }

    /**
     * @var string
     *
     * @ORM\Column(name="resolvio", type="string", length=255, nullable=true)
     */
    private $resolvio = '';

    /**
     * @VirtualProperty
     * @SerializedName("resolvio")
     */
    public function getResolvioText()
    {
        return ($this->getResolvio())?$this->getResolvio():'';
    }

    /**
     * @ORM\ManyToOne(targetEntity="Presis\UserBundle\Entity\User", inversedBy="reclamos")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    protected $user_resolvio;

    /**
     * @var string
     *
     * @ORM\Column(name="direccion", type="string", length=255, nullable=true)
     */
    private $direccion;

    /**
     * @var string
     *
     * @ORM\Column(name="telefono", type="string", length=100, nullable=true)
     */
    private $telefono;

    /**
     * @var string
     *
     * @ORM\Column(name="detalle", type="text", nullable=true)
     */
    private $detalle = '';

    /**
     * @var string
     *
     * @ORM\Column(name="observaciones", type="text", nullable=true)
     */
    private $observaciones;


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
     * Set nro
     *
     * @param integer $nro
     *
     * @return Reclamo
     */
    public function setNro($nro)
    {
        $this->nro = $nro;

        return $this;
    }

    /**
     * Get nro
     *
     * @return integer
     */
    public function getNro()
    {
        return $this->nro;
    }

    /**
     * Set fecha
     *
     * @param \DateTime $fecha
     *
     * @return Reclamo
     */
    public function setFecha($fecha)
    {
        $this->fecha = $fecha;

        return $this;
    }

    /**
     * Get fecha
     *
     * @return \DateTime
     */
    public function getFecha()
    {
        return $this->fecha;
    }

    /**
     * Set fechaLimite
     *
     * @param \DateTime $fechaLimite
     *
     * @return Reclamo
     */
    public function setFechaLimite($fechaLimite)
    {
        $this->fechaLimite = $fechaLimite;

        return $this;
    }

    /**
     * Get fechaLimite
     *
     * @return \DateTime
     */
    public function getFechaLimite()
    {
        return $this->fechaLimite;
    }

    /**
     * Set fechaResuelto
     *
     * @param \DateTime $fechaResuelto
     *
     * @return Reclamo
     */
    public function setFechaResuelto($fechaResuelto)
    {
        $this->fechaResuelto = $fechaResuelto;

        return $this;
    }

    /**
     * Get fechaResuelto
     *
     * @return \DateTime
     */
    public function getFechaResuelto()
    {
        return $this->fechaResuelto;
    }

    /**
     * Set resolvio
     *
     * @param string $resolvio
     *
     * @return Reclamo
     */
    public function setResolvio($resolvio)
    {
        $this->resolvio = $resolvio;

        return $this;
    }

    /**
     * Get resolvio
     *
     * @return string
     */
    public function getResolvio()
    {
        return $this->resolvio;
    }

    /**
     * Set direccion
     *
     * @param string $direccion
     *
     * @return Reclamo
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
     * Set telefono
     *
     * @param string $telefono
     *
     * @return Reclamo
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
     * Set detalle
     *
     * @param string $detalle
     *
     * @return Reclamo
     */
    public function setDetalle($detalle)
    {
        $this->detalle = $detalle;

        return $this;
    }

    /**
     * Get detalle
     *
     * @return string
     */
    public function getDetalle()
    {
        return $this->detalle;
    }

    /**
     * Set observaciones
     *
     * @param string $observaciones
     *
     * @return Reclamo
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
     * Set tipo
     *
     * @param array $tipo
     *
     * @return Reclamo
     */
    public function setTipo($tipo)
    {
        $this->tipo = $tipo;

        return $this;
    }

    /**
     * Get tipo
     *
     * @return array
     */
    public function getTipo()
    {
        return $this->tipo;
    }

    /**
     * Set pendiente
     *
     * @param array $pendiente
     *
     * @return Reclamo
     */
    public function setPendiente($pendiente)
    {
        $this->pendiente = $pendiente;

        return $this;
    }

    /**
     * Get pendiente
     *
     * @return array
     */
    public function getPendiente()
    {
        return $this->pendiente;
    }

    /**
     * Set retiro
     *
     * @param \Presis\RetiroBundle\Entity\Retiro $retiro
     *
     * @return Reclamo
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
     * Set userResolvio
     *
     * @param \Presis\UserBundle\Entity\User $userResolvio
     *
     * @return Reclamo
     */
    public function setUserResolvio(\Presis\UserBundle\Entity\User $userResolvio = null)
    {
        $this->user_resolvio = $userResolvio;

        return $this;
    }

    /**
     * Get userResolvio
     *
     * @return \Presis\UserBundle\Entity\User
     */
    public function getUserResolvio()
    {
        return $this->user_resolvio;
    }
}
