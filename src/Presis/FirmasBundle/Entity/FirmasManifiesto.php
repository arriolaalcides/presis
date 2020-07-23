<?php

namespace Presis\FirmasBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * FirmasManifiesto
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Presis\FirmasBundle\Entity\FirmasManifiestoRepository")
 */
class FirmasManifiesto
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
     * @ORM\Column(name="nro_manifiesto", type="integer")
     */
    private $nroManifiesto;

    /**
     * @var string
     *
     * @ORM\Column(name="img", type="string", length=254, nullable=true)
     */
    private $img;

    /**
     * @var string
     *
     * @ORM\Column(name="estado", type="string", length=45, nullable=true)
     */
    private $estado;

    /**
     * @var string
     *
     * @ORM\Column(name="recibio", type="string", length=45, nullable=true)
     */
    private $recibio;

    /**
     * @var string
     *
     * @ORM\Column(name="documento", type="string", length=45, nullable=true)
     */
    private $documento;

    /**
     * @var string
     *
     * @ORM\Column(name="obs", type="string", length=254, nullable=true)
     */
    private $obs;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fechaCel", type="datetime", nullable=true)
     */
    private $fechaCel;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fechaBase", type="datetime"), nullable=true
     */
    private $fechaBase;

    /**
     * @var integer
     *
     * @ORM\Column(name="distribuidor_id", type="integer", nullable=true)
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
     * Set nroManifiesto
     *
     * @param integer $nroManifiesto
     *
     * @return FirmasManifiesto
     */
    public function setNroManifiesto($nroManifiesto)
    {
        $this->nroManifiesto = $nroManifiesto;

        return $this;
    }

    /**
     * Get nroManifiesto
     *
     * @return integer
     */
    public function getNroManifiesto()
    {
        return $this->nroManifiesto;
    }

    /**
     * Set img
     *
     * @param string $img
     *
     * @return FirmasManifiesto
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
     * Set estado
     *
     * @param string $estado
     *
     * @return FirmasManifiesto
     */
    public function setEstado($estado)
    {
        $this->estado = $estado;

        return $this;
    }

    /**
     * Get estado
     *
     * @return string
     */
    public function getEstado()
    {
        return $this->estado;
    }

    /**
     * Set recibio
     *
     * @param string $recibio
     *
     * @return FirmasManifiesto
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
     * @return FirmasManifiesto
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
     * @return FirmasManifiesto
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
     * @return \DateTime
     */
    public function getFechaCel()
    {
        return $this->fechaCel;
    }

    /**
     * @param \DateTime $fechaCel
     */
    public function setFechaCel($fechaCel)
    {
        $this->fechaCel = $fechaCel;
    }

    /**
     * @return \DateTime
     */
    public function getFechaBase()
    {
        return $this->fechaBase;
    }

    /**
     * @param \DateTime $fechaBase
     */
    public function setFechaBase($fechaBase)
    {
        $this->fechaBase = $fechaBase;
    }


    /**
     * Set distribuidorId
     *
     * @param integer $distribuidorId
     *
     * @return FirmasManifiesto
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
}

