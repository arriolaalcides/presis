<?php

namespace Presis\ServicioBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Exclude;
use Doctrine\Common\Annotations\Annotation\Enum;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
/**
 * Servicio
 *
 * @ORM\Table(name="servicio")
 * @ORM\Entity(repositoryClass="Presis\ServicioBundle\Entity\ServicioRepository")
 * @ExclusionPolicy("none")
 * @UniqueEntity("codServ");
 *
 */
class Servicio
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
    /**
     * @var string
     *
     * @ORM\Column(name="codigo_servicio", type="string",length=10)
     */
    private $codServ;
    // ...

    /**
     * @ORM\OneToMany(targetEntity="Presis\ServicioBundle\Entity\Precio", mappedBy="servicio")
     * @Exclude()
     */
    protected $precios;
    /**
     * @ORM\OneToMany(targetEntity="Habilitaciones", mappedBy="servicio")
     * @Exclude()
     */
    protected $habilitaciones;

    public function __construct()
    {
        $this->habilitaciones = new ArrayCollection();
        $this->precios = new ArrayCollection();
        $this->bultos_excedentes = new ArrayCollection();
        $this->datosenvios = new ArrayCollection();
    }

    /**
     * @var boolean
     *
     * @ORM\Column(name="activo", type="boolean")
     *
     */
    private $activo = true;

    /**
     * @var string
     *
     * @ORM\Column(name="descripcion", type="string",length=254,nullable=true)
     *
     */
    private $descripcion;
    /**
     * @var string
     *
     * @ORM\Column(name="detalle_servicio", type="text",nullable=false,nullable=true)
     *
     */
    private $detalleServicio;

    /**
     * @ORM\OneToMany(targetEntity="Presis\GeneralBundle\Entity\BultoExcedente", mappedBy="cliente")
     */
    private $bultos_excedentes;

    /**
     * @ORM\OneToMany(targetEntity="Presis\DatosEnviosBundle\Entity\DatosEnvios", mappedBy="cliente")
     */
    private $datosenvios;

    /**
     * @return string
     */
    public function getDescripcion()
    {
        return $this->descripcion;
    }

    /**
     * @param string $descripcion
     */
    public function setDescripcion($descripcion)
    {
        $this->descripcion = $descripcion;
    }

    /**
     * Get codServ
     *
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }
    /**
     * Get codServ
     *
     * @return string
     */
    public function getCodServ()
    {
        return $this->codServ;
    }
    public function setCodServ($codigo){
        $this->codServ=$codigo;
    }

    /**
     * @return mixed
     */
    public function getHabilitaciones()
    {
        return $this->habilitaciones;
    }

    /**
     * @param mixed $habilitaciones
     */
    public function setHabilitaciones($habilitaciones)
    {
        $this->habilitaciones = $habilitaciones;
    }

    /**
     * @return mixed
     */
    public function getPrecios()
    {
        return $this->precios;
    }

    /**
     * @param mixed $precios
     */
    public function setPrecios($precios)
    {
        $this->precios = $precios;
    }

    public function __toString(){
        return $this->getDescripcion();
    }

    /**
     * @return string
     */
    public function getDetalleServicio()
    {
        return $this->detalleServicio;
    }

    /**
     * @param string $detalleServicio
     */
    public function setDetalleServicio($detalleServicio)
    {
        $this->detalleServicio = $detalleServicio;
    }


    /**
     * Add precio
     *
     * @param \Presis\ServicioBundle\Entity\Precio $precio
     *
     * @return Servicio
     */
    public function addPrecio(\Presis\ServicioBundle\Entity\Precio $precio)
    {
        $this->precios[] = $precio;

        return $this;
    }

    /**
     * Remove precio
     *
     * @param \Presis\ServicioBundle\Entity\Precio $precio
     */
    public function removePrecio(\Presis\ServicioBundle\Entity\Precio $precio)
    {
        $this->precios->removeElement($precio);
    }

    /**
     * Add habilitacione
     *
     * @param \Presis\ServicioBundle\Entity\Habilitaciones $habilitacione
     *
     * @return Servicio
     */
    public function addHabilitacione(\Presis\ServicioBundle\Entity\Habilitaciones $habilitacione)
    {
        $this->habilitaciones[] = $habilitacione;

        return $this;
    }

    /**
     * Remove habilitacione
     *
     * @param \Presis\ServicioBundle\Entity\Habilitaciones $habilitacione
     */
    public function removeHabilitacione(\Presis\ServicioBundle\Entity\Habilitaciones $habilitacione)
    {
        $this->habilitaciones->removeElement($habilitacione);
    }

    /**
     * @return mixed
     */
    public function getActivo()
    {
        return $this->activo;
    }

    /**
     * @param mixed $activo
     */
    public function setActivo($activo)
    {
        $this->activo = $activo;
    }

    /**
     * Add bultosExcedente
     *
     * @param \Presis\GeneralBundle\Entity\BultoExcedente $bultosExcedente
     *
     * @return Servicio
     */
    public function addBultosExcedente(\Presis\GeneralBundle\Entity\BultoExcedente $bultosExcedente)
    {
        $this->bultos_excedentes[] = $bultosExcedente;

        return $this;
    }

    /**
     * Remove bultosExcedente
     *
     * @param \Presis\GeneralBundle\Entity\BultoExcedente $bultosExcedente
     */
    public function removeBultosExcedente(\Presis\GeneralBundle\Entity\BultoExcedente $bultosExcedente)
    {
        $this->bultos_excedentes->removeElement($bultosExcedente);
    }

    /**
     * Get bultosExcedentes
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getBultosExcedentes()
    {
        return $this->bultos_excedentes;
    }

    /**
     * Add datosenvio
     *
     * @param \Presis\DatosEnviosBundle\Entity\DatosEnvios $datosenvio
     *
     * @return Servicio
     */
    public function addDatosenvio(\Presis\DatosEnviosBundle\Entity\DatosEnvios $datosenvio)
    {
        $this->datosenvios[] = $datosenvio;

        return $this;
    }

    /**
     * Remove datosenvio
     *
     * @param \Presis\DatosEnviosBundle\Entity\DatosEnvios $datosenvio
     */
    public function removeDatosenvio(\Presis\DatosEnviosBundle\Entity\DatosEnvios $datosenvio)
    {
        $this->datosenvios->removeElement($datosenvio);
    }

    /**
     * Get datosenvios
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getDatosenvios()
    {
        return $this->datosenvios;
    }
}
