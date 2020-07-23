<?php

namespace Presis\ConstanciaRetiroBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;
use JMS\Serializer\Annotation\VirtualProperty;
use JMS\Serializer\Annotation\SerializedName;
use Zend\Form\Element\Date;
use Doctrine\ORM\Mapping\ManyToMany;

/**
 * RetirosFijos
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Presis\ConstanciaRetiroBundle\Entity\RetirosFijosRepository")
 */
class RetirosFijos
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
     * @var date
     *
     * @ORM\Column(name="fecha", type="date", nullable=true)
     */
    private $fecha;

    /**
     * @var date
     *
     * @ORM\Column(name="fechaAsigando", type="date", nullable=true)
     */
    private $fechaAsignado;

    /**
     * @var string
     *
     * @ORM\Column(name="franja", type="string", length=150, nullable=true)
     */
    private $franja;

    /**
     * @ORM\ManyToOne(targetEntity="Presis\GeneralBundle\Entity\Cliente")
     * @ORM\JoinColumn(name="cliente_id", referencedColumnName="id",nullable=true,onDelete="cascade")
     **/
    private $cliente;

    /**
     * @var string
     *
     * @ORM\Column(name="calle", type="string", length=150, nullable=true)
     */
    private $calle;

    /**
     * @var integer
     *
     * @ORM\Column(name="altura", type="integer", nullable=true)
     */
    private $altura;

    /**
     * @var string
     *
     * @ORM\Column(name="piso", type="string", length=20, nullable=true)
     */
    private $piso;

    /**
     * @var string
     *
     * @ORM\Column(name="dpto", type="string", length=20, nullable=true)
     */
    private $dpto;

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
     * @var string
     *
     * @ORM\Column(name="usuario", type="string", length=45, nullable=true)
     */
    private $usuario;

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
     * Set calle
     *
     * @param string $calle
     *
     * @return ConstanciaRetiro
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
     * @return ConstanciaRetiro
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
     * @return ConstanciaRetiro
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
     * @return ConstanciaRetiro
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
     * @return ConstanciaRetiro
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
     * @return ConstanciaRetiro
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
     * @return \DateTime
     */
    public function getFecha()
    {
        return $this->fecha;
    }

    /**
     * @param datetime $fecha
     */
    public function setFecha($fecha)
    {
        $this->fecha = $fecha;
    }

    /**
     * @return string
     */
    public function getUsuario()
    {
        return $this->usuario;
    }

    /**
     * @param string $usuario
     */
    public function setUsuario($usuario)
    {
        $this->usuario = $usuario;
    }


    /**
     * @var datetime
     *
     * @ORM\Column(name="timestamp", type="datetime", nullable=true)
     */
    private $timestamp;

    /**
     * @return datetime
     */
    public function getTimestamp()
    {
        return $this->timestamp;
    }

    /**
     * @param datetime $timestamp
     */
    public function setTimestamp($timestamp)
    {
        $this->timestamp = $timestamp;
    }

    /**
     * @var string
     *
     * @ORM\Column(name="cp", type="string", length=10, nullable=true)
     */
    private $cp;

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
     * @VirtualProperty
     * @SerializedName("domicilio_retiro")
     *
     * @return string
     */
    public function getDomicilioRetiro()
    {
        $direccion = $this->getCalle().' '.
            $this->getAltura().' '.
            $this->getPiso().' '.
            $this->getDpto();
        return ($direccion)?$direccion:"";
    }


    /**
     * @var string
     *
     * @ORM\Column(name="estado", type="string", length=255, nullable=true)
     */
    private $estado;

    /**
     * @return string
     */
    public function getEstado()
    {
        return $this->estado;
    }

    /**
     * @param string $estado
     */
    public function setEstado($estado)
    {
        $this->estado = $estado;
    }

    /**
     * @var string
     *
     * @ORM\Column(name="distribuidor", type="string", length=255, nullable=true)
     */
    private $distribuidor;


    /**
     * @return string
     */
    public function getDistribuidor()
    {
        return $this->distribuidor;
    }

    /**
     * @param string $distribuidor
     */
    public function setDistribuidor($distribuidor)
    {
        $this->distribuidor = $distribuidor;
    }

    /**
     * @var string
     *
     * @ORM\Column(name="asigno", type="string", length=255, nullable=true)
     */
    private $asigno;

    /**
     * @return string
     */
    public function getAsigno()
    {
        return $this->asigno;
    }

    /**
     * @param string $asigno
     */
    public function setAsigno($asigno)
    {
        $this->asigno = $asigno;
    }

    /**
     * @var boolean
     *
     * @ORM\Column(name="impreso", type="boolean")
     */
    private $impreso = false;

    /**
     * @return boolean
     */
    public function isImpreso()
    {
        return $this->impreso;
    }

    /**
     * @param boolean $immpreso
     */
    public function setImpreso($impreso)
    {
        $this->impreso = $impreso;
    }

    /**
     * @VirtualProperty
     * @SerializedName("impresa")
     *
     * @return string
     */
    public function getImpresa()
    {
        return ($this->isImpreso())?"Sí":"No";
    }

    /**
     * @var string
     *
     * @ORM\Column(name="contacto", type="string", length=100, nullable=true)
     */
    private $contacto;

    /**
     * @var string
     *
     * @ORM\Column(name="telefono", type="string", length=20, nullable=true)
     */
    private $telefono;

    /**
     * @var string
     *
     * @ORM\Column(name="mail", type="string", length=155, nullable=true)
     */
    private $mail;

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
    public function getTelefono()
    {
        return $this->telefono;
    }

    /**
     * @param string $telefono
     */
    public function setTelefono($telefono)
    {
        $this->telefono = $telefono;
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
     * @var string
     *
     * @ORM\Column(name="observaciones", type="string", length=255, nullable=true)
     */
    private $observaciones;


    /**
     * @var integer
     *
     * @ORM\Column(name="bultos", type="integer", nullable=true)
     */
    private $bultos;


    /**
     * @var peso
     *
     * @ORM\Column(name="peso", type="float", nullable=true)
     */
    private $peso;

    /**
     * @return string
     */
    public function getObservaciones()
    {
        return $this->observaciones;
    }

    /**
     * @param string $observaciones
     */
    public function setObservaciones($observaciones)
    {
        $this->observaciones = $observaciones;
    }

    /**
     * @return int
     */
    public function getBultos()
    {
        return $this->bultos;
    }

    /**
     * @param int $bultos
     */
    public function setBultos($bultos)
    {
        $this->bultos = $bultos;
    }

    /**
     * @return peso
     */
    public function getPeso()
    {
        return $this->peso;
    }

    /**
     * @param peso $peso
     */
    public function setPeso($peso)
    {
        $this->peso = $peso;
    }

    /**
     * @return string
     */
    public function getFranja()
    {
        return $this->franja;
    }

    /**
     * @param string $franja
     */
    public function setFranja($franja)
    {
        $this->franja = $franja;
    }

    /**
     * @ORM\ManyToOne(targetEntity="Presis\GeneralBundle\Entity\Sucursal")
     * @ORM\JoinColumn(name="sucursal_id", referencedColumnName="id",nullable=true,onDelete="cascade")
     **/
    private $sucursal;

    /**
     * @return string
     */
    public function getSucursal()
    {
        return $this->sucursal;
    }

    /**
     * @param string $sucursal
     */
    public function setSucursal($sucursal)
    {
        $this->sucursal = $sucursal;
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
     * @var array
     *
     * @ORM\Column(name="dias", type="array", nullable=false)
     */
    private $dias;

    /**
     * @return array
     */
    public function getDias()
    {
        return $this->dias;
    }

    /**
     * @param array $dias
     */
    public function setDias($dias)
    {
        $this->dias = $dias;
    }

    /**
     * @return Date
     */
    public function getFechaAsignado()
    {
        return $this->fechaAsignado;
    }

    /**
     * @param Date $fechaAsignado
     */
    public function setFechaAsignado($fechaAsignado)
    {
        $this->fechaAsignado = $fechaAsignado;
    }


    /**
     * @var boolean
     *
     * @ORM\Column(name="is_habilitado", type="boolean")
     */
    private $is_habilitado = true;

    /**
     * @return boolean
     */
    public function isIsHabilitado()
    {
        return $this->is_habilitado;
    }

    /**
     * @param boolean $is_habilitado
     */
    public function setIsHabilitado($is_habilitado)
    {
        $this->is_habilitado = $is_habilitado;
    }



}

