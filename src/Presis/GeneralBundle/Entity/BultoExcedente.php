<?php

namespace Presis\GeneralBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Exclude;
use JMS\Serializer\Annotation\VirtualProperty;
use JMS\Serializer\Annotation\SerializedName;
/**
 * BultoExcedente
 *
 * @ORM\Table(name="bulto_excedente")
 * @ORM\Entity
 * @ExclusionPolicy("none")
 *
 */
class BultoExcedente
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
     * @ORM\Column(name="bultoExcedente", type="integer")
     */
    private $bultoExcedente;

    /**
     * @var string
     *
     * @ORM\Column(name="costoBultoExcedente", type="decimal", scale=2)
     */
    private $costoBultoExcedente;

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
     * Set bultoExcedente
     *
     * @param integer $bultoExcedente
     *
     * @return BultoExcedente
     */
    public function setBultoExcedente($bultoExcedente)
    {
        $this->bultoExcedente = $bultoExcedente;

        return $this;
    }

    /**
     * Get bultoExcedente
     *
     * @return integer
     */
    public function getBultoExcedente()
    {
        return $this->bultoExcedente;
    }

    /**
     * Set costoBultoExcedente
     *
     * @param string $costoBultoExcedente
     *
     * @return BultoExcedente
     */
    public function setCostoBultoExcedente($costoBultoExcedente)
    {
        $this->costoBultoExcedente = $costoBultoExcedente;

        return $this;
    }

    /**
     * Get costoBultoExcedente
     *
     * @return string
     */
    public function getCostoBultoExcedente()
    {
        return $this->costoBultoExcedente;
    }

    /**
     * Set cliente
     *
     * @param \Presis\GeneralBundle\Entity\Cliente $cliente
     *
     * @return BultoExcedente
     */
    public function setCliente(\Presis\GeneralBundle\Entity\Cliente $cliente = null)
    {
        $this->cliente = $cliente;

        return $this;
    }

    /**
     * Get cliente
     *
     * @return \Presis\GeneralBundle\Entity\Cliente
     */
    public function getCliente()
    {
        return $this->cliente;
    }

    /**
     * Set cordonEntrega
     *
     * @param \Presis\ServicioBundle\Entity\Cordon $cordonEntrega
     *
     * @return BultoExcedente
     */
    public function setCordonEntrega(\Presis\ServicioBundle\Entity\Cordon $cordonEntrega)
    {
        $this->cordonEntrega = $cordonEntrega;

        return $this;
    }

    /**
     * Get cordonEntrega
     *
     * @return \Presis\ServicioBundle\Entity\Cordon
     */
    public function getCordonEntrega()
    {
        return $this->cordonEntrega;
    }

    /**
     * Set cordonRetiro
     *
     * @param \Presis\ServicioBundle\Entity\Cordon $cordonRetiro
     *
     * @return BultoExcedente
     */
    public function setCordonRetiro(\Presis\ServicioBundle\Entity\Cordon $cordonRetiro)
    {
        $this->cordonRetiro = $cordonRetiro;

        return $this;
    }

    /**
     * Get cordonRetiro
     *
     * @return \Presis\ServicioBundle\Entity\Cordon
     */
    public function getCordonRetiro()
    {
        return $this->cordonRetiro;
    }

    /**
     * Set servicio
     *
     * @param \Presis\ServicioBundle\Entity\Servicio $servicio
     *
     * @return BultoExcedente
     */
    public function setServicio(\Presis\ServicioBundle\Entity\Servicio $servicio = null)
    {
        $this->servicio = $servicio;

        return $this;
    }

    /**
     * Get servicio
     *
     * @return \Presis\ServicioBundle\Entity\Servicio
     */
    public function getServicio()
    {
        return $this->servicio;
    }

    /**
     * @VirtualProperty
     * @SerializedName("cliente_empresa")
     *
     * @return string
     */
    public function getClienteEmpresa()
    {
        return ($this->getCliente())?$this->getCliente()->getEmpresa():"";
    }

    /**
     * @VirtualProperty
     * @SerializedName("serivicio_descripcion")
     *
     * @return string
     */
    public function getServicioDescripcion()
    {
        return ($this->getServicio())?$this->getServicio()->getDescripcion():"";
    }

    /**
     * @VirtualProperty
     * @SerializedName("servicio_cordon_retiro")
     *
     * @return string
     */
    public function getServicioCordonRetiro()
    {
        return ($this->getCordonRetiro())?$this->getCordonRetiro()->getDescripcion():"";
    }

    /**
     * @VirtualProperty
     * @SerializedName("servicio_cordon_entrega")
     *
     * @return string
     */
    public function getServicioCordonEntrega()
    {
        return ($this->getCordonEntrega())?$this->getCordonEntrega()->getDescripcion():"";
    }
}
