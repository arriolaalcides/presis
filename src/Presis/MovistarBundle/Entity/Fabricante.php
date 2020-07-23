<?php

namespace Presis\MovistarBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Presis\MovistarBundle\Entity\Modelo;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;
use JMS\Serializer\Annotation\VirtualProperty;
use JMS\Serializer\Annotation\SerializedName;
/**
 * Fabricante
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Presis\MovistarBundle\Entity\FabricanteRepository")
 */
class Fabricante
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
     * @ORM\Column(name="descricion", type="string", length=30)
     */
    private $descricion;

    /**
     * @ORM\OneToMany(targetEntity="\Presis\MovistarBundle\Entity\Modelo", mappedBy="fabricante", cascade={"remove"})
     */
    protected $modelos;

    public function __construct()
    {
        $this->modelos = new ArrayCollection();
    }


    /**
     * @return mixed
     */
    public function getModelos()
    {
        return $this->modelos;
    }

    /**
     * @param mixed $modelos
     */
    public function setModelos($modelos)
    {
        $this->modelos = $modelos;
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
     * Set descricion
     *
     * @param string $descricion
     *
     * @return Fabricante
     */
    public function setDescricion($descricion)
    {
        $this->descricion = $descricion;

        return $this;
    }

    /**
     * Get descricion
     *
     * @return string
     */
    public function getDescricion()
    {
        return $this->descricion;
    }

    public function __toString()
    {
        return $this->descricion;
    }

    /**
     * Add modelo
     *
     * @param \Presis\MovistarBundle\Entity\Modelo
     *
     * @return Fabricante
     */
    public function addModelo(\Presis\MovistarBundle\Entity\Modelo $modelo)
    {
        $this->modelos[] = $modelo;

        return $this;
    }

    /**
     * Remove modelo
     *
     * @param \Presis\MovistarBundle\Entity\Modelo
     */
    public function removeSucursale(\Presis\MovistarBundle\Entity\Modelo $modelo)
    {
        $this->modelos->removeElement($modelo);
    }



}

