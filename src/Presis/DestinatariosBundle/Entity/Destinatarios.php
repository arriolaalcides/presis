<?php

namespace Presis\DestinatariosBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Presis\DatosEnviosBundle\DatosEnviosBundle;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;
use JMS\Serializer\Annotation\VirtualProperty;
use JMS\Serializer\Annotation\SerializedName;
//use Symfony\Component\Translation\Tests\String;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
/**
 * Destinatarios
 *
 * @ORM\Table(name="destinatarios")
 * @ORM\Entity(repositoryClass="Presis\DestinatariosBundle\Entity\DestinatariosRepository")
 * @ExclusionPolicy("all")
 * @UniqueEntity("codigo")
 */
class Destinatarios
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
     * @ORM\Column(name="destinatario", type="string", length=100, nullable=true)
     * @Expose
     */
    private $destinatario;

    /**
     * @var string
     *
     * @ORM\Column(name="cuit", type="string", length=15, nullable=true)
     * @Expose
     */
    private $cuit;
    
    /**
     * @var string
     *
     * @ORM\Column(name="apellido_nombre", type="string", length=100, nullable=true)
     * @Expose
     */
    private $apellidoNombre;
    
    /**
     * @var string
     *
     * @ORM\Column(name="mail", type="string", length=500, nullable=true)
     * @Expose
     */
    private $mail;
    
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
     * @return Destinatarios
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
     * @return Destinatarios
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
     * @return Destinatarios
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
     * Set destinatario
     *
     * @param string $destinatario
     *
     * @return Destinatarios
     */
    public function setDestinatario($destinatario)
    {
        $this->destinatario = $destinatario;

        return $this;
    }

    /**
     * Get destinatario
     *
     * @return string
     */
    public function getDestinatario()
    {
        return $this->destinatario;
    }

    /**
     * Set calle
     *
     * @param string $calle
     *
     * @return Destinatarios
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
     * @return Destinatarios
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
     * @return Destinatarios
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
     * @return Destinatarios
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
     * @return Destinatarios
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
     * @return Destinatarios
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
     * @return Destinatarios
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
    
    /**
     * Set apellidoNombre
     *
     * @param string $empresa
     *
     * @return Destinatarios
     */
    public function setApellidoNombre($apellidoNombre)
    {
        $this->apellidoNombre = $apellidoNombre;

        return $this;
    }

    /**
     * Get apellidoNombre
     *
     * @return string
     */
    public function getApellidoNombre()
    {
        return $this->apellidoNombre;
    }
    
    /**
     * Set celular
     *
     * @param string $celular
     *
     * @return Destinatarios
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
     * Set mail
     *
     * @param string $mail
     *
     * @return Destinatarios
     */
    public function setMail($mail)
    {
        $this->mail = $mail;

        return $this;
    }

    /**
     * Get mail
     *
     * @return string
     */
    public function getMail()
    {
        return $this->mail;
    }

    private $comboElepants;


    public function getComboElepants(){

        return $this->codigo." (".$this->calle.")";

    }

    private $comboDefault;

    public function getComboDefault(){

        return $this->codigo." (".$this->empresa.")";

    }

    /*public function __toString()
    {
        return $this->codigo." (".$this->calle.")";
    }*/
    
    /**
     * @ORM\ManyToOne(targetEntity="Presis\GeneralBundle\Entity\Cliente")
     * @ORM\JoinColumn(name="cliente_id", referencedColumnName="id",nullable=true,onDelete="cascade")
     * @Expose
     **/
    private $cliente;
    
    /**
     * Set cliente
     *
     * @param string $cliente
     *
     * @return Remitente
     */
    public function setCliente($cliente)
    {
        $this->cliente = $cliente;

        return $this;
    }

    /**
     * Get cliente
     *
     * @return string
     */
    public function getCliente()
    {
        return $this->cliente;
    }


    //20-01-17 - PICCINI AGREGO PARA FILTRAR DESTINATARIOS X USUARIO Y NO POR CLIENTE
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

    /**
     * @var float
     *
     * @ORM\Column(name="kms", type="float", nullable=TRUE, precision=19, scale=2, columnDefinition="FLOAT(10,2)")
     * @Expose
     */
    private $kms=0;

    /**
     * @return float
     */
    public function getKms()
    {
        return $this->kms;
    }

    /**
     * @param float $kms
     */
    public function setKms($kms)
    {
        $this->kms = $kms;
    }

    /**
     * @var string
     *
     * @ORM\Column(name="zona", type="string", length=100, nullable=true)
     * @Expose
     */
    private $zona;

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

    /**
     * @return string
     */
    public function getCuit()
    {
        return $this->cuit;
    }

    /**
     * @param string $cuit
     */
    public function setCuit($cuit)
    {
        $this->cuit = $cuit;
    }




    
}

