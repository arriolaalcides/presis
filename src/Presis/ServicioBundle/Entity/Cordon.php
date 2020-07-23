<?php

namespace Presis\ServicioBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;




/**
 * Cordon
 *
 * @ORM\Table(name="cordon")
 * @ORM\Entity(repositoryClass="Presis\ServicioBundle\Entity\CordonRepository")
 * @UniqueEntity("descripcion")
 */
class Cordon
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;


    /**
     * @var string
     *
     * @ORM\Column(name="descripcion", type="string")
     *
     */
    private $descripcion;

    /**
     * @return string
     */
    public function getDescripcion()
    {
        return $this->descripcion;
    }

    /**
     * @param string $descripcion
     */
    public function setDescripcion($descripcion)
    {
        $this->descripcion = $descripcion;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    public function __toString(){
        return $this->getDescripcion();
    }

}
