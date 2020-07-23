<?php

namespace Presis\GeneralBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Exclude;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * Categoria
 *
 * @ORM\Table(name="categoria")
 * @ORM\Entity(repositoryClass="Presis\GeneralBundle\Entity\CategoriaRepository")
 * @ExclusionPolicy("none")
 * @UniqueEntity("nombre")
 */



class Categoria
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
     * @ORM\Column(name="nombre", type="string", length=255)
     */
    private $nombre;
    /**
     * @var float
     *
     * @ORM\Column(name="peso", type="decimal", scale=3)
     */
    private $peso;

    /**
    * @ORM\ManyToMany(targetEntity="Presis\GeneralBundle\Entity\Cliente", mappedBy="categorias")
    * @Exclude()
    **/
    private $clientes;
    public function __construct()
    {

        $this->clientes=new ArrayCollection();
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

    /**
     * @return decimal
     */
    public function getPeso()
    {
        return $this->peso;
    }

    /**
     * @param decimal $peso
     */
    public function setPeso($peso)
    {
        $this->peso = $peso;
    }
    public function __toString(){
        return (string)$this->getNombre();
    }




    /**
     * Add cliente
     *
     * @param \Presis\GeneralBundle\Entity\Cliente $cliente
     *
     * @return Categoria
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
}
