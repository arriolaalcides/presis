<?php

namespace Presis\RemitentesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;
use JMS\Serializer\Annotation\VirtualProperty;
use JMS\Serializer\Annotation\SerializedName;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
/**
 * Remitente
 *
 * @ORM\Table(name="remitente")
 * @ORM\Entity(repositoryClass="Presis\RemitentesBundle\Entity\RemitenteRepository")
 * @ExclusionPolicy("all")
 * @UniqueEntity("codigo")
 */
class Remitente
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @Expose
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="codigo", type="string", length=20, nullable=true)
     * @Expose
     */
    private $codigo;

    /**
     * @var string
     *
     * @ORM\Column(name="empresa", type="string", length=100, nullable=true)
     * @Expose
     */
    private $empresa;

    /**
     * @var string
     *
     * @ORM\Column(name="remitente", type="string", length=100, nullable=true)
     * @Expose
     */
    private $remitente;

    /**
     * @var string
     *
     * @ORM\Column(name="calle", type="string", length=100, nullable=true)
     * @Expose
     */
    private $calle;

    /**
     * @var integer
     *
     * @ORM\Column(name="altura", type="integer", nullable=true)
     * @Expose
     */
    private $altura;

    /**
     * @var string
     *
     * @ORM\Column(name="piso", type="string", length=10, nullable=true)
     * @Expose
     */
    private $piso;

    /**
     * @var string
     *
     * @ORM\Column(name="dpto", type="string", length=10, nullable=true)
     * @Expose
     */
    private $dpto;

    /**
     * @var string
     *
     * @ORM\Column(name="localidad", type="string", length=45, nullable=true)
     * @Expose
     */
    private $localidad;

    /**
     * @var string
     *
     * @ORM\Column(name="provincia", type="string", length=45, nullable=true)
     * @Expose
     */
    private $provincia;

    /**
     * @var string
     *
     * @ORM\Column(name="celular", type="string", length=20, nullable=true)
     * @Expose
     */
    private $celular;

    /**
     * @var string
     *
     * @ORM\Column(name="cp", type="string", length=10, nullable=true)
     * @Expose
     */
    private $cp;
    
    /**
     * @var string
     *
     * @ORM\Column(name="otherInfor", type="string", length=255, nullable=true)
     * @Expose
     */
    private $otherInfo;

    /**
     * @var string
     *
     * @ORM\Column(name="mail", type="string", length=500, nullable=true)
     * @Expose
     */
    private $mail;

    /**
     * @return string
     */
    public function getMail()
    {
        return $this->mail;
    }

    /**
     * @param string $mail
     */
    public function setMail($mail)
    {
        $this->mail = $mail;
    }



    /**
     * Get otherInfo
     *
     * @return string
     */
    public function getOtherInfo()
    {
        return $this->otherInfo;
    }
        
    /**
     * Set otherInfo
     *
     * @param string $otherInfo
     *
     * @return Remitente
     */
    public function setOtherInfo($otherInfo)
    {
        $this->otherInfo = $otherInfo;

        return $this;
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
     * Set codigo
     *
     * @param string $codigo
     *
     * @return Remitente
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
     * Set empresa
     *
     * @param string $empresa
     *
     * @return Remitente
     */
    public function setEmpresa($empresa)
    {
        $this->empresa = $empresa;

        return $this;
    }

    /**
     * Get empresa
     *
     * @return string
     */
    public function getEmpresa()
    {
        return $this->empresa;
    }

    /**
     * Set remitente
     *
     * @param string $remitente
     *
     * @return Remitente
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
        if($this->remitente){
            return $this->remitente;
        }else {
            return "";
        }
    }

    /**
     * Set calle
     *
     * @param string $calle
     *
     * @return Remitente
     */
    public function setCalle($calle)
    {
        $this->calle = $calle;

        return $this;
    }

    /**
     * Get calle
     *
     * @return string
     */
    public function getCalle()
    {
        return $this->calle;
    }

    /**
     * Set altura
     *
     * @param integer $altura
     *
     * @return Remitente
     */
    public function setAltura($altura)
    {
        $this->altura = $altura;

        return $this;
    }

    /**
     * Get altura
     *
     * @return integer
     */
    public function getAltura()
    {
        return $this->altura;
    }

    /**
     * Set piso
     *
     * @param string $piso
     *
     * @return Remitente
     */
    public function setPiso($piso)
    {
        $this->piso = $piso;

        return $this;
    }

    /**
     * Get piso
     *
     * @return string
     */
    public function getPiso()
    {
        return $this->piso;
    }

    /**
     * Set dpto
     *
     * @param string $dpto
     *
     * @return Remitente
     */
    public function setDpto($dpto)
    {
        $this->dpto = $dpto;

        return $this;
    }

    /**
     * Get dpto
     *
     * @return string
     */
    public function getDpto()
    {
        return $this->dpto;
    }

    /**
     * Set localidad
     *
     * @param string $localidad
     *
     * @return Remitente
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
     * @return Remitente
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
     * Set cp
     *
     * @param string $cp
     *
     * @return Remitente
     */
    public function setCp($cp)
    {
        $this->cp = $cp;

        return $this;
    }

    /**
     * Get cp
     *
     * @return string
     */
    public function getCp()
    {
        return $this->cp;
    }
    
    public function __toString()
    {
        return $this->getRemitente();
    }
    
    
    /**
     * @ORM\ManyToOne(targetEntity="Presis\GeneralBundle\Entity\Cliente",cascade={"persist"})
     * @ORM\JoinColumn(name="cliente_id", referencedColumnName="id",nullable=true,onDelete="cascade")
     * @Expose
     **/
    private $cliente;

    /**
     * @return mixed
     */
    public function getCliente()
    {
        return $this->cliente;
    }

    /**
     * @param mixed $cliente
     */
    public function setCliente($cliente)
    {
        $this->cliente = $cliente;
    }
    


    /**
     * Set celular
     *
     * @param string $celular
     *
     * @return Remitente
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

    //20-01-17 - PICCINI AGREGO PARA FILTRAR REMITENTES X USUARIO Y NO POR CLIENTE
    /**
     * @var string
     *
     * @ORM\Column(name="user", type="string", length=100, nullable=true)
     * @Expose
     */
    private $user;

    /**
     * @return string
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param string $user
     */
    public function setUser($user)
    {
        $this->user = $user;
    }
}
