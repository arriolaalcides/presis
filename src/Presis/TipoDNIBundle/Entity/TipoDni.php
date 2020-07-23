<?php

namespace Presis\TipoDNIBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * TipoDni
 *
 * @ORM\Table(name="tipodni")
 * @ORM\Entity(repositoryClass="Presis\TipoDNIBundle\Entity\TipoDniRepository")
 */
class TipoDni
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
     * @var string
     *
     * @ORM\Column(name="nombre", type="string", length=40)
     */
    private $nombre;

    /**
     * @ORM\OneToMany(targetEntity="Presis\DistribuidorBundle\Entity\Distribuidor", mappedBy="tipodni")
     */
    protected $distribuidores;

    /**
     * @ORM\OneToMany(targetEntity="Presis\GeneralBundle\Entity\Cliente", mappedBy="tipoDocumento")
     */
    protected $clientes;


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
     * Set nombre
     *
     * @param string $nombre
     *
     * @return TipoDni
     */
    public function setNombre($nombre)
    {
        $this->nombre = $nombre;

        return $this;
    }

    /**
     * Get nombre
     *
     * @return string
     */
    public function getNombre()
    {
        return $this->nombre;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->distribuidores = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add Distribuidor
     *
     * @param \Presis\DistribuidorBundle\Entity\Distribuidor $distribuidor
     *
     * @return TipoDni
     */
    public function addDistribuidor(\Presis\DistribuidorBundle\Entity\Distribuidor $distribuidor)
    {
        $this->distribuidores[] = $distribuidor;

        return $this;
    }

    /**
     * Remove Distribuidor
     *
     * @param \Presis\DistribuidorBundle\Entity\Distribuidor $distribuidor
     */
    public function removeDistribuidor(\Presis\DistribuidorBundle\Entity\Distribuidor $distribuidor)
    {
        $this->distribuidores->removeElement($distribuidor);
    }

    /**
     * Get Distribuidores
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getDistribuidores()
    {
        return $this->distribuidores;
    }

    /**
     * Get Distribuidores
     *
     * @return string
     */
    public function __toString()
    {
        return $this->getNombre();
    }
}
