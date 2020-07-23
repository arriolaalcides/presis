<?php

namespace Presis\EstadoBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;
use JMS\Serializer\Annotation\VirtualProperty;
use JMS\Serializer\Annotation\SerializedName;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
/**
 * Estado
 *
 * @ORM\Table(name="estado")
 * @ORM\Entity
 * @UniqueEntity("codigo")
 * @ExclusionPolicy("all")
 */
class Estado
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     *
     * @Expose
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="nombre", type="string", length=50)
     *
     * @Expose
     */
    private $nombre;

    /**
     * @var string
     *
     * @ORM\Column(name="codigo", type="string", length=5, nullable=true)
     *
     * @Expose
     */
    private $codigo;

    /**
     * @var boolean
     *
     * @ORM\Column(name="para_recorrido", type="boolean", nullable=true)
     * @Expose
     */
    private $seleccionableParaRecorrido = true;

    /**
     * @var boolean
     *
     * @ORM\Column(name="para_chofer", type="boolean", nullable=true)
     *@Expose
     */
    private $seleccionableParaChofer = true;

    /**
     * @var boolean
     *
     *@ORM\Column(name="para_web", type="boolean", nullable=true)
     *@Expose
     */
    private $seleccionableParaWeb = true;

    /**
     * @ORM\OneToMany(targetEntity="Presis\RetiroBundle\Entity\Retiro", mappedBy="estado")
     *
     */
    protected $retiros;

    /**
     * @ORM\OneToMany(targetEntity="Presis\GestionCelBundle\Entity\GestionCel", mappedBy="estado")
     *
     */
    protected $gestionescel;

    /**
     * @ORM\OneToMany(targetEntity="Presis\RetiroBundle\Entity\Motivo", mappedBy="estado_siguiente")
     */
    protected $motivos;

    /**
     * @ORM\OneToMany(targetEntity="Presis\TrackerBundle\Entity\Tracker", mappedBy="estado")
     */
    protected $historicos;

    /**
     * @return mixed
     */
    public function getSeleccionableParaWeb()
    {
        return $this->seleccionableParaWeb;
    }

    /**
     * @param mixed $seleccionableParaWeb
     */
    public function setSeleccionableParaWeb($seleccionableParaWeb)
    {
        $this->seleccionableParaWeb = $seleccionableParaWeb;
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
     * Set nombre
     *
     * @param string $nombre
     *
     * @return Estado
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
     * Set seleccionableParaRecorrido
     *
     * @param boolean $seleccionableParaRecorrido
     *
     * @return Estado
     */
    public function setSeleccionableParaRecorrido($seleccionableParaRecorrido)
    {
        $this->seleccionableParaRecorrido = $seleccionableParaRecorrido;

        return $this;
    }

    /**
     * Get seleccionableParaRecorrido
     *
     * @return boolean
     */
    public function isSeleccionableParaRecorrido()
    {
        return $this->seleccionableParaRecorrido;
    }

    public function __construct()
    {
        $this->retiros = new ArrayCollection();
        $this->motivos = new ArrayCollection();
        $this->historicos = new ArrayCollection();
        $this->gestionescel = new ArrayCollection();
    }

    public function __toString()
    {
        return $this->getCodigo() . ' - ' .$this->getNombre();
    }

    /**
     * Add retiro
     *
     * @param \Presis\RetiroBundle\Entity\Retiro $retiro
     *
     * @return Estado
     */
    public function addRetiro(\Presis\RetiroBundle\Entity\Retiro $retiro)
    {
        $this->retiros[] = $retiro;

        return $this;
    }

    /**
     * Remove retiro
     *
     * @param \Presis\RetiroBundle\Entity\Retiro $retiro
     */
    public function removeRetiro(\Presis\RetiroBundle\Entity\Retiro $retiro)
    {
        $this->retiros->removeElement($retiro);
    }

    /**
     * Get retiros
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getRetiros()
    {
        return $this->retiros;
    }

    /**
     * Add motivo
     *
     * @param \Presis\RetiroBundle\Entity\Motivo $motivo
     *
     * @return Estado
     */
    public function addMotivo(\Presis\RetiroBundle\Entity\Motivo $motivo)
    {
        $this->motivos[] = $motivo;

        return $this;
    }

    /**
     * Remove motivo
     *
     * @param \Presis\RetiroBundle\Entity\Motivo $motivo
     */
    public function removeMotivo(\Presis\RetiroBundle\Entity\Motivo $motivo)
    {
        $this->motivos->removeElement($motivo);
    }

    /**
     * Get motivos
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getMotivos()
    {
        return $this->motivos;
    }

    /**
     * Add historico
     *
     * @param \Presis\TrackerBundle\Entity\Tracker $historico
     *
     * @return Estado
     */
    public function addHistorico(\Presis\TrackerBundle\Entity\Tracker $historico)
    {
        $this->historicos[] = $historico;

        return $this;
    }

    /**
     * Remove historico
     *
     * @param \Presis\TrackerBundle\Entity\Tracker $historico
     */
    public function removeHistorico(\Presis\TrackerBundle\Entity\Tracker $historico)
    {
        $this->historicos->removeElement($historico);
    }

    /**
     * Get historicos
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getHistoricos()
    {
        return $this->historicos;
    }

    /**
     * Set codigo
     *
     * @param string $codigo
     *
     * @return Estado
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
     * Get seleccionableParaRecorrido
     *
     * @return boolean
     */
    public function getSeleccionableParaRecorrido()
    {
        return $this->seleccionableParaRecorrido;
    }

    /**
     * @return mixed
     */
    public function getSeleccionableParaChofer()
    {
        return $this->seleccionableParaChofer;
    }

    /**
     * @param mixed $seleccionableParaChofer
     */
    public function setSeleccionableParaChofer($seleccionableParaChofer)
    {
        $this->seleccionableParaChofer = $seleccionableParaChofer;
    }

    //07-01 PICCINI AGREGO PROPIEDA DELAY PARA RETRASAR FECHA PACTADA SEGUN CADA ESTADO
    /**
     * @var delay
     *
     * @ORM\Column(name="delay", type="integer", nullable=true)
     * @Expose
     */
    private $delay = 0;

    /**
     * @return delay
     */
    public function getDelay()
    {
        return $this->delay;
    }

    /**
     * @param delay $delay
     */
    public function setDelay($delay)
    {
        $this->delay = $delay;
    }

    /**
     * @var boolean
     *
     *@ORM\Column(name="paraEntrega", type="boolean", nullable=true)
     *@Expose
     */
    private $paraEntrega = true;

    /**
     * @return boolean
     */
    public function isParaEntrega()
    {
        return $this->paraEntrega;
    }

    /**
     * @param boolean $paraEntrega
     */
    public function setParaEntrega($paraEntrega)
    {
        $this->paraEntrega = $paraEntrega;
    }

    /**
     * @var boolean
     *
     *@ORM\Column(name="paraRetiro", type="boolean", nullable=true)
     *@Expose
     */
    private $paraRetiro = true;

    /**
     * @return boolean
     */
    public function isParaRetiro()
    {
        return $this->paraRetiro;
    }

    /**
     * @param boolean $paraRetiro
     */
    public function setParaRetiro($paraRetiro)
    {
        $this->paraRetiro = $paraRetiro;
    }

    //07-01 PICCINI AGREGO PROPIEDA DELAYRETIRO PARA REPACTAR FECHA DE RETIRO
    /**
     * @var delay
     *
     * @ORM\Column(name="delayretiro", type="integer", nullable=true)
     * @Expose
     */
    private $delayretiro = 0;

    /**
     * @return delay
     */
    public function getDelayretiro()
    {
        return $this->delayretiro;
    }

    /**
     * @param delay $delayretiro
     */
    public function setDelayretiro($delayretiro)
    {
        $this->delayretiro = $delayretiro;
    }

    /**
     * Add gestioncel
     *
     * @param \Presis\GestionCelBundle\Entity\GestionCel $gestioncel
     *
     * @return Estado
     */
    public function addGestionCel(\Presis\GestionCelBundle\Entity\GestionCel $gestioncel)
    {
        $this->gestionescel[] = $gestioncel;

        return $this;
    }

    /**
     * Remove gestioncel
     *
     * @param \Presis\GestionCelBundle\Entity\GestionCel $gestioncel
     */
    public function removeGestionCel(\Presis\GestionCelBundle\Entity\GestionCel $gestioncel)
    {
        $this->gestionescel->removeElement($gestioncel);
    }

    /**
     * Get gestionescel
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getGestionesCel()
    {
        return $this->gestionescel;
    }
    
    /**
     * @var boolean
     *
     *@ORM\Column(name="movi", type="boolean", nullable=true)
     *@Expose
     */
    private $movi = 0;

    /**
     * @return boolean
     */
    public function getMovi()
    {
        return $this->movi;
    }

    /**
     * @param boolean $movi
     */
    public function setMovi($movi)
    {
        $this->movi = $movi;
    }
    

}
