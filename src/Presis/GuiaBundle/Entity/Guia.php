<?php

namespace Presis\GuiaBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Guia
 *
 * @ORM\Table(name="guia")
 * @ORM\Entity(repositoryClass="Presis\GuiaBundle\Entity\GuiaRepository")
 */
class Guia
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
     * @ORM\Column(name="fechahora", type="datetime")
     */
    private $fechahora;

    // ...

    /**
     * @ORM\ManyToMany(targetEntity="Presis\RetiroBundle\Entity\Retiro")
     * @ORM\JoinTable(name="guias_retiros",
     *      joinColumns={@ORM\JoinColumn(name="guia_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="retiro_id", referencedColumnName="id")}
     *      )
     **/
    private $retiros;

    /**
     * @ORM\ManyToOne(targetEntity="Presis\GeneralBundle\Entity\Cliente")
     * @ORM\JoinColumn(name="cliente_id", referencedColumnName="id")
     **/
    private $cliente;

    public function __construct() {
        $this->retiros = new ArrayCollection();
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
     * Set fechahora
     *
     * @param \DateTime $fechahora
     * @return Guia
     */
    public function setFechahora($fechahora)
    {
        $this->fechahora = $fechahora;

        return $this;
    }

    /**
     * Get fechahora
     *
     * @return \DateTime 
     */
    public function getFechahora()
    {
        return $this->fechahora;
    }

    /**
     * @return mixed
     */
    public function getCliente()
    {
        return $this->cliente;
    }

    /**
     * @param mixed $cliente
     */
    public function setCliente($cliente)
    {
        $this->cliente = $cliente;
    }

    /**
     * @return mixed
     */
    public function getRetiros()
    {
        return $this->retiros;
    }

    /**
     * @param mixed $retiros
     */
    public function setRetiros($retiros)
    {
        $this->retiros = $retiros;
    }


    /**
     * Add retiro
     *
     * @param \Presis\RetiroBundle\Entity\Retiro $retiro
     *
     * @return Guia
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
}
