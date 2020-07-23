<?php

namespace Presis\TrackerBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Exclude;
use JMS\Serializer\Annotation\VirtualProperty;
use JMS\Serializer\Annotation\SerializedName;
use Presis\RetiroBundle\Entity\Retiro;
use Presis\RetiroBundle\Entity\Motivo;
use Presis\UserBundle\Entity\User;
use Presis\EstadoBundle\Entity\Estado;

/**
 * Tracker
 *
 * @ORM\Table(name="tracker")
 * @ORM\Entity
 */
class Tracker
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
     * @ORM\ManyToOne(targetEntity="Presis\DistribuidorBundle\Entity\Distribuidor", inversedBy="trackers")
     * @ORM\JoinColumn(name="distribuidor_id", referencedColumnName="id")
     *
     * @Exclude
     */
    private $distribuidor;

    /**
     * @return mixed
     */
    public function getDistribuidor()
    {
        return $this->distribuidor;
    }

    /**
     * @param mixed $distribuidor
     */
    public function setDistribuidor($distribuidor)
    {
        $this->distribuidor = $distribuidor;
    }

    /**
     * @VirtualProperty
     * @SerializedName("distribuidor")
     */
    public function getNombreDistribuidor()
    {
        $distribuidor = $this->getDistribuidor();
        $name = ($distribuidor)?$distribuidor->__toString():"";

        return $name;
    }

    /**
     * @ORM\ManyToOne(targetEntity="Presis\EstadoBundle\Entity\Estado", inversedBy="historicos")
     * @ORM\JoinColumn(name="estado_id", referencedColumnName="id")
     *
     * @Exclude
     */
    protected $estado;

    /**
     * @VirtualProperty
     * @SerializedName("estado")
     */
    public function getNombreEstado()
    {
        return $this->getEstado() . " ";
    }

    /**
     * @ORM\ManyToOne(targetEntity="Presis\RetiroBundle\Entity\Retiro", inversedBy="historicos")
     * @ORM\JoinColumn(name="retiro_id", referencedColumnName="id")
     *
     * @Exclude
     */
    protected $retiro;

    /**
     * @VirtualProperty
     * @SerializedName("tracker")
     */
    public function getNombreRetiro()
    {
        $retiro = $this->getRetiro();
        $name = ($retiro)?$retiro->getId():"";

        return $name;
    }

    /**
     * @ORM\ManyToOne(targetEntity="Presis\RetiroBundle\Entity\Motivo", inversedBy="historicos")
     * @ORM\JoinColumn(name="motivo_id", referencedColumnName="id")
     *
     * @Exclude
     */
    protected $motivo;

    /**
     * @VirtualProperty
     * @SerializedName("motivo")
     */
    public function getNombreMotivo()
    {
        $motivo = $this->getMotivo();
        $name = ($motivo)?$motivo->getName():"";

        return $name;
    }

    /**
     * @ORM\ManyToOne(targetEntity="Presis\UserBundle\Entity\User", inversedBy="historicos")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     *
     * @Exclude
     */
    protected $user;


    /**
     * @VirtualProperty
     * @SerializedName("user")
     */
    public function getUserName()
    {
        $name = ($this->getUser())?$this->getUser()->getUsername():"";

        return $name;
    }

    /**
     * @var string
     *
     * @ORM\Column(name="detalles", type="text", nullable=true)
     *
     *
     */
    private $detalles = "";

    /**
     * @var string
     *
     * @ORM\Column(name="obs", type="text", nullable=true)
     *
     */
    private $obs = "";

    /**
     * @var string
     *
     * @ORM\Column(name="receptor_nombre", type="string", length=100, nullable=true)
     *
     * @Exclude
     */
    private $receptorNombre = "";

    /**
     * @var string
     *
     * @ORM\Column(name="receptor_apellido", type="string", length=100, nullable=true)
     *
     * @Exclude
     */
    private $receptorApellido = "";

    /**
     * @VirtualProperty
     * @SerializedName("receptor_nombre_completo")
     */
    public function getReceptorNombreCompleto()
    {
        $separator = ($this->getReceptorApellido() == "" || $this->getReceptorNombre() == "")? "" :", ";
        return $this->getReceptorApellido() . $separator . $this->getReceptorNombre();
    }

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="receptor_fecha_hora", type="datetime", nullable=true)
     *
     * @Exclude
     */
    private $receptorFechaHora;

    /**
     * @var string
     *
     * @ORM\Column(name="dni", type="string", length=20, nullable=true)
     *
     * @Exclude
     */
    private $dni;

    /**
     * @VirtualProperty
     * @SerializedName("receptor_fecha_hora")
     *
     * @return string
     */
    public function getFechaRecepcion()
    {
        return ($this->getReceptorFechaHora())?$this->getReceptorFechaHora()->format('d/m/Y H:i'):" ";
    }

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="timestamp_modificacion", type="datetime")
     */
    private $timestampModificacion;

    public function __toString()
    {
        return $this->getId()."";
    }

    public function __construct()
    {
        $this->setTimestampModificacion(new \DateTime("now"));
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
     * Set receptorNombre
     *
     * @param string $receptorNombre
     *
     * @return Tracker
     */
    public function setReceptorNombre($receptorNombre)
    {
        $this->receptorNombre = $receptorNombre;

        return $this;
    }

    /**
     * Get receptorNombre
     *
     * @return string
     */
    public function getReceptorNombre()
    {
        return $this->receptorNombre;
    }

    /**
     * Set receptorApellido
     *
     * @param string $receptorApellido
     *
     * @return Tracker
     */
    public function setReceptorApellido($receptorApellido)
    {
        $this->receptorApellido = $receptorApellido;

        return $this;
    }

    /**
     * Get receptorApellido
     *
     * @return string
     */
    public function getReceptorApellido()
    {
        return $this->receptorApellido;
    }

    /**
     * Set receptorFechaHora
     *
     * @param \DateTime $receptorFechaHora
     *
     * @return Tracker
     */
    public function setReceptorFechaHora($receptorFechaHora)
    {
        $this->receptorFechaHora = $receptorFechaHora;

        return $this;
    }

    /**
     * Get receptorFechaHora
     *
     * @return \DateTime
     */
    public function getReceptorFechaHora()
    {
        return $this->receptorFechaHora;
    }

    /**
     * Set timestampModificado
     *
     * @param \DateTime $timestampModificacion
     *
     * @return Tracker
     */
    public function setTimestampModificacion($timestampModificacion)
    {
        $this->timestampModificacion = $timestampModificacion;

        return $this;
    }

    /**
     * Get timestampModificado
     *
     * @return \DateTime
     */
    public function getTimestampModificacion()
    {
        return $this->timestampModificacion;
    }

    /**
     * Set estado
     *
     * @param \Presis\EstadoBundle\Entity\Estado $estado
     *
     * @return Tracker
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
     * Set retiro
     *
     * @param \Presis\RetiroBundle\Entity\Retiro $retiro
     *
     * @return Tracker
     */
    public function setRetiro(Retiro $retiro = null)
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
     * Set motivo
     *
     * @param \Presis\RetiroBundle\Entity\Motivo $motivo
     *
     * @return Tracker
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
     * Set user
     *
     * @param \Presis\UserBundle\Entity\User $user
     *
     * @return Tracker
     */
    public function setUser(\Presis\UserBundle\Entity\User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \Presis\UserBundle\Entity\User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set detalles
     *
     * @param string $detalles
     *
     * @return Tracker
     */
    public function setDetalles($detalles)
    {
        $this->detalles = $detalles;

        return $this;
    }

    /**
     * Get detalles
     *
     * @return string
     */
    public function getDetalles()
    {
        return $this->detalles;
    }

    /*=================================AGREGO CAMPO PLANILLA PARA GUARDAR EL HISTORICO DE LA PLANILLA==================*/

    /**
     * @var int
     *
     * @ORM\Column(name="nroPlanilla", type="integer", nullable=true)
     *
     * @Exclude
     */
    private $nroPlanilla = 0;

    /**
     * @VirtualProperty
     * @SerializedName("nro_planilla")
     */
    public function getNroPlanillaString()
    {
        return $this->getNroPlanilla() . " ";
    }

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
     * @VirtualProperty
     * @SerializedName("dni")
     */
    public function getNroDni()
    {
        return $this->getDni() . " ";
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


    //PICCINI - SE AGREGA PARA GUARDAR LA FECHA DE PLANILLA Y PODER SACAR LA ULTIMA.
    /**
     * @var \Date
     *
     * @ORM\Column(name="fecha_planilla", type="date", nullable=true)
     *
     * @Exclude
     */
    private $fechaPlanilla;

    /**
     * @return \Date
     */
    public function getFechaPlanilla()
    {
        return $this->fechaPlanilla;
    }

    /**
     * @param \Date $fechaPlanilla
     */
    public function setFechaPlanilla($fechaPlanilla)
    {
        $this->fechaPlanilla = $fechaPlanilla;
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
     * @var string
     *
     * @ORM\Column(name="sucursal", type="string", length=100, nullable=true)
     *
     * @Exclude
     */
    private $sucursal;

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
     * @VirtualProperty
     * @SerializedName("sucursal_nombre")
     */
    public function getSucursalNombre()
    {
        return $this->getSucursal() . " ";
    }

    /**
     * @return string
     */
    public function getObs()
    {
        return $this->obs;
    }

    /**
     * @param string $obs
     */
    public function setObs($obs)
    {
        $this->obs = $obs;
    }

    /**
     * @VirtualProperty
     * @SerializedName("observacion")
     */
    public function getObservaciones()
    {
        return $this->getObs() . " ";
    }

    /**
     * @var string
     *
     * @ORM\Column(name="updateTracker", type="string", length=40, nullable=true)
     */
    private $updateTracker;

    /**
     * @return mixed
     */
    public function getUpdateTracker()
    {
        return $this->updateTracker;
    }

    /**
     * @param mixed $updateTracker
     */
    public function setUpdateTracker($updateTracker)
    {
        $this->updateTracker = $updateTracker;
    }

    


}
