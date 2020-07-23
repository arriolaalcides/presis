<?php

namespace Presis\RendicionBundle\Entity;

use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;
use JMS\Serializer\Annotation\VirtualProperty;
use JMS\Serializer\Annotation\SerializedName;
use Doctrine\Common\Collections\ArrayCollection;
use Presis\EstadoBundle\Entity\Estado;
use Presis\TrackerBundle\Entity\Tracker;
use Presis\RetiroBundle\Entity\Retiro;
use Doctrine\ORM\Mapping as ORM;

/**
 * Rendicion
 *
 * @ORM\Table(name="rendicion")
 * @ORM\Entity
 *
 * @ExclusionPolicy("all")
 */
class Rendicion
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
     * @var \DateTime
     *
     * @ORM\Column(name="fecha", type="date")
     * @Expose
     */
    private $fecha;

    /**
     * @var string
     *
     * @ORM\Column(name="detalles", type="text", nullable=true)
     * @Expose
     */
    private $detalles;

    /**
     * @var boolean
     *
     * @ORM\Column(name="cerrada", type="boolean", nullable=true)
     *
     * @Expose
     */
    private $cerrada = false;

    /**
     * @ORM\ManyToOne(targetEntity="Presis\GeneralBundle\Entity\Cliente", inversedBy="rendiciones")
     * @ORM\JoinColumn(name="cliente_id", referencedColumnName="id")
     */
    protected $cliente;

    /**
     * @VirtualProperty
     * @SerializedName("cliente")
     */
    public function getNombreCliente()
    {
        return $this->getCliente() . " ";
    }

    /**
     * @ORM\OneToMany(targetEntity="Presis\RendicionBundle\Entity\RendicionRetiro", mappedBy="rendicion", cascade={"persist", "remove"})
     */
    private $rendiciones_retiros;

    public function __construct() {
        $this->rendiciones_retiros = new ArrayCollection();
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
     * Set fecha
     *
     * @param \DateTime $fecha
     *
     * @return Rendicion
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
     * Set detalles
     *
     * @param string $detalles
     *
     * @return Rendicion
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

    /**
     * Set Cliente
     *
     * @param \Presis\GeneralBundle\Entity\Cliente $cliente
     *
     * @return Rendicion
     */
    public function setCliente(\Presis\GeneralBundle\Entity\Cliente $cliente = null)
    {
        $this->cliente = $cliente;

        return $this;
    }

    /**
     * Get Cliente
     *
     * @return \Presis\GeneralBundle\Entity\Cliente
     */
    public function getCliente()
    {
        return $this->cliente;
    }

    /**
     * Get Cliente Id. Protege de tratar de acceder a getId() de un null
     *
     * @return integer
     */
    public function getClienteId()
    {
        return ($this->getCliente())?$this->getCliente()->getId():null;
    }

    /**
     * Add Retiro, abstrae de la tabla intermedia
     *
     * @param \Presis\RetiroBundle\Entity\Retiro $retiro
     *
     * @return Rendicion
     */
    public function addRetiro(\Presis\RetiroBundle\Entity\Retiro $retiro)
    {
        $retiros = $this->getRetiros();
        if(!$retiros->contains($retiro))
        {
            $rr = new RendicionRetiro($this, $retiro);
            $this->addRendicionesRetiro($rr);
        }

        return $this;
    }

    /**
     * Get retiros
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getRetiros()
    {
        $retiros = new ArrayCollection();

        $rr = $this->getRendicionesRetiros();
        foreach($rr as $item)
        {
            $retiros[] = $item->getRetiro();
        }
        return $retiros;
    }

    /**
     * Set expreso
     *
     * @param \Presis\ExpresoBundle\Entity\Expreso $expreso
     *
     * @return Rendicion
     */
    public function setExpreso(\Presis\ExpresoBundle\Entity\Expreso $expreso = null)
    {
        $this->expreso = $expreso;

        return $this;
    }

    /**
     * Get expreso
     *
     * @return \Presis\ExpresoBundle\Entity\Expreso
     */
    public function getExpreso()
    {
        return $this->expreso;
    }

    /**
     * Set colectora
     *
     * @param string $colectora
     *
     * @return Rendicion
     */
    public function setColectora($colectora)
    {
        $this->colectora = $colectora;

        return $this;
    }

    /**
     * Get colectora
     *
     * @return string
     */
    public function getColectora()
    {
        return $this->colectora;
    }

    /**
     * Set guiaExpreso
     *
     * @param string $guiaExpreso
     *
     * @return Rendicion
     */
    public function setGuiaExpreso($guiaExpreso)
    {
        $this->guiaExpreso = $guiaExpreso;

        return $this;
    }

    /**
     * Get guiaExpreso
     *
     * @return string
     */
    public function getGuiaExpreso()
    {
        return $this->guiaExpreso;
    }

    /**
     * Set cerrada
     *
     * @param boolean $cerrada
     *
     * @return Rendicion
     */
    public function setCerrada($cerrada)
    {
        $this->cerrada = $cerrada;

        return $this;
    }

    /**
     * Get cerrada
     *
     * @return boolean
     */
    public function isCerrada()
    {
        return $this->cerrada;
    }

    /**
     * Activa el atributo rendida de los retiros
     *
     */
    public function rendirRetiros()
    {
        $rendiciones_retiros = $this->getRendicionesRetiros();

        foreach ($rendiciones_retiros as $rendicion_retiro) {
            $retiro = $rendicion_retiro->getRetiro();
            $retiro->setRendido(true);
        }
    }

    /**
     * Add rendicionesRetiro
     *
     * @param \Presis\RendicionBundle\Entity\RendicionRetiro $rendicionesRetiro
     *
     * @return Rendicion
     */
    public function addRendicionesRetiro(\Presis\RendicionBundle\Entity\RendicionRetiro $rendicionesRetiro)
    {
        $this->rendiciones_retiros[] = $rendicionesRetiro;

        return $this;
    }

    /**
     * Remove rendicionesRetiro
     *
     * @param \Presis\RendicionBundle\Entity\RendicionRetiro $rendicionesRetiro
     */
    public function removeRendicionesRetiro(\Presis\RendicionBundle\Entity\RendicionRetiro $rendicionesRetiro)
    {
        $this->rendiciones_retiros->removeElement($rendicionesRetiro);
    }

    /**
     * Get rendicionesRetiros
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getRendicionesRetiros()
    {
        return $this->rendiciones_retiros;
    }

    /**
     * El número de orden más grande
     *
     * @return integer
     */
    public function getMaxOrdenRetiros()
    {
        $max = 0;

        $rr = $this->getRendicionesRetiros();
        foreach($rr as $item)
        {
            if($item->getOrden() > $max) {
                $max = $item->getOrden();
            }
        }
        return $max;
    }
}
