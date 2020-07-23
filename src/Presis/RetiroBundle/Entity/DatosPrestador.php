<?php

namespace Presis\RetiroBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * DatosPrestador
 *
 * @ORM\Table(name="datosprestador")
 * @ORM\Entity(repositoryClass="Presis\RetiroBundle\Entity\DatosPrestadorRepository")
 */
class DatosPrestador
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
     * @ORM\Column(name="cp", type="smallint")
     */
    private $cp;

    /**
     * @var string
     *
     * @ORM\Column(name="partido", type="string", length=255,nullable=true)
     */
    private $partido;

    /**
     * @var string
     *
     * @ORM\Column(name="localidad", type="string", length=255,nullable=true)
     */
    private $localidad;

    /**
     * @var string
     *
     * @ORM\Column(name="barrio", type="string", length=255,nullable=true)
     */
    private $barrio;

    /**
     * @var string
     *
     * @ORM\Column(name="zona", type="string", length=255,nullable=true)
     */
    private $zona;

    /**
     * @var string
     *
     * @ORM\Column(name="subzona", type="string", length=255,nullable=true)
     */
    private $subzona;

    /**
     * @var string
     *
     * @ORM\Column(name="provincia", type="string", length=255,nullable=true)
     */
    private $provincia;

    /**
     * @var string
     *
     * @ORM\Column(name="prestador", type="string", length=255,nullable=true)
     */
    private $prestador;
    /**
     * @var string
     *
     * @ORM\Column(name="cordon", type="string", length=255,nullable=true)
     */
    private $cordon;


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
    public function getBarrio()
    {
        return $this->barrio;
    }

    /**
     * @param string $barrio
     */
    public function setBarrio($barrio)
    {
        $this->barrio = $barrio;
    }

    /**
     * @return string
     */
    public function getCordon()
    {
        return $this->cordon;
    }

    /**
     * @param string $cordon
     */
    public function setCordon($cordon)
    {
        $this->cordon = $cordon;
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
    public function getLocalidad()
    {
        return $this->localidad;
    }

    /**
     * @param string $localidad
     */
    public function setLocalidad($localidad)
    {
        $this->localidad = $localidad;
    }

    /**
     * @return string
     */
    public function getPartido()
    {
        return $this->partido;
    }

    /**
     * @param string $partido
     */
    public function setPartido($partido)
    {
        $this->partido = $partido;
    }

    /**
     * @return string
     */
    public function getPrestador()
    {
        return $this->prestador;
    }

    /**
     * @param string $prestador
     */
    public function setPrestador($prestador)
    {
        $this->prestador = $prestador;
    }

    /**
     * @return string
     */
    public function getProvincia()
    {
        return $this->provincia;
    }

    /**
     * @param string $provincia
     */
    public function setProvincia($provincia)
    {
        $this->provincia = $provincia;
    }

    /**
     * @return string
     */
    public function getSubzona()
    {
        return $this->subzona;
    }

    /**
     * @param string $subzona
     */
    public function setSubzona($subzona)
    {
        $this->subzona = $subzona;
    }

    /**
     * @return string
     */
    public function getZona()
    {
        return $this->zona;
    }

    /**
     * @param string $zona
     */
    public function setZona($zona)
    {
        $this->zona = $zona;
    }

}
