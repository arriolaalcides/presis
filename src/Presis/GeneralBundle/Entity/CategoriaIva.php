<?php

namespace Presis\GeneralBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CategoriaIva
 *
 * @ORM\Table(name="categoria_iva")
 * @ORM\Entity
 */
class CategoriaIva
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
     * @ORM\Column(name="nombre", type="string", length=20)
     */
    private $nombre;

    /**
     * @ORM\OneToMany(targetEntity="Presis\GeneralBundle\Entity\Cliente", mappedBy="categoriaIva")
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
     * @return CategoriaIva
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
        $this->clientes = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add cliente
     *
     * @param \Presis\GeneralBundle\Entity\Cliente $cliente
     *
     * @return CategoriaIva
     */
    public function addCliente(\Presis\GeneralBundle\Entity\Cliente $cliente)
    {
        $this->clientes[] = $cliente;

        return $this;
    }

    /**
     * Remove cliente
     *
     * @param \Presis\GeneralBundle\Entity\Cliente $cliente
     */
    public function removeCliente(\Presis\GeneralBundle\Entity\Cliente $cliente)
    {
        $this->clientes->removeElement($cliente);
    }

    /**
     * Get clientes
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getClientes()
    {
        return $this->clientes;
    }

    function __toString()
    {
        return $this->nombre;
    }


}
