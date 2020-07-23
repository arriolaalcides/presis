<?php

namespace Presis\MovistarBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Presis\MovistarBundle\Entity\Fabricante;
/**
 * Modelo
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Presis\MovistarBundle\Entity\ModeloRepository")
 */
class Modelo
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
     * @ORM\Column(name="descripcion", type="string", length=45)
     */
    private $descripcion;


    /**
     * @var float
     *
     * @ORM\Column(name="valor_declarado", type="float", nullable=true)
     *
     */
    private $valorDeclarado;


    /**
     * @ORM\ManyToOne(targetEntity="\Presis\MovistarBundle\Entity\Fabricante", inversedBy="modelos")
     * @ORM\JoinColumn(name="fabricante_id", referencedColumnName="id")
     */
    protected $fabricante;

    /**
     * @var boolean
     *
     * @ORM\Column(name="activo", type="boolean",nullable=true)
     */
    private $activo=false;



    /**
     * @return mixed
     */
    public function getFabricante()
    {
        return $this->fabricante;
    }

    /**
     * @param mixed $fabricante
     */
    public function setFabricante($fabricante)
    {
        $this->fabricante = $fabricante;
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
     * Set descripcion
     *
     * @param string $descripcion
     *
     * @return Modelo
     */
    public function setDescripcion($descripcion)
    {
        $this->descripcion = $descripcion;

        return $this;
    }

    /**
     * Get descripcion
     *
     * @return string
     */
    public function getDescripcion()
    {
        return $this->descripcion;
    }

    public function __toString()
    {
        return $this->descripcion;
    }

    /**
     * @return float
     */
    public function getValorDeclarado()
    {
        return $this->valorDeclarado;
    }

    /**
     * @param float $valorDeclarado
     */
    public function setValorDeclarado($valorDeclarado)
    {
        $this->valorDeclarado = $valorDeclarado;
    }

    /**
     * @return boolean
     */
    public function getActivo()
    {
        return $this->activo;
    }

    /**
     * @param boolean $activo
     */
    public function setActivo($activo)
    {
        $this->activo = $activo;
    }


}

