<?php

namespace Presis\RecorridoBundle\Entity;

use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;
use JMS\Serializer\Annotation\VirtualProperty;
use JMS\Serializer\Annotation\SerializedName;
use \Presis\CompradorBundle\Entity\Comprador;
use Doctrine\ORM\Mapping as ORM;

/**
 * RecorridoRetiro
 *
 * @ORM\Table(name="recorridos_retiros")
 * @ORM\Entity
 *
 * @ExclusionPolicy("all")
 */
class RecorridoRetiro
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
     * @var integer
     *
     * @ORM\Column(name="orden", type="integer", nullable=true)
     *
     * @Expose
     */
    private $orden;

    /**
     * @VirtualProperty
     * @SerializedName("id_retiro")
     */
    public function getIdRetiro()
    {
        return $this->getRetiro()->getId();
    }

    /**
     * @VirtualProperty
     * @SerializedName("comprador")
     */
    public function getNombreComprador()
    {
        return $this->getRetiro()->getNombreComprador();
    }

    /**
     * @VirtualProperty
     * @SerializedName("comprador_direccion")
     */
    public function getDireccionComprador()
    {
        return $this->getRetiro()->getDireccionComprador();
    }

    /**
     * @VirtualProperty
     * @SerializedName("comprador_email")
     */
    public function getEmailComprador()
    {
        return $this->getRetiro()->getEmailComprador();
    }

    /**
     * @VirtualProperty
     * @SerializedName("comprador_localidad")
     */
    public function getLocalidadComprador()
    {
        return $this->getRetiro()->getLocalidadComprador();
    }

    /**
     * @VirtualProperty
     * @SerializedName("contrareembolso")
     */
    public function getContrareembolso()
    {
        $retiro = $this->getRetiro();

        return ($retiro && $retiro->getDatosEnvios())?$retiro->getDatosEnvios()->getContrareembolso():0;
    }

    /**
     * @ORM\ManyToOne(targetEntity="Presis\RecorridoBundle\Entity\Recorrido", inversedBy="recorridos_retiros")
     * @ORM\JoinColumn(name="recorrido_id", referencedColumnName="id")
     */
    protected $recorrido;

    /**
     * @ORM\ManyToOne(targetEntity="Presis\RetiroBundle\Entity\Retiro", inversedBy="recorridos_retiros")
     * @ORM\JoinColumn(name="retiro_id", referencedColumnName="id")
     *
     */
    protected $retiro;

    /**
     * @ORM\ManyToOne(targetEntity="Presis\UserBundle\Entity\User", inversedBy="retiros_planillados")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    protected $user;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fecha_creacion", type="date")
     */
    private $fechaCreacion;

    /**
     * Constructor con Recorrido y retiro
     *
     * @param \Presis\RecorridoBundle\Entity\Recorrido $recorrido
     * @param \Presis\RetiroBundle\Entity\Retiro $retiro
     */
    public function __construct(\Presis\RecorridoBundle\Entity\Recorrido $recorrido = null, \Presis\RetiroBundle\Entity\Retiro $retiro)
    {
        $this->retiro = $retiro;
        $this->setFechaCreacion(new \DateTime('now'));

        if ($recorrido) {
            $this->recorrido = $recorrido;
            $this->setOrden($recorrido->getMaxOrdenRetiros() + 1);
        }
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
     * Set orden
     *
     * @param integer $orden
     *
     * @return RecorridoRetiro
     */
    public function setOrden($orden)
    {
        $this->orden = $orden;

        return $this;
    }

    /**
     * Get orden
     *
     * @return integer
     */
    public function getOrden()
    {
        return $this->orden;
    }

    /**
     * Set recorrido
     *
     * @param \Presis\RecorridoBundle\Entity\Recorrido $recorrido
     *
     * @return RecorridoRetiro
     */
    public function setRecorrido(\Presis\RecorridoBundle\Entity\Recorrido $recorrido = null)
    {
        $this->recorrido = $recorrido;

        return $this;
    }

    /**
     * Get recorrido
     *
     * @return \Presis\RecorridoBundle\Entity\Recorrido
     */
    public function getRecorrido()
    {
        return $this->recorrido;
    }

    /**
     * Set retiro
     *
     * @param \Presis\RetiroBundle\Entity\Retiro $retiro
     *
     * @return RecorridoRetiro
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
     * Set user
     *
     * @param \Presis\UserBundle\Entity\User $user
     *
     * @return RecorridoRetiro
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
     * Set fechaInsercion
     *
     * @param \DateTime $fechaCreacion
     *
     * @return RecorridoRetiro
     */
    public function setFechaCreacion($fechaCreacion)
    {
        $this->fechaCreacion = $fechaCreacion;

        return $this;
    }

    /**
     * Get fechaInsercion
     *
     * @return \DateTime
     */
    public function getFechaCreacion()
    {
        return $this->fechaCreacion;
    }

    /*======================= VUELVO A AGREGAR PORQUE SI NO, NO FUNCIONA EL IMPORTADOR DE COLECTORES ======================*/
    /**
     * @ORM\ManyToOne(targetEntity="Presis\DistribuidorBundle\Entity\Distribuidor", inversedBy="retiros_planillados")
     * @ORM\JoinColumn(name="distribuidor_id", referencedColumnName="id")
     */
    protected $distribuidor;

    /**
     * Set distribuidor
     *
     * @param \Presis\DistribuidorBundle\Entity\Distribuidor $distribuidor
     *
     * @return RecorridoRetiro
     */
    public function setDistribuidor(\Presis\DistribuidorBundle\Entity\Distribuidor $distribuidor = null)
    {
        $this->distribuidor = $distribuidor;

        return $this;
    }

    /**
     * Get distribuidor
     *
     * @return \Presis\DistribuidorBundle\Entity\Distribuidor
     */
    public function getDistribuidor()
    {
        return $this->distribuidor;
    }

}
