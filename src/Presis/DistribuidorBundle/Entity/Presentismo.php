<?php

namespace Presis\DistribuidorBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Presentismo
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Presis\DistribuidorBundle\Entity\PresentismoRepository")
 */
class Presentismo
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
     * @var \DateTime
     *
     * @ORM\Column(name="hora", type="time")
     */
    private $hora;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="timestamp", type="datetime")
     */
    private $timestamp;


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
     * @return Presentismo
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
     * Set hora
     *
     * @param \DateTime $hora
     *
     * @return Presentismo
     */
    public function setHora($hora)
    {
        $this->hora = $hora;

        return $this;
    }

    /**
     * Get hora
     *
     * @return \DateTime
     */
    public function getHora()
    {
        return $this->hora;
    }



    /**
     * Set timestamp
     *
     * @param \DateTime $timestamp
     *
     * @return Presentismo
     */
    public function setTimestamp($timestamp)
    {
        $this->timestamp = $timestamp;

        return $this;
    }

    /**
     * Get timestamp
     *
     * @return \DateTime
     */
    public function getTimestamp()
    {
        return $this->timestamp;
    }

    /**
     * @var string
     *
     * @ORM\Column(name="estado", type="string", length=45)
     */
    private $estado;

    /**
     * @return string
     */
    public function getEstado()
    {
        return $this->estado;
    }

    /**
     * @param string $estado
     */
    public function setEstado($estado)
    {
        $this->estado = $estado;
    }

    /**
     * @var string
     *
     * @ORM\Column(name="apenom", type="string", length=150)
     */
    private $apenom;


    /**
     * @var string
     *
     * @ORM\Column(name="codigo", type="string", length=100)
     */
    private $codigo;

    /**
     * @return string
     */
    public function getCodigo()
    {
        return $this->codigo;
    }

    /**
     * @param string $codigo
     */
    public function setCodigo($codigo)
    {
        $this->codigo = $codigo;
    }

    /**
     * @var string
     *
     * @ORM\Column(name="recorrido", type="string", length=100)
     */
    private $recorrido;

    /**
     * @var string
     *
     * @ORM\Column(name="observaciones", type="string", length=255)
     */
    private $observaciones;

    /**
     * @return string
     */
    public function getApenom()
    {
        return $this->apenom;
    }

    /**
     * @param string $apenom
     */
    public function setApenom($apenom)
    {
        $this->apenom = $apenom;
    }

    /**
     * @return string
     */
    public function getRecorrido()
    {
        return $this->recorrido;
    }

    /**
     * @param string $recorrido
     */
    public function setRecorrido($recorrido)
    {
        $this->recorrido = $recorrido;
    }

    /**
     * @return string
     */
    public function getObservaciones()
    {
        return $this->observaciones;
    }

    /**
     * @param string $observaciones
     */
    public function setObservaciones($observaciones)
    {
        $this->observaciones = $observaciones;
    }




}

