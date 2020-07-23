<?php

namespace Presis\RetiroBundle\Entity;

use Doctrine\ORM\Mapping as ORM;


/**
 * Sender
 *
 * @ORM\Table(name="sender")
 * @ORM\Entity(repositoryClass="Presis\RetiroBundle\Entity\SenderRepository")
 */
class Sender
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
     * @ORM\Column(name="empresa",length=254, type="string", nullable=true)

     */
    private $empresa;
    /**
     * @var string
     *
     * @ORM\Column(name="calle", type="string", length=254, nullable=true)
     */
    private $calle;

    /**
     * @var string
     *
     * @ORM\Column(name="altura", type="string", length=10, nullable=true)
     */
    private $altura;

    /**
     * @var string
     *
     * @ORM\Column(name="piso", type="string", length=10, nullable=true)
     */
    private $piso;

    /**
     * @var string
     *
     * @ORM\Column(name="dpto", type="string", length=10, nullable=true)
     */
    private $dpto;

    /**
     * @var string
     *
     * @ORM\Column(name="celular", type="string", length=20, nullable=true)
     */
    private $celular;

    /**
     * @var string
     *
     * @ORM\Column(name="other_info", type="text",length=254,nullable=true)
     */
    private $otherInfo;
    /**
     * @var string
     *
     * @ORM\Column(name="cp", type="string",length=15,nullable=true)
     */
    private $cp;
    
    /**
     * @var string
     *
     * @ORM\Column(name="codigo", type="string", length=25, nullable=true)
     */
    private $codigo;

    /**
     * @var string
     *
     * @ORM\Column(name="remitente", type="string", length=45, nullable=true)
     */
    private $remitente;

    /**
     * @var string
     *
     * @ORM\Column(name="localidad", type="string", length=45, nullable=true)
     */
    private $localidad;

    /**
     * @var string
     *
     * @ORM\Column(name="provincia", type="string", length=45, nullable=true)
     */
    private $provincia;

    /**
     * @return string
     */
    public function getCp()
    {
        return $this->cp;
    }

    /**
     * @param string $cp
     */
    public function setCp($cp)
    {
        $this->cp = $cp;
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
     * @return string
     */
    public function getAltura()
    {
        return $this->altura;
    }

    /**
     * @param string $altura
     */
    public function setAltura($altura)
    {
        $this->altura = $altura;
    }

    /**
     * @return string
     */
    public function getCalle()
    {
        return $this->calle;
    }

    /**
     * @param string $calle
     */
    public function setCalle($calle)
    {
        $this->calle = $calle;
    }

    /**
     * @return string
     */
    public function getDpto()
    {
        return $this->dpto;
    }

    /**
     * @param string $dpto
     */
    public function setDpto($dpto)
    {
        $this->dpto = $dpto;
    }

    public function getDireccion()
    {
        return $this->getCalle()." ".$this->getAltura()." ".$this->getPiso()." ".$this->getDpto();
    }

    /**
     * @return string
     */
    public function getEmpresa()
    {
        return $this->empresa;
    }

    /**
     * @param string $empresa
     */
    public function setEmpresa($empresa)
    {
        $this->empresa = $empresa;
    }

    /**
     * @return string
     */
    public function getOtherInfo()
    {
        return $this->otherInfo;
    }

    /**
     * @param string $otherInfo
     */
    public function setOtherInfo($otherInfo)
    {
        $this->otherInfo = $otherInfo;
    }

    /**
     * @return string
     */
    public function getPiso()
    {
        return $this->piso;
    }

    /**
     * @param string $piso
     */
    public function setPiso($piso)
    {
        $this->piso = $piso;
    }
    
    /**
     * Set codigo
     *
     * @param string $codigo
     *
     * @return Sender
     */
    public function setCodigo($codigo)
    {
        $this->codigo = $codigo;

        return $this;
    }

    /**
     * Get codigo
     *
     * @return string
     */
    public function getCodigo()
    {
        return $this->codigo;
    }

    /**
     * Set remitente
     *
     * @param string $remitente
     *
     * @return Sender
     */
    public function setRemitente($remitente)
    {
        $this->remitente = $remitente;

        return $this;
    }

    /**
     * Get remitente
     *
     * @return string
     */
    public function getRemitente()
    {
        return $this->remitente;
    }

    /**
     * Set localidad
     *
     * @param string $localidad
     *
     * @return Sender
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
     * Set provincia
     *
     * @param string $provincia
     *
     * @return Sender
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
    
    public function __toString() {
        return (string)$this->getRemitente();
    }


    /**
     * Set celular
     *
     * @param string $celular
     *
     * @return Sender
     */
    public function setCelular($celular)
    {
        $this->celular = $celular;

        return $this;
    }

    /**
     * Get celular
     *
     * @return string
     */
    public function getCelular()
    {
        return $this->celular;
    }

    /**
     * @var string
     *
     * @ORM\Column(name="email",length=500, type="string", nullable=true)
     */
    private $email;

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param string $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }


}
