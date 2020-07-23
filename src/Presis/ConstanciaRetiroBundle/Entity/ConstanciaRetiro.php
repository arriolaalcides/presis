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
 * ConstanciaRetiro
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Presis\ConstanciaRetiroBundle\Entity\ConstanciaRetiroRepository")
 * @ExclusionPolicy("none")
 */
class ConstanciaRetiro
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
     * @ORM\Column(name="timestamp", type="datetime", nullable=true)
     *
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
     * @VirtualProperty
     * @SerializedName("fechaBase")
     *
     * @return string
     */
    public function getFechaBase()
    {
        return $this->getTimestamp();
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
     * @var boolean
     *
     * @ORM\Column(name="confirmada", type="boolean")
     */
    private $confirmada = false;

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
     * @var integer
     *
     * @ORM\Column(name="retiro", type="integer", nullable=true)
     */
    private $retiro;

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
     * @var string
     *
     * @ORM\Column(name="sucursal", type="string", length=255, nullable=true)
     */
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
     * @var boolean
     *
     * @ORM\Column(name="is_fijo", type="boolean")
     */
    private $is_fijo = false;

    /**
     * @return boolean
     */
    public function isIsFijo()
    {
        return $this->is_fijo;
    }

    /**
     * @param boolean $is_fijo
     */
    public function setIsFijo($is_fijo)
    {
        $this->is_fijo = $is_fijo;
    }

    /**
     * @var date
     *
     * @ORM\Column(name="fechaHora", type="date", nullable=true)
     */
    private $fechaHora;

    /**
     * @return Date
     */
    public function getFechaHora()
    {
        return $this->fechaHora;
    }

    /**
     * @param Date $fechaHora
     */
    public function setFechaHora($fechaHora)
    {
        $this->fechaHora = $fechaHora;
    }

    /**
     * @var date
     *
     * @ORM\Column(name="fechaRetirado", type="datetime", nullable=true)
     */
    private $fechaRetirado;

    /**
     * @return Date
     */
    public function getFechaRetirado()
    {
        return $this->fechaRetirado;
    }

    /**
     * @param Date $fechaRetirado
     */
    public function setFechaRetirado($fechaRetirado)
    {
        $this->fechaRetirado = $fechaRetirado;
    }

    /**
     * @return boolean
     */
    public function isConfirmada()
    {
        return $this->confirmada;
    }

    /**
     * @param boolean $confirmada
     */
    public function setConfirmada($confirmada)
    {
        $this->confirmada = $confirmada;
    }

    /**
     * @return int
     */
    public function getRetiro()
    {
        return $this->retiro;
    }

    /**
     * @param int $retiro
     */
    public function setRetiro($retiro)
    {
        $this->retiro = $retiro;
    }

    /**
     * @var string
     *
     * @ORM\Column(name="end", type="string", length=5, nullable=true)
     */
    private $end;

    /**
     * @return string
     */
    public function getEnd()
    {
        return $this->end;
    }

    /**
     * @param string $end
     */
    public function setEnd($end)
    {
        $this->end = $end;
    }

    /**
     * @var boolean
     *
     * @ORM\Column(name="habilitado", type="boolean")
     */
    private $habilitado = false;

    /**
     * @var boolean
     *
     * @ORM\Column(name="enviado", type="boolean")
     */
    private $enviado = false;

    /**
     * @var boolean
     *
     * @ORM\Column(name="recibido", type="boolean")
     */
    private $recibido = false;

    /**
     * @return boolean
     */
    public function isEnviado()
    {
        return $this->enviado;
    }

    /**
     * @param boolean $enviado
     */
    public function setEnviado($enviado)
    {
        $this->enviado = $enviado;
    }

    /**
     * @return boolean
     */
    public function isRecibido()
    {
        return $this->recibido;
    }

    /**
     * @param boolean $recibido
     */
    public function setRecibido($recibido)
    {
        $this->recibido = $recibido;
    }

    /**
     * @return boolean
     */
    public function isHabilitado()
    {
        return $this->habilitado;
    }

    /**
     * @param boolean $habilitado
     */
    public function setHabilitado($habilitado)
    {
        $this->habilitado = $habilitado;
    }





}