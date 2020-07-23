<?php

namespace Presis\ExpresoBundle\Entity;

use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;
use Doctrine\ORM\Mapping as ORM;

/**
 * Expreso
 *
 * @ORM\Table(name="expreso")
 * @ORM\Entity
 *
 * @ExclusionPolicy("all")
 */
class Expreso
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     *
     * @Expose
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="codigo", type="string", length=10, nullable=false)
     *
     * @Expose
     */
    private $codigo;

    /**
     * @var string
     *
     * @ORM\Column(name="nombre", type="string", length=25, nullable=false)
     *
     * @Expose
     */
    private $nombre;

    /**
     * @var string
     *
     * @ORM\Column(name="direccion", type="string", length=35, nullable=true)
     *
     * @Expose
     */
    private $direccion;

    /**
     * @var string
     *
     * @ORM\Column(name="localidad", type="string", length=20, nullable=true)
     *
     * @Expose
     */
    private $localidad;

    /**
     * @var string
     *
     * @ORM\Column(name="provincia", type="string", length=20, nullable=true)
     */
    private $provincia;

    /**
     * @var string
     *
     * @ORM\Column(name="telefono", type="string", length=30, nullable=true)
     *
     * @Expose
     */
    private $telefono;

    /**
     * @var string
     *
     * @ORM\Column(name="telefono2", type="string", length=30, nullable=true)
     */
    private $telefono2;

    /**
     * @var string
     *
     * @ORM\Column(name="cp", type="string", length=10, nullable=true)
     */
    private $cp;

    /**
     * @var string
     *
     * @ORM\Column(name="mail", type="string", length=50, nullable=true)
     *
     * @Expose
     */
    private $mail;

    /**
     * @var string
     *
     * @ORM\Column(name="web", type="string", length=50, nullable=true)
     */
    private $web;

    /**
     * @var string
     *
     * @ORM\Column(name="cuit", type="string", length=15, nullable=true)
     */
    private $cuit;

    /**
     * @var string
     *
     * @ORM\Column(name="encargado", type="string", length=40, nullable=true)
     *
     * @Expose
     */
    private $encargado;

    /**
     * @var string
     *
     * @ORM\Column(name="horario", type="string", length=20, nullable=true)
     */
    private $horario;

    /**
     * @var string
     *
     * @ORM\Column(name="codigos", type="string", length=30, nullable=true)
     */
    private $codigos;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="alta", type="date", nullable=true)
     */
    private $alta;

    /**
     * @var string
     *
     * @ORM\Column(name="estado", type="string", length=10, nullable=true)
     */
    private $estado;

    /**
     * @var string
     *
     * @ORM\Column(name="medio", type="string", length=18, nullable=true)
     */
    private $medio;

    /**
     * @var string
     *
     * @ORM\Column(name="descripcionServicio", type="text", nullable=true)
     */
    private $descripcionservicio;

    /**
     * @var string
     *
     * @ORM\Column(name="otrosDatos", type="string", length=100, nullable=true)
     */
    private $otrosdatos;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fecha_alta", type="date", nullable=true)
     */
    private $fechaAlta;

    /**
     * @var boolean
     *
     * @ORM\Column(name="const", type="boolean", nullable=true)
     */
    private $const;

    /**
     * @var string
     *
     * @ORM\Column(name="zonasAtencion", type="string", length=30, nullable=true)
     */
    private $zonasatencion;

    /**
     * @var string
     *
     * @ORM\Column(name="contacto", type="string", length=40, nullable=true)
     */
    private $contacto;

    /**
     * @var boolean
     *
     * @ORM\Column(name="finishing", type="boolean", nullable=true)
     */
    private $finishing;

    /**
     * @var string
     *
     * @ORM\Column(name="ip", type="string", length=30, nullable=true)
     */
    private $ip;

    /**
     * @var string
     *
     * @ORM\Column(name="codigoEmpleado", type="string", length=30, nullable=true)
     */
    private $codigoempleado;

    /**
     * @var string
     *
     * @ORM\Column(name="empleado", type="string", length=30, nullable=true)
     */
    private $empleado;

    /**
     * @var string
     *
     * @ORM\Column(name="sucursal", type="string", length=30, nullable=true)
     */
    private $sucursal;

    /**
     * @var boolean
     *
     * @ORM\Column(name="esExpreso", type="boolean", nullable=true)
     */
    private $esexpreso;

    /**
     * @var string
     *
     * @ORM\Column(name="zona", type="string", length=30, nullable=true)
     */
    private $zona;

    /**
     * @var string
     *
     * @ORM\Column(name="cuentaCorriente", type="string", length=20, nullable=true)
     */
    private $cuentacorriente;

    /**
     * @ORM\OneToMany(targetEntity="Presis\RecorridoBundle\Entity\Recorrido", mappedBy="expreso")
     */
    private $recorridos;

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
     * @return Expreso
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
     * Set nombre
     *
     * @param string $nombre
     *
     * @return Expreso
     */
    public function setNombre($nombre)
    {
        $this->nombre = $nombre;

        return $this;
    }

    /**
     * Get nombre
     *
     * @return string
     */
    public function getNombre()
    {
        return $this->nombre;
    }

    /**
     * Set direccion
     *
     * @param string $direccion
     *
     * @return Expreso
     */
    public function setDireccion($direccion)
    {
        $this->direccion = $direccion;

        return $this;
    }

    /**
     * Get direccion
     *
     * @return string
     */
    public function getDireccion()
    {
        return ($this->direccion === NULL)?"":$this->direccion;
    }

    /**
     * Set localidad
     *
     * @param string $localidad
     *
     * @return Expreso
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
        return ($this->localidad === NULL)?"":$this->localidad;
    }

    /**
     * Set provincia
     *
     * @param string $provincia
     *
     * @return Expreso
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
     * Set telefono
     *
     * @param string $telefono
     *
     * @return Expreso
     */
    public function setTelefono($telefono)
    {
        $this->telefono = $telefono;

        return $this;
    }

    /**
     * Get telefono
     *
     * @return string
     */
    public function getTelefono()
    {
        return $this->telefono;
    }

    /**
     * Set telefono2
     *
     * @param string $telefono2
     *
     * @return Expreso
     */
    public function setTelefono2($telefono2)
    {
        $this->telefono2 = $telefono2;

        return $this;
    }

    /**
     * Get telefono2
     *
     * @return string
     */
    public function getTelefono2()
    {
        return $this->telefono2;
    }

    /**
     * Set cp
     *
     * @param string $cp
     *
     * @return Expreso
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
     * Set mail
     *
     * @param string $mail
     *
     * @return Expreso
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

    /**
     * Set web
     *
     * @param string $web
     *
     * @return Expreso
     */
    public function setWeb($web)
    {
        $this->web = $web;

        return $this;
    }

    /**
     * Get web
     *
     * @return string
     */
    public function getWeb()
    {
        return $this->web;
    }

    /**
     * Set cuit
     *
     * @param string $cuit
     *
     * @return Expreso
     */
    public function setCuit($cuit)
    {
        $this->cuit = $cuit;

        return $this;
    }

    /**
     * Get cuit
     *
     * @return string
     */
    public function getCuit()
    {
        return $this->cuit;
    }

    /**
     * Set encargado
     *
     * @param string $encargado
     *
     * @return Expreso
     */
    public function setEncargado($encargado)
    {
        $this->encargado = $encargado;

        return $this;
    }

    /**
     * Get encargado
     *
     * @return string
     */
    public function getEncargado()
    {
        return $this->encargado;
    }

    /**
     * Set horario
     *
     * @param string $horario
     *
     * @return Expreso
     */
    public function setHorario($horario)
    {
        $this->horario = $horario;

        return $this;
    }

    /**
     * Get horario
     *
     * @return string
     */
    public function getHorario()
    {
        return $this->horario;
    }

    /**
     * Set codigos
     *
     * @param string $codigos
     *
     * @return Expreso
     */
    public function setCodigos($codigos)
    {
        $this->codigos = $codigos;

        return $this;
    }

    /**
     * Get codigos
     *
     * @return string
     */
    public function getCodigos()
    {
        return $this->codigos;
    }

    /**
     * Set alta
     *
     * @param \DateTime $alta
     *
     * @return Expreso
     */
    public function setAlta($alta)
    {
        $this->alta = $alta;

        return $this;
    }

    /**
     * Get alta
     *
     * @return \DateTime
     */
    public function getAlta()
    {
        return $this->alta;
    }

    /**
     * Set estado
     *
     * @param string $estado
     *
     * @return Expreso
     */
    public function setEstado($estado)
    {
        $this->estado = $estado;

        return $this;
    }

    /**
     * Get estado
     *
     * @return string
     */
    public function getEstado()
    {
        return $this->estado;
    }

    /**
     * Set medio
     *
     * @param string $medio
     *
     * @return Expreso
     */
    public function setMedio($medio)
    {
        $this->medio = $medio;

        return $this;
    }

    /**
     * Get medio
     *
     * @return string
     */
    public function getMedio()
    {
        return $this->medio;
    }

    /**
     * Set descripcionservicio
     *
     * @param string $descripcionservicio
     *
     * @return Expreso
     */
    public function setDescripcionservicio($descripcionservicio)
    {
        $this->descripcionservicio = $descripcionservicio;

        return $this;
    }

    /**
     * Get descripcionservicio
     *
     * @return string
     */
    public function getDescripcionservicio()
    {
        return $this->descripcionservicio;
    }

    /**
     * Set otrosdatos
     *
     * @param string $otrosdatos
     *
     * @return Expreso
     */
    public function setOtrosdatos($otrosdatos)
    {
        $this->otrosdatos = $otrosdatos;

        return $this;
    }

    /**
     * Get otrosdatos
     *
     * @return string
     */
    public function getOtrosdatos()
    {
        return $this->otrosdatos;
    }

    /**
     * Set fechaAlta
     *
     * @param \DateTime $fechaAlta
     *
     * @return Expreso
     */
    public function setFechaAlta($fechaAlta)
    {
        $this->fechaAlta = $fechaAlta;

        return $this;
    }

    /**
     * Get fechaAlta
     *
     * @return \DateTime
     */
    public function getFechaAlta()
    {
        return $this->fechaAlta;
    }

    /**
     * Set const
     *
     * @param boolean $const
     *
     * @return Expreso
     */
    public function setConst($const)
    {
        $this->const = $const;

        return $this;
    }

    /**
     * Get const
     *
     * @return boolean
     */
    public function getConst()
    {
        return $this->const;
    }

    /**
     * Set zonasatencion
     *
     * @param string $zonasatencion
     *
     * @return Expreso
     */
    public function setZonasatencion($zonasatencion)
    {
        $this->zonasatencion = $zonasatencion;

        return $this;
    }

    /**
     * Get zonasatencion
     *
     * @return string
     */
    public function getZonasatencion()
    {
        return $this->zonasatencion;
    }

    /**
     * Set contacto
     *
     * @param string $contacto
     *
     * @return Expreso
     */
    public function setContacto($contacto)
    {
        $this->contacto = $contacto;

        return $this;
    }

    /**
     * Get contacto
     *
     * @return string
     */
    public function getContacto()
    {
        return $this->contacto;
    }

    /**
     * Set finishing
     *
     * @param boolean $finishing
     *
     * @return Expreso
     */
    public function setFinishing($finishing)
    {
        $this->finishing = $finishing;

        return $this;
    }

    /**
     * Get finishing
     *
     * @return boolean
     */
    public function getFinishing()
    {
        return $this->finishing;
    }

    /**
     * Set ip
     *
     * @param string $ip
     *
     * @return Expreso
     */
    public function setIp($ip)
    {
        $this->ip = $ip;

        return $this;
    }

    /**
     * Get ip
     *
     * @return string
     */
    public function getIp()
    {
        return $this->ip;
    }

    /**
     * Set codigoempleado
     *
     * @param string $codigoempleado
     *
     * @return Expreso
     */
    public function setCodigoempleado($codigoempleado)
    {
        $this->codigoempleado = $codigoempleado;

        return $this;
    }

    /**
     * Get codigoempleado
     *
     * @return string
     */
    public function getCodigoempleado()
    {
        return $this->codigoempleado;
    }

    /**
     * Set empleado
     *
     * @param string $empleado
     *
     * @return Expreso
     */
    public function setEmpleado($empleado)
    {
        $this->empleado = $empleado;

        return $this;
    }

    /**
     * Get empleado
     *
     * @return string
     */
    public function getEmpleado()
    {
        return $this->empleado;
    }

    /**
     * Set sucursal
     *
     * @param string $sucursal
     *
     * @return Expreso
     */
    public function setSucursal($sucursal)
    {
        $this->sucursal = $sucursal;

        return $this;
    }

    /**
     * Get sucursal
     *
     * @return string
     */
    public function getSucursal()
    {
        return $this->sucursal;
    }

    /**
     * Set esexpreso
     *
     * @param boolean $esexpreso
     *
     * @return Expreso
     */
    public function setEsexpreso($esexpreso)
    {
        $this->esexpreso = $esexpreso;

        return $this;
    }

    /**
     * Get esexpreso
     *
     * @return boolean
     */
    public function getEsexpreso()
    {
        return $this->esexpreso;
    }

    /**
     * Set zona
     *
     * @param string $zona
     *
     * @return Expreso
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
     * Set cuentacorriente
     *
     * @param string $cuentacorriente
     *
     * @return Expreso
     */
    public function setCuentacorriente($cuentacorriente)
    {
        $this->cuentacorriente = $cuentacorriente;

        return $this;
    }

    /**
     * Get cuentacorriente
     *
     * @return string
     */
    public function getCuentacorriente()
    {
        return $this->cuentacorriente;
    }

    /**
     *
     * @return string
     */
    public function __toString()
    {
        return $this->codigo . " - " . $this->nombre;
    }
}
