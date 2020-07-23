<?php

namespace Presis\RecorridoBundle\Entity;

use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Exclude;
use JMS\Serializer\Annotation\VirtualProperty;
use JMS\Serializer\Annotation\SerializedName;
use Doctrine\Common\Collections\ArrayCollection;
use Presis\EstadoBundle\Entity\Estado;
use Presis\TrackerBundle\Entity\Tracker;
use Presis\RetiroBundle\Entity\Retiro;
use Doctrine\ORM\Mapping as ORM;

/**
 * Recorrido
 *
 * @ORM\Table(name="recorrido")
 * @ORM\Entity
 */
class Recorrido
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
     * @var \DateTime
     *
     * @ORM\Column(name="fecha", type="date")
     */
    private $fecha;

    /**
     * @var string
     *
     * @ORM\Column(name="detalles", type="text", nullable=true)
     */
    private $detalles;

    /**
     * @var string
     *
     * @ORM\Column(name="colectora", type="string", length=20, nullable=true)
     */
    private $colectora;

    /**
     * @var string
     *
     * @ORM\Column(name="guia_expreso", type="string", length=20, nullable=true)
     */
    private $guiaExpreso;

    /**
     * @var boolean
     *
     * @ORM\Column(name="es_entrega", type="boolean", nullable=true)
     */
    private $esEntrega = true;

    /**
     * @return boolean
     */
    public function getEsEntrega()
    {
        return $this->esEntrega;
    }



    /**
     * @param boolean $esEntrega
     *
     * @return Recorrido
     */
    public function setEsEntrega($esEntrega)
    {
        $this->esEntrega = $esEntrega;

        return $this;
    }

    /**
     * @var boolean
     *
     * @ORM\Column(name="cerrada", type="boolean", nullable=true)
     */
    private $cerrada = false;

    /**
     * @ORM\ManyToOne(targetEntity="Presis\DistribuidorBundle\Entity\Distribuidor", inversedBy="recorridos")
     * @ORM\JoinColumn(name="distribuidor_id", referencedColumnName="id")
     *
     * @Exclude
     */
    protected $distribuidor;

    /**
     * @VirtualProperty
     * @SerializedName("distribuidor")
     */
    public function getNombreDistribuidor()
    {
        return $this->getDistribuidor() . " ";
    }

    /**
     * @VirtualProperty
     * @SerializedName("bultos")
     */
    public function getBultos()
    {
        $bultos = 0;
        $retiros = $this->getRetiros();
        foreach ($retiros as $retiro) {
            $bultos += $retiro->getDatosEnvios()->getBultos();
        }

        return $bultos;
    }

    /**
     * @ORM\ManyToOne(targetEntity="Presis\ExpresoBundle\Entity\Expreso", inversedBy="recorridos")
     * @ORM\JoinColumn(name="expreso_id", referencedColumnName="id")
     * 
     * @Exclude
     */
    protected $expreso;

    /**
     * @VirtualProperty
     * @SerializedName("expreso")
     */
    public function getNombreExpreso()
    {
        return $this->getExpreso() . " ";
    }

    /**
     * @ORM\OneToMany(targetEntity="Presis\RecorridoBundle\Entity\RecorridoRetiro", mappedBy="recorrido", cascade={"persist", "remove"})
     */
    private $recorridos_retiros;

    public function __construct() {
        $this->recorridos_retiros = new ArrayCollection();
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
     * @return Recorrido
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
     * @return Recorrido
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
     * Set Distribuidor
     *
     * @param \Presis\DistribuidorBundle\Entity\Distribuidor $distribuidor
     *
     * @return Recorrido
     */
    public function setDistribuidor(\Presis\DistribuidorBundle\Entity\Distribuidor $distribuidor = null)
    {
        $this->distribuidor = $distribuidor;

        return $this;
    }

    /**
     * Get Distribuidor
     *
     * @return \Presis\DistribuidorBundle\Entity\Distribuidor
     */
    public function getDistribuidor()
    {
        return $this->distribuidor;
    }

    /**
     * Add Retiro, abstrae de la tabla intermedia
     *
     * @param \Presis\RetiroBundle\Entity\Retiro $retiro
     *
     * @return Recorrido
     */
    public function addRetiro(\Presis\RetiroBundle\Entity\Retiro $retiro)
    {
        $retiros = $this->getRetiros();
        if(!$retiros->contains($retiro))
        {
            $rr = new RecorridoRetiro($this, $retiro);
            $this->addRecorridosRetiro($rr);
        }

        return $this;
    }

    /**
     * @VirtualProperty
     * @SerializedName("cantidad_retiros")
     */
    public function getCantidadRetiros()
    {
        return $this->getRecorridosRetiros()->count();
    }

    /**
     * Get retiros
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getRetiros()
    {
        $retiros = new ArrayCollection();

        $rr = $this->getRecorridosRetiros();
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
     * @return Recorrido
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
     * @return Recorrido
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
     * @return Recorrido
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
     * @return Recorrido
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
     * Cambia el estado de todos los retiros del recorrido a "Distribución"
     *
     */
    public function finalizarRetiros($estado, $user)
    {
        $recorridos_retiros = $this->getRecorridosRetiros();

        foreach ($recorridos_retiros as $recorrido_retiro) {
            $retiro = $recorrido_retiro->getRetiro();
            $retiro->setEstado($estado);

            /* También actualiza la entidad Tracker */
            $tracker = new Tracker();
            $tracker->setRetiro($retiro);
            $tracker->setEstado($estado);
            $tracker->setUser($user);

            $retiro->addHistorico($tracker);
        }
    }

    /**
     * Add recorridosRetiro
     *
     * @param \Presis\RecorridoBundle\Entity\RecorridoRetiro $recorridosRetiro
     *
     * @return Recorrido
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

    /**
     * El número de orden más grande
     *
     * @return integer
     */
    public function getMaxOrdenRetiros()
    {
        $max = 0;

        $rr = $this->getRecorridosRetiros();
        foreach($rr as $item)
        {
            if($item->getOrden() > $max) {
                $max = $item->getOrden();
            }
        }
        return $max;
    }

//AGREGO PARA GENERAR SUCURSAL DE DESTINO 26-06-17
    /**
     * @ORM\ManyToOne(targetEntity="Presis\GeneralBundle\Entity\Sucursal", inversedBy="sucursal")
     * @ORM\JoinColumn(name="sucursal_id", referencedColumnName="id")
     *
     */
    protected $sucursal;

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


}