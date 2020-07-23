<?php

namespace Presis\RetiroBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Comprador
 *
 * @ORM\Table(name="comprador")
 * @ORM\Entity(repositoryClass="Presis\RetiroBundle\Entity\CompradorRepository")
 */
class Comprador
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
     * @ORM\Column(name="empresa", type="string", length=100, nullable=true)
     */
    private $empresa;
    /**
     * @var string
     *
     * @ORM\Column(name="cuit", type="string", length=13, nullable=true)
     *
     */
    private $cuit;
    /**
     * @var string
     *
     * @ORM\Column(name="localidad", type="string", length=100, nullable=true)
     */
    private $localidad;

    /**
     * @var string
     *
     * @ORM\Column(name="provincia", type="string", length=100, nullable=true)
     */
    private $provincia;
    /**
     * @var string
     *
     * @ORM\Column(name="calle", type="string", length=254,nullable=true)
     */
    private $calle;

    /**
     * @var string
     *
     * @ORM\Column(name="apellido_nombre", type="string", length=254,nullable=true)
     */
    private $apenom;
    /**
     * @var string
     *
     * @ORM\Column(name="altura", type="string", length=254,nullable=true)
     */
    private $altura;

    /**
     * @var string
     *
     * @ORM\Column(name="piso", type="string", length=254,nullable=true)
     */
    private $piso;

    /**
     * @var string
     *
     * @ORM\Column(name="dpto", type="string", length=254,nullable=true)
     */
    private $dpto;
    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=500,nullable=true)
     */
    private $email;
    /**
     * @var string
     *
     * @ORM\Column(name="celular", type="string", length=254,nullable=true)
     */
    private $celular;
    /**
     * @var string
     *
     * @ORM\Column(name="other_info", type="text", length=254, nullable=true)
     */
    private $otherInfo;
    /**
     * @var integer
     *
     * @ORM\Column(name="cp", type="string", length=25, nullable=true)
     */
    private $cp;
    /**
     * @var string
     *
     * @ORM\Column(name="codigo", type="string", length=25, nullable=true)
     */
    private $codigo;
    
     /**
     * Set codigo
     *
     * @param string $codigo
     *
     * @return Comprador
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
     * @return Comprador
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
     * Set localidad
     *
     * @param string $localidad
     *
     * @return Comprador
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
     * @return Comprador
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
     * @return string
     */
    public function getApenom()
    {
        return $this->apenom;
    }

    /**
     * @param string $apenom
     */
    public function setApenom($apenom)
    {
        //$apenom = $this->limpiarCaracteresEspeciales($apenom);
        $this->apenom = $apenom;
    }
    
    public function __toString() {
        return $this->getApenom()."";
    }

    public function limpiarCaracteresEspeciales($string ){
        $string = htmlentities($string);
        $string = preg_replace('/\&(.)[^;]*;/', '\\1', $string);
        return $string;
    }


    /**
     * @var string
     *
     * @ORM\Column(name="horario", type="text", length=5, nullable=true)
     */
    private $horario;

    /**
     * @var string
     *
     * @ORM\Column(name="documento", type="text", length=15, nullable=true)
     */
    private $documento;

    /**
     * @return string
     */
    public function getHorario()
    {
        return $this->horario;
    }

    /**
     * @param string $horario
     */
    public function setHorario($horario)
    {
        $this->horario = $horario;
    }

    /**
     * @return string
     */
    public function getDocumento()
    {
        return $this->documento;
    }

    /**
     * @param string $documento
     */
    public function setDocumento($documento)
    {
        $this->documento = $documento;
    }

    /**
 * @var string
 *
 * @ORM\Column(name="obs1", type="text", length=255, nullable=true)
 */
    private $obs1;

    /**
     * @var string
     *
     * @ORM\Column(name="obs2", type="text", length=255, nullable=true)
     */
    private $obs2;

    /**
     * @var string
     *
     * @ORM\Column(name="obs3", type="text", length=255, nullable=true)
     */
    private $obs3;

    /**
     * @var string
     *
     * @ORM\Column(name="obs4", type="text", length=255, nullable=true)
     */
    private $obs4;

    /**
     * @return string
     */
    public function getObs1()
    {
        return $this->obs1;
    }

    /**
     * @param string $obs1
     */
    public function setObs1($obs1)
    {
        $this->obs1 = $obs1;
    }

    /**
     * @return string
     */
    public function getObs2()
    {
        return $this->obs2;
    }

    /**
     * @param string $obs2
     */
    public function setObs2($obs2)
    {
        $this->obs2 = $obs2;
    }

    /**
     * @return string
     */
    public function getObs3()
    {
        return $this->obs3;
    }

    /**
     * @param string $obs3
     */
    public function setObs3($obs3)
    {
        $this->obs3 = $obs3;
    }

    /**
     * @var string
     *
     * @ORM\Column(name="comZona", type="text", length=45, nullable=true)
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
     * @var string
     *
     * @ORM\Column(name="obsEstado", type="text", length=45, nullable=true)
     */
    private $obsEstado;

    /**
     * @return string
     */
    public function getObsEstado()
    {
        return $this->obsEstado;
    }

    /**
     * @param string $obsEstado
     */
    public function setObsEstado($obsEstado)
    {
        $this->obsEstado = $obsEstado;
    }

    /**
     * @return string
     */
    public function getObs4()
    {
        return $this->obs4;
    }

    /**
     * @param string $obs4
     */
    public function setObs4($obs4)
    {
        $this->obs4 = $obs4;
    }

    /**
     * @var float
     *
     * @ORM\Column(name="kms", type="float", nullable=TRUE, precision=19, scale=2, columnDefinition="FLOAT(10,2)")
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
