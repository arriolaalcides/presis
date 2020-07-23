<?php

namespace Presis\ServicioBundle\Services;
/**
 * Created by IntelliJ IDEA.
 * User: pamtru
 * Date: 12/08/2014
 * Time: 03:29 PM
 */


use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\ORMException;
use Presis\ServicioBundle\Entity\Servicio;
use Presis\ServicioBundle\Entity\ServicioRepository;
class ServiceManager {
    protected $em;
    protected $repo;
    protected $class;
    public function __construct(EntityManager $em,$class){
        $this->em=$em;
        $this->class;

        $this->repo=$em->getRepository($class);
    }
    public function helloService(){
        return "Hola servicio2";
    }
    public function createServicio(){

        return new Servicio();

    }
    public function saveServicio(Servicio $servicio,$flush){

            $repo = $this->em->getRepository('PresisServicioBundle:Servicio');
            $serv = $repo->find($servicio->getCodServ());

            $this->em->persist($servicio);
            if ($flush) {
                $this->em->flush();
                return true;
            }



    }
    public function flush(){
        $this->em->flush();
    }

} 