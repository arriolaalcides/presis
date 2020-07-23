<?php

namespace Presis\GeneralBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Presis\DistribuidorBundle\Entity\Distribuidor;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Exclude;
use JMS\Serializer\Annotation\Expose;
use JMS\Serializer\Annotation\VirtualProperty;
use JMS\Serializer\Annotation\SerializedName;
/**
 * Sucursal
 *
 * @ORM\Table(name="sucursal")
 * @ORM\Entity(repositoryClass="Presis\GeneralBundle\Entity\SucursalRepository")
 * @ExclusionPolicy("none")
 */
class Sucursal
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
     * @ORM\Column(name="cp", type="string", length=25)


     */
    private $cp;
    /**
     * @var string
     *
     * @ORM\Column(name="codigo_sucursal", type="string", length=255)
     */
    private $codSuc;
    /**
     * @var string
     *
     * @ORM\Column(name="descripcion", type="string", length=255)
     */
    private $descripcion;

    /**
     * @var string
     *
     * @ORM\Column(name="calle", type="string", length=255)
     */
    private $calle;

    /**
     * @var string
     *
     * @ORM\Column(name="altura", type="string", length=255)
     */
    private $altura;

    /**
     * @var string
     *
     * @ORM\Column(name="piso", type="string", length=255,nullable=true)
     */
    private $piso;

    /**
     * @var string
     *
     * @ORM\Column(name="dpto", type="string", length=255,nullable=true)
     */
    private $dpto;

    /**
     * @var string
     *
     * @ORM\Column(name="other_info", type="text",nullable=true)
     */
    private $otherInfo;

    /**
     * @ORM\ManyToOne(targetEntity="Presis\GeneralBundle\Entity\Cliente", inversedBy="sucursales")
     * @ORM\JoinColumn(name="cliente_id", referencedColumnName="id",nullable=false)
     */

    protected $cliente;


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
     * @return Sucursal
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

    /**
     * Set calle
     *
     * @param string $calle
     * @return Sucursal
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
     * @param string $altura
     * @return Sucursal
     */
    public function setAltura($altura)
    {
        $this->altura = $altura;

        return $this;
    }

    /**
     * Get altura
     *
     * @return string 
     */
    public function getAltura()
    {
        return $this->altura;
    }

    /**
     * Set piso
     *
     * @param string $piso
     * @return Sucursal
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
     * @return Sucursal
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
     * Set otherInfo
     *
     * @param string $otherInfo
     * @return Sucursal
     */
    public function setOtherInfo($otherInfo)
    {
        $this->otherInfo = $otherInfo;

        return $this;
    }

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
     * Get otherInfo
     *
     * @return string 
     */

    public function getOtherInfo()
    {
        return $this->otherInfo;
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
    public function __toString(){
        return $this->getDescripcion();
    }

    /**
     * @return string
     */
    public function getCodSuc()
    {
        return $this->codSuc;
    }

    /**
     * @param string $codSuc
     */
    public function setCodSuc($codSuc)
    {
        $this->codSuc = $codSuc;
    }


    //01-02-17 PICCINI - AGREGO LOCALIDAD Y PROVINCIA
    /**
     * @var string
     *
     * @ORM\Column(name="localidad", type="string", length=255)
     */
    private $localidad;

    /**
     * @var string
     *
     * @ORM\Column(name="provincia", type="string", length=255)
     */
    private $provincia;

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
     * @VirtualProperty
     * @SerializedName("empresa")
     *
     * @return string
     */
    public function getEmpresaText()
    {
        return $this->getCliente()->getEmpresa();
    }


    /**
     * @var string
     *
     * @ORM\Column(name="cuit", type="string", length=13, nullable=true)
     */
    private $cuit;

    /**
     * @var string
     *
     * @ORM\Column(name="mail", type="string", length=255, nullable=true)
     */
    private $mail;

    /**
     * @var string
     *
     * @ORM\Column(name="contacto", type="string", length=100, nullable=true)
     */
    private $contacto;

    /**
     * @var string
     *
     * @ORM\Column(name="razonSocial", type="string", length=255, nullable=true)
     */
    private $razonSocial;

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
     * @return string
     */
    public function getContacto()
    {
        return $this->contacto;
    }

    /**
     * @param string $contacto
     */
    public function setContacto($contacto)
    {
        $this->contacto = $contacto;
    }

    /**
     * @return string
     */
    public function getRazonSocial()
    {
        return $this->razonSocial;
    }

    /**
     * @param string $razonSocial
     */
    public function setRazonSocial($razonSocial)
    {
        $this->razonSocial = $razonSocial;
    }

    /**
     * @var float
     *
     * @ORM\Column(name="kms", type="float", nullable=TRUE, precision=19, scale=2, columnDefinition="FLOAT(10,2)")
     */
    private $kms=0;


    /**
     * @var string
     *
     * @ORM\Column(name="celular", type="string", length=255, nullable=true)
     */
    private $celular;


    /**
     * @return string
     */
    public function getCelular()
    {
        return $this->celular;
    }

    /**
     * @param string $celular
     */
    public function setCelular($celular)
    {
        $this->celular = $celular;
    }

    /**
     * @return string
     */
    public function getKms()
    {
        return $this->kms;
    }

    /**
     * @param string $kms
     */
    public function setKms($kms)
    {
        $this->kms = $kms;
    }

    /**
     * @ORM\OneToMany(targetEntity="Presis\RecorridoBundle\Entity\Recorrido", mappedBy="sucursal", cascade={"persist", "remove"})
     */
    private $sucursal;


    /**
     * @ORM\Column(type="boolean", nullable=true)
     * @Expose
     */
    private $esPropia = false;

    /**
     * @return mixed
     */
    public function getSucursal()
    {
        return $this->sucursal;
    }

    /**
     * @param mixed $sucursal
     */
    public function setSucursal($sucursal)
    {
        $this->sucursal = $sucursal;
    }

    /**
     * @return mixed
     */
    public function getEsPropia()
    {
        return $this->esPropia;
    }

    /**
     * @param mixed $esPropia
     */
    public function setEsPropia($esPropia)
    {
        $this->esPropia = $esPropia;
    }

    /**
     * @ORM\OneToMany(targetEntity="Presis\DistribuidorBundle\Entity\Distribuidor", mappedBy="sucursal", cascade={"persist", "remove"})
     */
    private $distribuidor;

    /**
     * @return mixed
     */
    public function getDistribuidor()
    {
        return $this->distribuidor;
    }

    /**
     * @param mixed $distribuidor
     */
    public function setDistribuidor($distribuidor)
    {
        $this->distribuidor = $distribuidor;
    }



    /**
     * Constructor. Por defecto copia el id en el código, aunque puede ser editado luego.
     */
    public function __construct()
    {
        $this->distribuidor = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add distribuidor
     *
     * @param \Presis\DistribuidorBundle\Entity\Distribuidor $distribuidor
     *
     * @return Distribuidor
     */
    public function addDistribuidor(\Presis\DistribuidorBundle\Entity\Distribuidor $distribuidor)
    {
        $this->distribuidor[] = $distribuidor;

        return $this;
    }

    /**
     * Remove distribuidor
     *
     * @param \Presis\DistribuidorBundle\Entity\Distribuidor $distribuidor
     */
    public function removeDistribuidor(\Presis\DistribuidorBundle\Entity\Distribuidor $distribuidor)
    {
        $this->distribuidor->removeElement($distribuidor);
    }


}
