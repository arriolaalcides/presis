<?php

namespace Presis\RetiroBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Motivo
 *
 * @ORM\Table(name="motivo")
 * @ORM\Entity(repositoryClass="Presis\RetiroBundle\Entity\MotivoRepository")
 */
class Motivo
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
     * @ORM\Column(name="name", type="string", length=50)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="codigo", type="string", length=5)
     */
    private $codigo;

    /**
     * @ORM\ManyToOne(targetEntity="Presis\EstadoBundle\Entity\Estado", inversedBy="motivos")
     * @ORM\JoinColumn(name="estado_id", referencedColumnName="id")
     */
    protected $estado_siguiente;
    /**
     * @ORM\OneToMany(targetEntity="Presis\RetiroBundle\Entity\Retiro", mappedBy="motivo")
     */
    protected $retiros;

    /**
     * @ORM\OneToMany(targetEntity="Presis\TrackerBundle\Entity\Tracker", mappedBy="motivo")
     */
    protected $historicos;

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
     * Set name
     *
     * @param string $name
     *
     * @return Motivo
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set codigo
     *
     * @param string $codigo
     *
     * @return Motivo
     */
    public function setCodigo($codigo)
    {
        $this->codigo = $codigo;

        return $this;
    }

    /**
     * Get codigo
     *
     * @return string
     */
    public function getCodigo()
    {
        return $this->codigo;
    }

    /**
     * Set estado
     *
     * @param \stdClass $estado
     *
     * @return Motivo
     */
    public function setEstado($estado)
    {
        $this->estado = $estado;

        return $this;
    }

    /**
     * Get estado
     *
     * @return \stdClass
     */
    public function getEstado()
    {
        return $this->estado;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->retiros = new \Doctrine\Common\Collections\ArrayCollection();
        $this->historicos = new \Doctrine\Common\Collections\ArrayCollection();
    }

    public function __toString()
    {
        return $this->name;
    }

    /**
     * Set estadoSiguiente
     *
     * @param \Presis\EstadoBundle\Entity\Estado $estadoSiguiente
     *
     * @return Motivo
     */
    public function setEstadoSiguiente(\Presis\EstadoBundle\Entity\Estado $estadoSiguiente = null)
    {
        $this->estado_siguiente = $estadoSiguiente;

        return $this;
    }

    /**
     * Get estadoSiguiente
     *
     * @return \Presis\EstadoBundle\Entity\Estado
     */
    public function getEstadoSiguiente()
    {
        return $this->estado_siguiente;
    }

    /**
     * Add retiro
     *
     * @param \Presis\RetiroBundle\Entity\Retiro $retiro
     *
     * @return Motivo
     */
    public function addRetiro(\Presis\RetiroBundle\Entity\Retiro $retiro)
    {
        $this->retiros[] = $retiro;

        return $this;
    }

    /**
     * Remove retiro
     *
     * @param \Presis\RetiroBundle\Entity\Retiro $retiro
     */
    public function removeRetiro(\Presis\RetiroBundle\Entity\Retiro $retiro)
    {
        $this->retiros->removeElement($retiro);
    }

    /**
     * Get retiros
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getRetiros()
    {
        return $this->retiros;
    }

    /**
     * Add historico
     *
     * @param \Presis\TrackerBundle\Entity\Tracker $historico
     *
     * @return Motivo
     */
    public function addHistorico(\Presis\TrackerBundle\Entity\Tracker $historico)
    {
        $this->historicos[] = $historico;

        return $this;
    }

    /**
     * Remove historico
     *
     * @param \Presis\TrackerBundle\Entity\Tracker $historico
     */
    public function removeHistorico(\Presis\TrackerBundle\Entity\Tracker $historico)
    {
        $this->historicos->removeElement($historico);
    }

    /**
     * Get historicos
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getHistoricos()
    {
        return $this->historicos;
    }
}
