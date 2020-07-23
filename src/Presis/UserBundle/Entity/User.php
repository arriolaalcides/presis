<?php

namespace Presis\UserBundle\Entity;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;
use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\Common\Collections\ArrayCollection;
use Presis\GeneralBundle\Entity\Cliente;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Exclude;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
/**
 * User
 *
 * @ORM\Table(name="fos_user")
 * @ORM\Entity(repositoryClass="Presis\UserBundle\Entity\UserRepository")
 * @ExclusionPolicy("all")
 * @UniqueEntity("email")
 *
 *
 */
class User extends BaseUser
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\OneToOne(targetEntity="Presis\GeneralBundle\Entity\Vendedor")
     * @ORM\JoinColumn(name="vendedor_id", referencedColumnName="id",nullable=true,onDelete="cascade")
     **/
    private $vendedor;

    /**
     * @ORM\ManyToOne(targetEntity="Presis\GeneralBundle\Entity\Cliente")
     * @ORM\JoinColumn(name="cliente_id", referencedColumnName="id",nullable=true,onDelete="cascade")
     **/
    private $cliente;

    /**
     * @ORM\ManyToOne(targetEntity="Presis\DistribuidorBundle\Entity\Distribuidor")
     * @ORM\JoinColumn(name="distribuidor_id", referencedColumnName="id",nullable=true,onDelete="cascade")
     **/
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
     * @ORM\OneToMany(targetEntity="Presis\TrackerBundle\Entity\Tracker", mappedBy="user")
     */
    private $historicos;

    /**
     * @ORM\OneToMany(targetEntity="Presis\ReclamoBundle\Entity\Reclamo", mappedBy="user_resolvio")
     */
    private $reclamos;

    /**
     * @ORM\OneToMany(targetEntity="Presis\RecorridoBundle\Entity\RecorridoRetiro", mappedBy="user_importo")
     */
    private $retiros_planillados;

    public function __construct()
    {
        parent::__construct();
        $this->historicos = new ArrayCollection();
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
     * @return mixed
     */
    public function getCliente()
    {
//        if($this->cliente) {
            return $this->cliente;
//        }else{
//            return new Cliente();
//        }
    }

    /**
     * @param mixed $cliente
     */
    public function setCliente($cliente)
    {
        $this->cliente = $cliente;
    }

    /**
     * @return mixed
     */
    public function getVendedor()
    {
        return $this->vendedor;
    }

    /**
     * @param mixed $vendedor
     */
    public function setVendedor($vendedor)
    {
        $this->vendedor = $vendedor;
    }


    /**
     * Add historico
     *
     * @param \Presis\TrackerBundle\Entity\Tracker $historico
     *
     * @return User
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
     * Add reclamo
     *
     * @param \Presis\ReclamoBundle\Entity\Reclamo $reclamo
     *
     * @return User
     */
    public function addReclamo(\Presis\ReclamoBundle\Entity\Reclamo $reclamo)
    {
        $this->reclamos[] = $reclamo;

        return $this;
    }

    /**
     * Remove reclamo
     *
     * @param \Presis\ReclamoBundle\Entity\Reclamo $reclamo
     */
    public function removeReclamo(\Presis\ReclamoBundle\Entity\Reclamo $reclamo)
    {
        $this->reclamos->removeElement($reclamo);
    }

    /**
     * Get reclamos
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getReclamos()
    {
        return $this->reclamos;
    }

    /**
     * Add retirosPlanillado
     *
     * @param \Presis\RecorridoBundle\Entity\RecorridoRetiro $retirosPlanillado
     *
     * @return User
     */
    public function addRetirosPlanillado(\Presis\RecorridoBundle\Entity\RecorridoRetiro $retirosPlanillado)
    {
        $this->retiros_planillados[] = $retirosPlanillado;

        return $this;
    }

    /**
     * Remove retirosPlanillado
     *
     * @param \Presis\RecorridoBundle\Entity\RecorridoRetiro $retirosPlanillado
     */
    public function removeRetirosPlanillado(\Presis\RecorridoBundle\Entity\RecorridoRetiro $retirosPlanillado)
    {
        $this->retiros_planillados->removeElement($retirosPlanillado);
    }

    /**
     * Get retirosPlanillados
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getRetirosPlanillados()
    {
        return $this->retiros_planillados;
    }


    //20-01 - PICCINI AGREGO PARRA SABER SI UN USUARIO PUEDE O NO VER TODAS LAS GUIAS
    /**
     * @var boolean
     *
     * @ORM\Column(name="user_admin", type="boolean",nullable=true)
     */
    private $userAdmin=false;

    /**
     * @return boolean
     */
    public function isUserAdmin()
    {
        return $this->userAdmin;
    }

    /**
     * @param boolean $userAdmin
     */
    public function setUserAdmin($userAdmin)
    {
        $this->userAdmin = $userAdmin;
    }

    /**
     * @ORM\ManyToOne(targetEntity="Presis\GeneralBundle\Entity\Sucursal")
     * @ORM\JoinColumn(name="sucursal_id", referencedColumnName="id",nullable=true,onDelete="cascade")
     **/
    private $sucursal;

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


    public function isGranted($role)
    {
        return in_array($role, $this->getRoles());
    }


     /**
     * @var integer
     *
     * @ORM\Column(name="pass_handheld", type="integer", nullable=true)
     */
    private $pass_handheld;

    
     /**
     * @var datetime
     *
     * @ORM\Column(name="fecha_hora_login_HH", type="datetime", nullable=true)
     */
    private $fecha_hora_login_HH;

    



	
}
