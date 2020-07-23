<?php

namespace Presis\RendicionBundle\Entity;

use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;
use JMS\Serializer\Annotation\VirtualProperty;
use JMS\Serializer\Annotation\SerializedName;
use \Presis\CompradorBundle\Entity\Comprador;
use Doctrine\ORM\Mapping as ORM;

/**
 * RendicionRetiro
 *
 * @ORM\Table(name="rendiciones_retiros")
 * @ORM\Entity
 *
 * @ExclusionPolicy("all")
 */
class RendicionRetiro
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
     * @ORM\ManyToOne(targetEntity="Presis\RendicionBundle\Entity\Rendicion", inversedBy="rendiciones_retiros")
     * @ORM\JoinColumn(name="rendicion_id", referencedColumnName="id")
     */
    protected $rendicion;

    /**
     * @ORM\ManyToOne(targetEntity="Presis\RetiroBundle\Entity\Retiro", inversedBy="rendiciones_retiros")
     * @ORM\JoinColumn(name="retiro_id", referencedColumnName="id")
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
     * Constructor con Rendicion y retiro
     *
     * @param \Presis\RendicionBundle\Entity\Rendicion $rendicion
     * @param \Presis\RetiroBundle\Entity\Retiro $retiro
     */
    public function __construct(\Presis\RendicionBundle\Entity\Rendicion $rendicion = null, \Presis\RetiroBundle\Entity\Retiro $retiro)
    {
        $this->retiro = $retiro;
        $this->setFechaCreacion(new \DateTime('now'));

        if ($rendicion) {
            $this->rendicion = $rendicion;
            $this->setOrden($rendicion->getMaxOrdenRetiros() + 1);
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
     * @return RendicionRetiro
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
     * Set Rendicion
     *
     * @param \Presis\RendicionBundle\Entity\Rendicion $rendicion
     *
     * @return RendicionRetiro
     */
    public function setRendicion(\Presis\RendicionBundle\Entity\Rendicion $rendicion = null)
    {
        $this->rendicion = $rendicion;

        return $this;
    }

    /**
     * Get rendicion
     *
     * @return \Presis\RendicionBundle\Entity\Rendicion
     */
    public function getRendicion()
    {
        return $this->rendicion;
    }

    /**
     * Set retiro
     *
     * @param \Presis\RetiroBundle\Entity\Retiro $retiro
     *
     * @return RendicionRetiro
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
     * Set distribuidor
     *
     * @param \Presis\DistribuidorBundle\Entity\Distribuidor $distribuidor
     *
     * @return RendicionRetiro
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

    /**
     * Set user
     *
     * @param \Presis\UserBundle\Entity\User $user
     *
     * @return RendicionRetiro
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
     * @return RendicionRetiro
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
}
