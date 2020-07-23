<?php

namespace Presis\ReclamoBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * TipoReclamo
 *
 * @ORM\Table(name="tiporeclamo")
 * @ORM\Entity
 */
class TipoReclamo
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
     * @ORM\Column(name="nombre", type="string", length=50)
     */
    private $nombre;

    /**
     * @ORM\OneToMany(targetEntity="Presis\ReclamoBundle\Entity\Reclamo", mappedBy="tipo")
     */
    protected $reclamos;

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
     * @return TipoReclamo
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
     * Retorna el nombre, usado para los elementos Select
     *
     * @return string
     */
    public function __toString()
    {
        return $this->getNombre();
    }
}

