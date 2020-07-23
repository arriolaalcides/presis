<?php

namespace Presis\ServicioBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Presis\ServicioBundle\Validator\Constraint\CheckTime as TimeAssert;
/**
 * Habilitaciones
 *
 * @ORM\Table(name="habilitaciones")
 * @ORM\Entity(repositoryClass="Presis\ServicioBundle\Entity\HabilitacionesRepository")
 * @UniqueEntity(
 *     fields={"cordonEntrega", "cordonRetiro","servicio"})
 * @TimeAssert
 */
class Habilitaciones
{
    public function __construct(){
        $this->horaDesde=new \DateTime('00:00');
        $this->horaHasta=new \DateTime('23:59');

    }

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="Cordon")
     * @ORM\JoinColumn(name="cordonEntrega_id", referencedColumnName="id",nullable=false)
     */

    protected $cordonEntrega;
    /**
     * @ORM\ManyToOne(targetEntity="Cordon")
     * @ORM\JoinColumn(name="cordonRetiro_id", referencedColumnName="id",nullable=false)
     */

    protected $cordonRetiro;

    /**
     * @ORM\ManyToOne(targetEntity="Servicio", inversedBy="habilitaciones")
     * @ORM\JoinColumn(name="servicio_id", referencedColumnName="id",nullable=false)

     */

    protected $servicio;

    /**
     * @var time
     *
     * @ORM\Column(name="hora_desde", type="time")


     */
    private $horaDesde;
    /**
     * @var time
     * @ORM\Column(name="hora_hasta", type="time")
     */
    private $horaHasta;

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
     * @return mixed
     */
    public function getCordonEntrega()
    {
        return $this->cordonEntrega;
    }

    /**
     * @param mixed $cordonEntrega
     */
    public function setCordonEntrega($cordonEntrega)
    {
        $this->cordonEntrega = $cordonEntrega;
    }

    /**
     * @return mixed
     */
    public function getCordonRetiro()
    {
        return $this->cordonRetiro;
    }

    /**
     * @param mixed $cordonRetiro
     */
    public function setCordonRetiro($cordonRetiro)
    {
        $this->cordonRetiro = $cordonRetiro;
    }

    /**
     * @return time
     */
    public function getHoraDesde()
    {

            return $this->horaDesde;

    }

    /**
     * @param time $hora_desde
     */
    public function setHoraDesde($hora_desde)
    {
        $this->horaDesde = $hora_desde;
    }

    /**
     * @return time
     */
    public function getHoraHasta()
    {
        return $this->horaHasta;
    }

    /**
     * @param time $hora_hasta
     */
    public function setHoraHasta($hora_hasta)
    {
        $this->horaHasta = $hora_hasta;
    }

    /**
     * @return mixed
     */
    public function getServicio()
    {
        return $this->servicio;
    }

    /**
     * @param mixed $servicio
     */
    public function setServicio($servicio)
    {
        $this->servicio = $servicio;
    }

}
