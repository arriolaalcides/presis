<?php

namespace Presis\FirmasBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Firmas
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Presis\FirmasBundle\Entity\FirmasRepository")
 */
class Firmas
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
     * @var integer
     *
     * @ORM\Column(name="tracking", type="integer")
     */
    private $tracking;

    /**
     * @var string
     *
     * @ORM\Column(name="img", type="string", length=255)
     */
    private $img;

    /**
     * @var string
     *
     * @ORM\Column(name="codEstado", type="string", length=10)
     */
    private $codEstado;

    /**
     * @var string
     *
     * @ORM\Column(name="detalleEstado", type="string", length=45)
     */
    private $detalleEstado;

    /**
     * @var string
     *
     * @ORM\Column(name="recibio", type="string", length=45)
     */
    private $recibio;

    /**
     * @var string
     *
     * @ORM\Column(name="documento", type="string", length=45)
     */
    private $documento;

    /**
     * @var string
     *
     * @ORM\Column(name="obs", type="string", length=100)
     */
    private $obs;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fechaCel", type="datetime")
     */
    private $fechaCel;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fechaBase", type="datetime")
     */
    private $fechaBase;

    /**
     * @var integer
     *
     * @ORM\Column(name="distribuidor_id", type="integer")
     */
    private $distribuidorId;


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
     * Set tracking
     *
     * @param integer $tracking
     *
     * @return Firmas
     */
    public function setTracking($tracking)
    {
        $this->tracking = $tracking;

        return $this;
    }

    /**
     * Get tracking
     *
     * @return integer
     */
    public function getTracking()
    {
        return $this->tracking;
    }

    /**
     * Set img
     *
     * @param string $img
     *
     * @return Firmas
     */
    public function setImg($img)
    {
        $this->img = $img;

        return $this;
    }

    /**
     * Get img
     *
     * @return string
     */
    public function getImg()
    {
        return $this->img;
    }

    /**
     * Set codEstado
     *
     * @param string $codEstado
     *
     * @return Firmas
     */
    public function setCodEstado($codEstado)
    {
        $this->codEstado = $codEstado;

        return $this;
    }

    /**
     * Get codEstado
     *
     * @return string
     */
    public function getCodEstado()
    {
        return $this->codEstado;
    }

    /**
     * Set detalleEstado
     *
     * @param string $detalleEstado
     *
     * @return Firmas
     */
    public function setDetalleEstado($detalleEstado)
    {
        $this->detalleEstado = $detalleEstado;

        return $this;
    }

    /**
     * Get detalleEstado
     *
     * @return string
     */
    public function getDetalleEstado()
    {
        return $this->detalleEstado;
    }

    /**
     * Set recibio
     *
     * @param string $recibio
     *
     * @return Firmas
     */
    public function setRecibio($recibio)
    {
        $this->recibio = $recibio;

        return $this;
    }

    /**
     * Get recibio
     *
     * @return string
     */
    public function getRecibio()
    {
        return $this->recibio;
    }

    /**
     * Set documento
     *
     * @param string $documento
     *
     * @return Firmas
     */
    public function setDocumento($documento)
    {
        $this->documento = $documento;

        return $this;
    }

    /**
     * Get documento
     *
     * @return string
     */
    public function getDocumento()
    {
        return $this->documento;
    }

    /**
     * Set obs
     *
     * @param string $obs
     *
     * @return Firmas
     */
    public function setObs($obs)
    {
        $this->obs = $obs;

        return $this;
    }

    /**
     * Get obs
     *
     * @return string
     */
    public function getObs()
    {
        return $this->obs;
    }

    /**
     * Set fechaCel
     *
     * @param \DateTime $fechaCel
     *
     * @return Firmas
     */
    public function setFechaCel($fechaCel)
    {
        $this->fechaCel = $fechaCel;

        return $this;
    }

    /**
     * Get fechaCel
     *
     * @return \DateTime
     */
    public function getFechaCel()
    {
        return $this->fechaCel;
    }

    /**
     * Set fechaBase
     *
     * @param \DateTime $fechaBase
     *
     * @return Firmas
     */
    public function setFechaBase($fechaBase)
    {
        $this->fechaBase = $fechaBase;

        return $this;
    }

    /**
     * Get fechaBase
     *
     * @return \DateTime
     */
    public function getFechaBase()
    {
        return $this->fechaBase;
    }

    /**
     * Set distribuidorId
     *
     * @param integer $distribuidorId
     *
     * @return Firmas
     */
    public function setDistribuidorId($distribuidorId)
    {
        $this->distribuidorId = $distribuidorId;

        return $this;
    }

    /**
     * Get distribuidorId
     *
     * @return integer
     */
    public function getDistribuidorId()
    {
        return $this->distribuidorId;
    }

    function __toString()
    {
        return $this->img;
    }

    /**
     * @var string
     *
     * @ORM\Column(name="distribuidor", type="string", length=100)
     */
    private $distribuidor;

    /**
     * @return string
     */
    public function getDistribuidor()
    {
        return $this->distribuidor;
    }

    /**
     * @param string $distribuidor
     */
    public function setDistribuidor($distribuidor)
    {
        $this->distribuidor = $distribuidor;
    }

    /**
     * @var string
     *
     * @ORM\Column(name="lat", type="string",  length=100, nullable=true)
     */
    private $lat;

    /**
     * @var string
     *
     * @ORM\Column(name="lon", type="string",  length=100, nullable=true)
     */
    private $lon;

    /**
     * @return string
     */
    public function getLat()
    {
        return $this->lat;
    }

    /**
     * @param string $lat
     */
    public function setLat($lat)
    {
        $this->lat = $lat;
    }

    /**
     * @return string
     */
    public function getLon()
    {
        return $this->lon;
    }

    /**
     * @param string $lon
     */
    public function setLon($lon)
    {
        $this->lon = $lon;
    }



}

