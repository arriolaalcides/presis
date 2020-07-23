<?php

namespace Presis\ServicioBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Presis\ServicioBundle\Validator\Constraint\CheckGeneralList;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Presis\ServicioBundle\Validator\Constraint\CheckGeneralList as CheckGeneralAssert;

/**
 * Lista
 *
 * @ORM\Table(name="lista")
 * @ORM\Entity(repositoryClass="Presis\ServicioBundle\Entity\ListaRepository")
 * @UniqueEntity("descripcion")
 * @CheckGeneralAssert

 */
class Lista
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
     * @ORM\Column(name="descripcion", type="string",nullable=false)
     *
     */
    private $descripcion;

    /**
     * @ORM\OneToMany(targetEntity="Presis\ServicioBundle\Entity\Precio", mappedBy="lista",cascade={"remove"})
     */
    protected $precios;
    /**
     * @var boolean
     *
     * @ORM\Column(name="is_general", type="boolean")

     */
    private $isGeneral;
    /**
     * @ORM\ManyToOne(targetEntity="Presis\GeneralBundle\Entity\Vendedor", inversedBy="listas")
     * @ORM\JoinColumn(name="vendedor_id", referencedColumnName="id",nullable=true)
     */

    protected $vendedor;
    /**
     * @ORM\OneToOne(targetEntity="Presis\GeneralBundle\Entity\Cliente",inversedBy="lista")
     * @ORM\JoinColumn(name="cliente_id", referencedColumnName="id",nullable=true)
     */
    private $cliente;

    public function __construct() {
        $this->precios = new ArrayCollection();
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
     * @return string
     */
    public function getDescripcion()
    {
        if (isset($this->descripcion)) {
            return $this->descripcion;
        }else{
            return "";
        }



    }

    /**
     * @param string $descripcion
     */
    public function setDescripcion($descripcion)
    {
        $this->descripcion = $descripcion;
    }

    /**
     * @return boolean
     */
    public function getIsGeneral()
    {
        return $this->isGeneral;
    }

    /**
     * @param boolean $isGeneral
     */
    public function setIsGeneral($isGeneral)
    {
        $this->isGeneral = $isGeneral;
    }

    /**
     * @return mixed
     */
    public function getPrecios()
    {
        return $this->precios;
    }

    /**
     * @param mixed $precios
     */
    public function setPrecios($precios)
    {
        $this->precios = $precios;
    }
    /**
     * @return string
     */
    public function __toString(){
        return (string)$this->getDescripcion();
    }

    /**
     * @return mixed
     */
    public function getVendedor()
    {
        return $this->vendedor;
    }

    /**
     * @param mixed $vendedor
     */
    public function setVendedor($vendedor)
    {
        $this->vendedor = $vendedor;
    }



    /**
     * Add precio
     *
     * @param \Presis\ServicioBundle\Entity\Precio $precio
     *
     * @return Lista
     */
    public function addPrecio(\Presis\ServicioBundle\Entity\Precio $precio)
    {
        $this->precios[] = $precio;

        return $this;
    }

    /**
     * Remove precio
     *
     * @param \Presis\ServicioBundle\Entity\Precio $precio
     */
    public function removePrecio(\Presis\ServicioBundle\Entity\Precio $precio)
    {
        $this->precios->removeElement($precio);
    }
}
