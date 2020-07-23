<?php

namespace Presis\RetiroBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Producto
 *
 * @ORM\Table(name="producto")
 * @ORM\Entity
 */
class Producto
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
     *
     * @ORM\Column(name="peso", type="decimal",scale=3,nullable=true)
     */
    private $peso;
    /**
     * @var integer
     *
     * @ORM\Column(name="alto", type="decimal",scale=3,nullable=true)
     */
    private $alto;
    /**
     * @var integer
     *
     * @ORM\Column(name="largo", type="decimal",scale=3,nullable=true)
     */
    private $largo;
    /**
     * @var integer
     *
     * @ORM\Column(name="profundidad", type="decimal",scale=3,nullable=true)
     */
    private $profundidad;
    /**
     * @ORM\ManyToOne(targetEntity="Presis\GeneralBundle\Entity\Categoria")
     * @ORM\JoinColumn(name="categoria_id", referencedColumnName="id",nullable=true)
     **/
    private $categoria;

    /**
     * @var int
     *
     * @ORM\Column(name="forma_carga", type="smallint",nullable=false)
     */
    private $formaCarga;


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
     * Set peso
     *
     * @param decimal $peso
     * @return Producto
     */
    public function setPeso($peso)
    {
        $this->peso = $peso;

        return $this;
    }

    /**
     * Get peso
     *
     * @return decimal
     */
    public function getPeso()
    {
        return $this->peso;
    }

    /**
     * @return mixed
     */
    public function getCategoria()
    {
        return $this->categoria;
    }

    /**
     * @param mixed $categoria
     */
    public function setCategoria($categoria)
    {
        $this->categoria = $categoria;
    }
    /**
     * @ORM\ManyToOne(targetEntity="Retiro",inversedBy="productos",cascade={"remove"})
     * @ORM\JoinColumn(name="retiro_id", referencedColumnName="id")
     **/
    private $retiro;

    /**
     * @return mixed
     */
    public function getRetiro()
    {
        return $this->retiro;
    }

    /**
     * @param mixed $retiro
     */
    public function setRetiro($retiro)
    {
        $this->retiro = $retiro;
    }

    /**
     * @return int
     */
    public function getFormaCarga()
    {
        return $this->formaCarga;
    }

    /**
     * @param int $formaCarga
     */
    public function setFormaCarga($formaCarga)
    {
        $this->formaCarga = $formaCarga;
    }

    /**
     * @return int
     */
    public function getAlto()
    {
        return $this->alto;
    }

    /**
     * @param int $alto
     */
    public function setAlto($alto)
    {
        $this->alto = $alto;
    }

    /**
     * @return int
     */
    public function getLargo()
    {
        return $this->largo;
    }

    /**
     * @param int $largo
     */
    public function setLargo($largo)
    {
        $this->largo = $largo;
    }

    /**
     * @return int
     */
    public function getProfundidad()
    {
        return $this->profundidad;
    }

    /**
     * @param int $profundidad
     */
    public function setProfundidad($profundidad)
    {
        $this->profundidad = $profundidad;
    }


}
