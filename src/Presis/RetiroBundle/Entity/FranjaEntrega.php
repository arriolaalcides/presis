<?php

namespace Presis\RetiroBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * FranjaEntrega
 *
 * @ORM\Table(name="franjaentrega")
 * @ORM\Entity(repositoryClass="Presis\RetiroBundle\Entity\FranjaEntregaRepository")
 */
class FranjaEntrega
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
     * @ORM\Column(name="descripcion", type="string", length=255)
     */
    private $descripcion;
    /**
     * @var string
     *
     * @ORM\Column(name="codigo", type="string", length=45)
     */
    private $codigo;

    /**
     * @return string
     */
    public function getCodigo()
    {
        return $this->codigo;
    }

    /**
     * @param string $codigo
     */
    public function setCodigo($codigo)
    {
        $this->codigo = $codigo;
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
     * @return FranjaEntrega
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
    public function __toString(){
        return $this->getDescripcion();
    }
}
