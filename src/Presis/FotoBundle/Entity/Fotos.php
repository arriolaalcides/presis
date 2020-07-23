<?php

namespace Presis\FotoBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\ExclusionPolicy;
/**
 * Foto
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Presis\FotoBundle\Entity\FotosRepository")
 * @ExclusionPolicy("None")
 */
class Fotos
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
     * @var integer
     *
     * @ORM\Column(name="tracking", type="integer")
     */
    private $tracking;

    /**
     * @var string
     *
     * @ORM\Column(name="img", type="string", length=255)
     */
    private $img;


    /**
     * @var datetimee
     *
     * @ORM\Column(name="fecha", type="datetime")
     */
    private $fecha;


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
     * @return int
     */
    public function getTracking()
    {
        return $this->tracking;
    }

    /**
     * @param int $tracking
     */
    public function setTracking($tracking)
    {
        $this->tracking = $tracking;
    }

    /**
     * @return String
     */
    public function getImg()
    {
        return $this->img;
    }

    /**
     * @param String $img
     */
    public function setImg($img)
    {
        $this->img = $img;
    }

    /**
     * @return datetimee
     */
    public function getFecha()
    {
        return $this->fecha;
    }

    /**
     * @param datetimee $fecha
     */
    public function setFecha($fecha)
    {
        $this->fecha = $fecha;
    }



}

