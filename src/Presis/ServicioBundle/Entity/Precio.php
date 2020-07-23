<?php

namespace Presis\ServicioBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Precio
 *
 * @ORM\Table(name="precio")
 * @ORM\Entity(repositoryClass="Presis\ServicioBundle\Entity\PrecioRepository")
 * @UniqueEntity(fields={"rango","servicio","cordonEntrega","cordonRetiro","lista"})
 */
class Precio
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
     * @var integer
     * @ORM\Column(name="rango", type="integer",nullable=false)
     * @Assert\Range(
     *      min = 0,
     *      max = 99999,
     *      minMessage = "El rango debe ser positivo",
     *      maxMessage = "El rango no debe ser mayor a 99999")
     */
    private $rango;

    /**
     * @ORM\ManyToOne(targetEntity="Presis\ServicioBundle\Entity\Servicio", inversedBy="precios")
     * @ORM\JoinColumn(name="servicio_id", referencedColumnName="id",nullable=false)
     */


    protected $servicio;
    /**
     * @ORM\ManyToOne(targetEntity="Presis\ServicioBundle\Entity\Cordon")
     * @ORM\JoinColumn(name="cordonEntrega_id", referencedColumnName="id",nullable=false)
     */

    protected $cordonEntrega;
    /**
     * @ORM\ManyToOne(targetEntity="Presis\ServicioBundle\Entity\Cordon")
     * @ORM\JoinColumn(name="cordonRetiro_id", referencedColumnName="id",nullable=false)
     */

    protected $cordonRetiro;
    /**
     * @var integer
     *
     * @ORM\Column(name="precio", type="decimal",scale=2,nullable=false)

     */
    private $precio = 0;


    /**
     * @ORM\ManyToOne(targetEntity="Presis\ServicioBundle\Entity\Lista", inversedBy="precios")
     * @ORM\JoinColumn(name="lista_id", referencedColumnName="id",nullable=false)
     */
    protected $lista;
    
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
    public function getCordonEntrega()
    {
        return $this->cordonEntrega;
    }

    /**
     * @param mixed $cordonEntrega
     */
    public function setCordonEntrega($cordonEntrega)
    {
        $this->cordonEntrega = $cordonEntrega;
    }

    /**
     * @return mixed
     */
    public function getCordonRetiro()
    {
        return $this->cordonRetiro;
    }

    /**
     * @param mixed $cordonRetiro
     */
    public function setCordonRetiro($cordonRetiro)
    {
        $this->cordonRetiro = $cordonRetiro;
    }

    /**
     * @return mixed
     */
    public function getLista()
    {
        return $this->lista;
    }

    /**
     * @param mixed $lista
     */
    public function setLista($lista)
    {
        $this->lista = $lista;
    }

    /**
     * @return int
     */
    public function getPrecio()
    {
        return $this->precio;
    }

    /**
     * @param int $precio
     */
    public function setPrecio($precio)
    {
        $this->precio = $precio;
    }

    /**
     * @return int
     */
    public function getRango()
    {
        return $this->rango;
    }

    /**
     * @param int $rango
     */
    public function setRango($rango)
    {
        $this->rango = $rango;
    }

    /**
     * @return mixed
     */
    public function getServicio()
    {
        return $this->servicio;
    }

    /**
     * @param mixed $servicio
     */
    public function setServicio($servicio)
    {
        $this->servicio = $servicio;
    }
}
