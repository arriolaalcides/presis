<?php

namespace Presis\GeneralBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * PesoRangoExcedente
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Presis\GeneralBundle\Entity\PesoRangoExcedenteRepository")
 */
class PesoRangoExcedente
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
     * @ORM\ManyToOne(targetEntity="Presis\GeneralBundle\Entity\Cliente", inversedBy="rendiciones")
     * @ORM\JoinColumn(name="cliente_id", referencedColumnName="id")
     *
     */
    private $cliente;

    /**
     * @ORM\ManyToOne(targetEntity="Presis\ServicioBundle\Entity\Servicio")
     * @ORM\JoinColumn(name="servicio_id", referencedColumnName="id")
     *
     **/
    private $servicio;

    /**
     * @ORM\ManyToOne(targetEntity="Presis\ServicioBundle\Entity\Cordon")
     * @ORM\JoinColumn(name="cordonEntrega_id", referencedColumnName="id",nullable=false)
     *
     */
    private $cordonEntrega;

    /**
     * @ORM\ManyToOne(targetEntity="Presis\ServicioBundle\Entity\Cordon")
     * @ORM\JoinColumn(name="cordonRetiro_id", referencedColumnName="id",nullable=false)
     *
     */
    private $cordonRetiro;

    /**
     * @var integer
     *
     * @ORM\Column(name="rangoHasta", type="integer")
     */
    private $rangoHasta;

    /**
     * @var string
     *
     * @ORM\Column(name="costoRangoExcedente", type="decimal", scale=2)
     */
    private $costoRangoExcedente;


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
     * Set cliente
     *
     * @param string $cliente
     *
     * @return PesoRangoExcedente
     */
    public function setCliente($cliente)
    {
        $this->cliente = $cliente;

        return $this;
    }

    /**
     * Get cliente
     *
     * @return string
     */
    public function getCliente()
    {
        return $this->cliente;
    }

    /**
     * Set servicio
     *
     * @param string $servicio
     *
     * @return PesoRangoExcedente
     */
    public function setServicio($servicio)
    {
        $this->servicio = $servicio;

        return $this;
    }

    /**
     * Get servicio
     *
     * @return string
     */
    public function getServicio()
    {
        return $this->servicio;
    }

    /**
     * Set cordonEntrega
     *
     * @param integer $cordonEntrega
     *
     * @return PesoRangoExcedente
     */
    public function setCordonEntrega($cordonEntrega)
    {
        $this->cordonEntrega = $cordonEntrega;

        return $this;
    }

    /**
     * Get cordonEntrega
     *
     * @return integer
     */
    public function getCordonEntrega()
    {
        return $this->cordonEntrega;
    }

    /**
     * Set cordonRetiro
     *
     * @param integer $cordonRetiro
     *
     * @return PesoRangoExcedente
     */
    public function setCordonRetiro($cordonRetiro)
    {
        $this->cordonRetiro = $cordonRetiro;

        return $this;
    }

    /**
     * Get cordonRetiro
     *
     * @return integer
     */
    public function getCordonRetiro()
    {
        return $this->cordonRetiro;
    }

    /**
     * Set rangoHasta
     *
     * @param integer $rangoHasta
     *
     * @return PesoRangoExcedente
     */
    public function setRangoHasta($rangoHasta)
    {
        $this->rangoHasta = $rangoHasta;

        return $this;
    }

    /**
     * Get rangoHasta
     *
     * @return integer
     */
    public function getRangoHasta()
    {
        return $this->rangoHasta;
    }

    /**
     * Set costoRangoExcedente
     *
     * @param float $costoRangoExcedente
     *
     * @return PesoRangoExcedente
     */
    public function setCostoRangoExcedente($costoRangoExcedente)
    {
        $this->costoRangoExcedente = $costoRangoExcedente;

        return $this;
    }

    /**
     * Get costoRangoExcedente
     *
     * @return float
     */
    public function getCostoRangoExcedente()
    {
        return $this->costoRangoExcedente;
    }

    public function __toString() {
        return $this->costoRangoExcedente;
    }

    /**
     * @var string
     *
     * @ORM\Column(name="tipoServicio", type="string", length=254)
     *
     */
    private $tipoServicio;

    /**
     * @return string
     */
    public function getTipoServicio()
    {
        return $this->tipoServicio;
    }

    /**
     * @param string $tipoServicio
     */
    public function setTipoServicio($tipoServicio)
    {
        $this->tipoServicio = $tipoServicio;
    }





}

