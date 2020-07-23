<?php

namespace Presis\GeneralBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Vendedor
 *
 * @ORM\Table(name="vendedor")
 * @ORM\Entity(repositoryClass="Presis\GeneralBundle\Entity\VendedorRepository")
 */
class Vendedor
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
     * @ORM\Column(name="nombre", type="string")
     */
    private $nombre;
    /**
     * @var string
     *
     * @ORM\Column(name="telefono", type="string")
     */
    private $telefono;
    /**
     * @var string
     *
     * @ORM\Column(name="direccion", type="string")
     */
    private $direccion;
    /**
     * @var integer
     *
     * @ORM\Column(name="cp", type="integer")


     */
    private $cp;


    /**
     * @ORM\OneToMany(targetEntity="Presis\GeneralBundle\Entity\Cliente", mappedBy="vendedor")
     */
    protected $clientes;
    /**
     * @ORM\OneToMany(targetEntity="Presis\ServicioBundle\Entity\Lista", mappedBy="vendedor")
     */
    protected $listas;
    public function __construct() {
        $this->clientes = new ArrayCollection();
        $this->listas=new ArrayCollection();
    }

    /**
     * @return mixed
     */
    public function getClientes()
    {
        return $this->clientes;
    }

    /**
     * @param mixed $clientes
     */
    public function setClientes($clientes)
    {
        $this->clientes = $clientes;
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
     * @return string
     */
    public function getNombre()
    {
        return $this->nombre;
    }

    /**
     * @param string $nombre
     */
    public function setNombre($nombre)
    {
        $this->nombre = $nombre;
    }
    public function __toString(){
        return $this->getNombre();
    }

    /**
     * @return mixed
     */
    public function getListas()
    {
        return $this->listas;
    }

    /**
     * @param mixed $listas
     */
    public function setListas($listas)
    {
        $this->listas = $listas;
    }

    /**
     * @return int
     */
    public function getCp()
    {
        return $this->cp;
    }

    /**
     * @param int $cp
     */
    public function setCp($cp)
    {
        $this->cp = $cp;
    }

    /**
     * @return string
     */
    public function getDireccion()
    {
        return $this->direccion;
    }

    /**
     * @param string $direccion
     */
    public function setDireccion($direccion)
    {
        $this->direccion = $direccion;
    }

    /**
     * @return string
     */
    public function getTelefono()
    {
        return $this->telefono;
    }

    /**
     * @param string $telefono
     */
    public function setTelefono($telefono)
    {
        $this->telefono = $telefono;
    }



    /**
     * Add cliente
     *
     * @param \Presis\GeneralBundle\Entity\Cliente $cliente
     *
     * @return Vendedor
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
     * Add lista
     *
     * @param \Presis\ServicioBundle\Entity\Lista $lista
     *
     * @return Vendedor
     */
    public function addLista(\Presis\ServicioBundle\Entity\Lista $lista)
    {
        $this->listas[] = $lista;

        return $this;
    }

    /**
     * Remove lista
     *
     * @param \Presis\ServicioBundle\Entity\Lista $lista
     */
    public function removeLista(\Presis\ServicioBundle\Entity\Lista $lista)
    {
        $this->listas->removeElement($lista);
    }
}
