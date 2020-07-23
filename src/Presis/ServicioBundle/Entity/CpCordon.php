<?php

namespace Presis\ServicioBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * CpCordon
 *
 * @ORM\Table(name="cpcordon")
 * @ORM\Entity(repositoryClass="Presis\ServicioBundle\Entity\CpCordonRepository")
 * @UniqueEntity("cp")
 */
class CpCordon
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
     * @ORM\ManyToOne(targetEntity="Presis\ServicioBundle\Entity\Cordon")
     * @ORM\JoinColumn(name="cordon_id", referencedColumnName="id",nullable=false)
     * @Assert\NotBlank()
     **/
    private $cordon;

    /**
     * @var integer
     *
     * @ORM\Column(name="tiempo_entrega", type="smallint", nullable=true)
     */
    private $tiempoDeEntrega;

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
     * Set cp
     *
     * @param integer $cp
     * @return CpCordon
     */
    public function setCp($cp)
    {
        $this->cp = $cp;

        return $this;
    }

    /**
     * Get cp
     *
     * @return integer 
     */
    public function getCp()
    {
        return $this->cp;
    }

    /**
     * Set partido
     *
     * @param string $partido
     * @return CpCordon
     */
    public function setPartido($partido)
    {
        $this->partido = $partido;

        return $this;
    }

    /**
     * Get partido
     *
     * @return string 
     */
    public function getPartido()
    {
        return $this->partido;
    }

    /**
     * Set localidad
     *
     * @param string $localidad
     * @return CpCordon
     */
    public function setLocalidad($localidad)
    {
        $this->localidad = $localidad;

        return $this;
    }

    /**
     * Get localidad
     *
     * @return string 
     */
    public function getLocalidad()
    {
        return $this->localidad;
    }

    /**
     * Set barrio
     *
     * @param string $barrio
     * @return CpCordon
     */
    public function setBarrio($barrio)
    {
        $this->barrio = $barrio;

        return $this;
    }

    /**
     * Get barrio
     *
     * @return string 
     */
    public function getBarrio()
    {
        return $this->barrio;
    }

    /**
     * Set zona
     *
     * @param string $zona
     * @return CpCordon
     */
    public function setZona($zona)
    {
        $this->zona = $zona;

        return $this;
    }

    /**
     * Get zona
     *
     * @return string 
     */
    public function getZona()
    {
        return $this->zona;
    }

    /**
     * Set subzona
     *
     * @param string $subzona
     * @return CpCordon
     */
    public function setSubzona($subzona)
    {
        $this->subzona = $subzona;

        return $this;
    }

    /**
     * Get subzona
     *
     * @return string 
     */
    public function getSubzona()
    {
        return $this->subzona;
    }

    /**
     * Set provincia
     *
     * @param string $provincia
     * @return CpCordon
     */
    public function setProvincia($provincia)
    {
        $this->provincia = $provincia;

        return $this;
    }

    /**
     * Get provincia
     *
     * @return string 
     */
    public function getProvincia()
    {
        return $this->provincia;
    }

    /**
     * Set prestador
     *
     * @param string $prestador
     * @return CpCordon
     */
    public function setPrestador($prestador)
    {
        $this->prestador = $prestador;

        return $this;
    }

    /**
     * Get prestador
     *
     * @return string 
     */
    public function getPrestador()
    {
        return $this->prestador;
    }

    /**
     * @return mixed
     */
    public function getCordon()
    {
        return $this->cordon;
    }

    /**
     * @param mixed $cordon
     */
    public function setCordon($cordon)
    {
        $this->cordon = $cordon;
    }

    function __toString()
    {
        return $this->zona."";
    }



    /**
     * Set tiempoDeEntrega
     *
     * @param integer $tiempoDeEntrega
     *
     * @return CpCordon
     */
    public function setTiempoDeEntrega($tiempoDeEntrega)
    {
        $this->tiempoDeEntrega = $tiempoDeEntrega;

        return $this;
    }

    /**
     * Get tiempoDeEntrega
     *
     * @return integer
     */
    public function getTiempoDeEntrega()
    {
        return $this->tiempoDeEntrega;
    }

    /**
     * @var string
     *
     * @ORM\Column(name="kms", type="decimal", scale=2, nullable=false)

     */
    private $kms = 0;

    /**
     * @var string
     *
     * @ORM\Column(name="kmsAdicionales", type="decimal", scale=2, nullable=false)

     */
    private $kmsAdicionales = 0;

    /**
     * @return int
     */
    public function getKms()
    {
        return $this->kms;
    }

    /**
     * @param int $kms
     */
    public function setKms($kms)
    {
        $this->kms = $kms;
    }

    /**
     * @return int
     */
    public function getKmsAdicionales()
    {
        return $this->kmsAdicionales;
    }

    /**
     * @param int $kmsAdicionales
     */
    public function setKmsAdicionales($kmsAdicionales)
    {
        $this->kmsAdicionales = $kmsAdicionales;
    }

    /**
     * @var string
     *
     * @ORM\Column(name="tipoServicio", type="string", length=255,nullable=true)
     */
    private $tipoServicio;

    /**
     * @return string
     */
    public function getTipoServicio()
    {
        return $this->tipoServicio;
    }

    /**
     * @param string $tipoServicio
     */
    public function setTipoServicio($tipoServicio)
    {
        $this->tipoServicio = $tipoServicio;
    }




}
