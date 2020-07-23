<?php

namespace Presis\ServicioBundle\Services;
/**
 * Created by IntelliJ IDEA.
 * User: pamtru
 * Date: 12/08/2014
 * Time: 03:29 PM
 */


use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\ORMException;
use Presis\RetiroBundle\Entity\DatosPrestador;
use Presis\RetiroBundle\Entity\Sender;
use Presis\ServicioBundle\Entity\CpCordon;
use Presis\ServicioBundle\Entity\Servicio;
use Presis\ServicioBundle\Entity\ServicioRepository;
use Doctrine\Common\Collections\Criteria;

class PrecioManager {
    protected $em;

    public function setEntityManager(ObjectManager $em)
    {
        $this->em = $em;
    }
    public function getPesoByCategoria($categoria){

        $caterepo=$this->em->getRepository("PresisGeneralBundle:Categoria");
        $cate=$caterepo->findOneByNombre($categoria);
        return $cate->getPeso();

    }
    public function calcularMayorPeso($largo,$ancho,$profundidad,$peso,$aforo){
        $pesdime=$this->calcularPesoVolumetrico($largo,$ancho,$profundidad,$aforo);

        return ($peso>$pesdime)? $peso : $pesdime;
    }
    public function calcularMayorPesoDomestico($bultos,$largo,$ancho,$profundidad,$peso,$coeficiente,$empresa){

        //die($largo.'/'.$ancho.'/'.$profundidad.'/'.$peso.'/'.$coeficiente);

        $pesdime=$this->calcularPesoVolumetricoDomestico($bultos,$largo,$ancho,$profundidad,$coeficiente,$empresa);

        //die("DIMENSION: ".$pesdime.'//'."PESO: ".$peso);

        return ($peso>$pesdime)? $peso : $pesdime;
    }
    public function validarCategoria($categoria,$cliente){


        $categorias=$cliente->getCategorias();
        foreach ($categorias as $cate){
            if(strcasecmp($cate->getNombre(),$categoria)==0){
                return $cate;
            }
        }

        return false;
    }
    public function generatePrestador(CpCordon $cpcordon){
        $datos=new DatosPrestador();
        $datos->setCp($cpcordon->getCp());
        $datos->setBarrio($cpcordon->getBarrio());
        $datos->setCordon($cpcordon->getCordon()->getDescripcion());
        $datos->setLocalidad($cpcordon->getLocalidad());
        $datos->setPartido($cpcordon->getPartido());
        $datos->setPrestador($cpcordon->getPrestador());
        $datos->setProvincia($cpcordon->getProvincia());
        $datos->setSubzona($cpcordon->getSubzona());
        $datos->setZona(($cpcordon->getZona()));
        return $datos;

    }
    public function validarDimensiones($dimensiones=null){
        if (!isset($dimensiones[0]["alto"])||empty($dimensiones[0]["alto"])){

            return false;
        }else{
            if (!isset($dimensiones[0]["largo"])||empty($dimensiones[0]["largo"])){

                return false;
            }else{
                if (!isset($dimensiones[0]["profundidad"])||empty($dimensiones[0]["profundidad"])){

                    return false;
                }else{
                    if (!is_numeric($dimensiones[0]["profundidad"])){

                        return false;
                    }
                    if (!is_numeric($dimensiones[0]["largo"])){

                        return false;
                    }
                    if (!is_numeric($dimensiones[0]["alto"])){

                        return false;
                    }
                }
            }
        }

        return true;
    }
    public function validarComprador($comprador){



        if (!isset($comprador["calle"])){
            return false;
        }
        if (!isset($comprador["altura"])){
            return false;
        }
        if (!isset($comprador["cp"])){
            return false;
        }
        if (!isset($comprador["destinatario"])){
            return false;
        }
        return true;
    }
    public  function validarSucursal($cli,$codsuc){
        $critsuc=Criteria::create()
            ->where(Criteria::expr()->eq("codSuc",$codsuc))
            ->setMaxResults(1);

        $sucursal=$cli->getSucursales()->matching($critsuc)->get(0);
        if (!$sucursal){
            return false;
        }
        return $sucursal;
    }
    public  function validarFranja($franjap){
        $franjaRepo=$this->em->getRepository("PresisRetiroBundle:FranjaEntrega");
        $franja=$franjaRepo->findOneByCodigo($franjap);
        if (!$franja){
            return false;
        }
        return $franja;
    }
    public function validarServicio($user,$cpe,$codSucu,$cs)
    {
        $servRepo=$this->em->getRepository("PresisServicioBundle:Servicio");
        $servicios=$servRepo->findHabilitados2($user,$cpe,$codSucu);
        foreach($servicios as $servicio){
            if ($servicio->getCodServ()==$cs){
                return $servicio;
            }
        }
        return false;
    }
    public function validarPeso($peso)
    {
        if (empty($peso) || !is_numeric($peso)) {
            return false;
        }
        return true;
    }
    public function setSenderData($sucursal){
        if ($sucursal) {
            $sender = new Sender();

            $sender->setAltura($sucursal->getAltura());
            $sender->setCp($sucursal->getCp());
            $sender->setCalle($sucursal->getCalle());
            $sender->setDpto($sucursal->getDpto());
            $sender->setEmpresa($sucursal->getCliente()->getEmpresa());
            $sender->setOtherInfo($sucursal->getOtherInfo());
            return $sender;
        }else{
            return null;
        }
    }

    public function calcularPesoVolumetrico($largo,$ancho,$profundidad,$aforo){
        $pesvolu=$largo*$ancho*$profundidad*$aforo;

        return ($pesvolu/300.00);
    }

    public function calcularPesoVolumetricoDomestico($bultos,$largo,$ancho,$profundidad, $coeficiente_aforo,$empresa){

        $pesvolu=$largo*$ancho*$profundidad;

        if($empresa=='maslogistica'){
            //die(($pesvolu."/".$coeficiente_aforo)."*".$bultos);
            return ($pesvolu/$coeficiente_aforo)*$bultos;
        }else{
            return ($pesvolu/$coeficiente_aforo);
        }

    }

    public function calcularPrecio($servicio,$peso,$cli,$sucursal,$cp){

        $repoLista = $this->em->getRepository('PresisServicioBundle:Lista');
        $repoCordon = $this->em->getRepository('PresisServicioBundle:CpCordon');

        //$cordon
        if (!$cli){
            return -1;
        }


        if (!$sucursal){
            return -1;
        }
        $cpsuc=$sucursal->getCP();
        $cordone=$repoCordon->findOneByCp($cp)->getCordon();

        if (!$cordone){
            return -1;
        }
        $cordonr=$repoCordon->findOneByCp($cpsuc)->getCordon();
        if (!$cordonr){

            return -1;
        }

        if (!$cli->getCustomPriceList()) {

            $lista = $repoLista->findOneBy(array("isGeneral" => true));
        }else{
            $lista=$cli->getLista();
        }

        if ($lista){
            $criteria=Criteria::create()
                ->where(Criteria::expr()->gte("rango",$peso))
                ->andWhere(Criteria::expr()->eq("cordonEntrega",$cordone))
                ->andWhere(Criteria::expr()->eq("cordonRetiro",$cordonr))
                ->andWhere(Criteria::expr()->eq("servicio",$servicio))
                ->orderBy(Array("rango"=>"asc"))
                ->setMaxResults(1);


            $precios=$lista->getPrecios()->matching($criteria);

            $precio=$precios->get(0);

            if (!isset($precio)){
                return -1;
                //cho
            }
            return $precio;
        }else{
            return -1;
        }
    }
    public function calcularPrecio2($servicio,$peso,$cli,$sucursal,$cp){

        $repoLista = $this->em->getRepository('PresisServicioBundle:Lista');
        $repoCordon = $this->em->getRepository('PresisServicioBundle:CpCordon');

        //$cordon
        if (!$cli){
            return -1;
        }


        if (!$sucursal){
            return -1;
        }
        $cpsuc=$sucursal->getCP();
        $cordone=$repoCordon->findOneByCp($cp)->getCordon();

        if (!$cordone){
            return -1;
        }
        $cordonr=$repoCordon->findOneByCp($cpsuc)->getCordon();
        if (!$cordonr){

            return -1;
        }

        if (!$cli->getCustomPriceList()) {

            $lista = $repoLista->findOneBy(array("isGeneral" => true));
        }else{
            $lista=$cli->getLista();
        }
        if ($lista){
            $criteria=Criteria::create()
                ->where(Criteria::expr()->gte("rango",$peso))
                ->andWhere(Criteria::expr()->eq("cordonEntrega",$cordone))
                ->andWhere(Criteria::expr()->eq("cordonRetiro",$cordonr))
                ->andWhere(Criteria::expr()->eq("servicio",$servicio))
                ->orderBy(Array("rango"=>"asc"))
                ->setMaxResults(1);


            $precios=$lista->getPrecios()->matching($criteria);

            $precio=$precios->get(0);

            if (!isset($precio)){
                return -1;
                //cho
            }
            return $precio->getPrecio();
        }else{
            return -1;
        }
    }

    /**
     * @param $bultosOPesoExcedente
     * @param $cliente
     * @param $servicio
     * @param $cordonRetiro
     * @param $cordonEntrega
     *
     * @return float
     */
    private function calcularPrecioPorExceso($bultosOPesoExcedente, $cliente, $servicio, $cordonRetiro, $cordonEntrega){

        $bultoExcedente = $this->em->getRepository("PresisGeneralBundle:BultoExcedente")->findOneBy(
            array(
                'cliente' => $cliente,
                'servicio' => $servicio,
                'cordonRetiro' => $cordonRetiro,
                'cordonEntrega' => $cordonEntrega
            ));

        $excedente = 0;
        if($bultoExcedente) {
            $excedente = ceil($bultosOPesoExcedente / $bultoExcedente->getBultoExcedente()) * $bultoExcedente->getCostoBultoExcedente();
        }
        return $excedente;
    }

    /**
     * @param $valor_declarado
     * @param $cliente
     * @return float
     */
    public function calcularFletePorValorDeclarado($valor_declarado, $cliente)
    {
        if (empty($valor_declarado)) {
            $valor_declarado = $cliente->getValorDeclaradoPorDefecto();
        }
        $costoFlete = $cliente->getPorcentajeACobrar() * $valor_declarado / 100;
        if ($costoFlete < $cliente->getMinimoACobrar()) {
            $costoFlete = $cliente->getMinimoACobrar();
        }
        if ($costoFlete > $cliente->getMaximoACobrar()) {
            $costoFlete = $cliente->getMaximoACobrar();
        }
        return $costoFlete;
    }

    /**
     * @param $valor_declarado
     * @param $cliente
     * @return float
     */
    public function calcularSeguro($valor_declarado, $cliente)
    {
        $seguro = $cliente->getPorcentajeSeguro() * $valor_declarado / 100;
        if ($seguro < $cliente->getSeguroMinimo()) {
            $seguro = $cliente->getSeguroMinimo();
        }
        if ($seguro > $cliente->getSeguroMaximo()) {
            $seguro = $cliente->getSeguroMaximo();
        }
        return $seguro;
    }

    /**
     * @param $rango
     * @param $cliente
     * @param $servicio
     * @param $cordonRetiro
     * @param $cordonEntrega
     *
     * @return \Presis\ServicioBundle\Entity\Precio
     */
    private function getPrecioMaxBultosOPeso($rango, $cliente, $servicio, $cordonRetiro, $cordonEntrega)
    {
        $precios = $this->em->getRepository("PresisServicioBundle:Precio")->findByCliente($cliente, $servicio, $cordonRetiro, $cordonEntrega);
        $elPrecio = ($precios)?$precios[0]:null;
        foreach ($precios as $precio) {
            if($precio->getRango() >= $rango) {
                $elPrecio = $precio;
                //die("RANGO: ".$rango." LLEGO: ".$elPrecio);
            }
        }
        return $elPrecio;
    }

    /**
     * @param $bultos
     * @param $id_servicio
     * @param $cpRemitente
     * @param $cpDestinatario
     * @param $cliente
     * @return float|int
     */
    public function calcularFletePorBultos($bultos, $id_servicio, $cpRemitente, $cpDestinatario, $cliente)
    {
        return $this->calcularFletePorBultosOPeso($bultos, $id_servicio, $cpRemitente, $cpDestinatario, $cliente);
    }

    /**
     * @param $peso
     * @param $id_servicio
     * @param $cpRemitente
     * @param $cpDestinatario
     * @param $cliente
     * @return float|int
     */
    public function calcularFletePorPeso($peso, $id_servicio, $cpRemitente, $cpDestinatario, $cliente)
    {
        return $this->calcularFletePorBultosOPeso($peso, $id_servicio, $cpRemitente, $cpDestinatario, $cliente);
    }

    /**
     * @param $bultosOPeso
     * @param $id_servicio
     * @param $cpRemitente
     * @param $cpDestinatario
     * @param $cliente
     * @return float|int
     */
    public function calcularFletePorBultosOPeso($bultosOPeso, $id_servicio, $cpRemitente, $cpDestinatario, $cliente)
    {
        $cpCordonRetiro = $this->em->getRepository("PresisServicioBundle:CpCordon")->findOneBy(
            array('cp' => $cpRemitente));
        $cpCordonEntrega = $this->em->getRepository("PresisServicioBundle:CpCordon")->findOneBy(
            array('cp' => $cpDestinatario));
        $servicio = $this->em->getRepository("PresisServicioBundle:Servicio")->find($id_servicio);

        if(!$cpCordonRetiro || !$cpCordonEntrega || !$servicio || !$bultosOPeso) {
            return 0;
        }

        $costoFlete = 0;
        $precio = $this->getPrecioMaxBultosOPeso($bultosOPeso, $cliente, $servicio, $cpCordonRetiro->getCordon(), $cpCordonEntrega->getCordon());
        if($precio) {
            $costoFlete = $precio->getPrecio();
            if ($bultosOPeso > $precio->getRango()) {
                $costoFlete += $this->calcularPrecioPorExceso($bultosOPeso - $precio->getRango(), $cliente, $servicio, $cpCordonRetiro->getCordon(), $cpCordonEntrega->getCordon());
            }
        }
        return $costoFlete;
    }
/*===============================================================================================================================*/
    // 21-03 PICCCINI AGREGO PARA CALCULAR POR RANGO DE PESO

    /**
     * @param $peso
     * @param $id_servicio
     * @param $cpRemitente
     * @param $cpDestinatario
     * @param $cliente
     * @return float|int
     */
    public function calcularPesoPorRango($kms, $peso, $id_servicio, $cpRemitente, $cpDestinatario, $cliente)
    {

        return $this->calcularFletePorBultosOPesoXrango($kms, $peso, $id_servicio, $cpRemitente, $cpDestinatario, $cliente);
    }

    /**
     * @param $bultosOPeso
     * @param $id_servicio
     * @param $cpRemitente
     * @param $cpDestinatario
     * @param $cliente
     * @return float|int
     */
    public function calcularFletePorBultosOPesoXrango($kms, $bultosOPeso, $id_servicio, $cpRemitente, $cpDestinatario, $cliente)
    {
        $cpCordonRetiro = $this->em->getRepository("PresisServicioBundle:CpCordon")->findOneBy(
            array('cp' => $cpRemitente));
        $cpCordonEntrega = $this->em->getRepository("PresisServicioBundle:CpCordon")->findOneBy(
            array('cp' => $cpDestinatario));
        $servicio = $this->em->getRepository("PresisServicioBundle:Servicio")->find($id_servicio);


        if(!$cpCordonRetiro || !$cpCordonEntrega || !$servicio || !$bultosOPeso) {
            return 0;
        }

        if($cpCordonEntrega->getSubZona()=='INT'){

            $precio = $this->getPrecioMaxBultosOPeso($bultosOPeso, $cliente, $servicio, $cpCordonRetiro->getCordon(), $cpCordonEntrega->getCordon());
            //$costoFlete = $precio->getPrecio();

            $kms = $cpCordonEntrega->getKms();
            $kmsAdicionales = $cpCordonEntrega->getKmsAdicionales();

            if($cpCordonEntrega->getTipoServicio()=='REDESPACHO'){
                $precio = $this->getPrecioMaxBultosOPeso($bultosOPeso, $cliente, $servicio, $cpCordonRetiro->getCordon(), $cpCordonEntrega->getCordon());
                $valorTroncal = $precio->getPrecio()*$cpCordonEntrega->getKms();
                //die("COSTO TRONCAL: ".$valorTroncal*$cpCordonEntrega->getKms());
                $valorRedespacho = $this->calcularPrecioPorExcesoRangoInterior($bultosOPeso, $bultosOPeso - $precio->getRango(), $cliente, $servicio, $cpCordonRetiro->getCordon(), $cpCordonEntrega->getCordon(), $cpCordonEntrega->getTipoServicio());
                $valorRedespacho = $valorRedespacho*$cpCordonEntrega->getKmsAdicionales();
                //die("COSTO REDESPACHO: ".$valorRedespacho*$cpCordonEntrega->getKmsAdicionales());
                return $valorTroncal+$valorRedespacho;
            }else{
                $valorPuertaPuerta = 0;
                $precio = $this->getPrecioMaxBultosOPeso($bultosOPeso, $cliente, $servicio, $cpCordonRetiro->getCordon(), $cpCordonEntrega->getCordon());
                $valorPuertaPuerta = $precio->getPrecio()*$cpCordonEntrega->getKms();
                if ($bultosOPeso <= $cliente->getKgFijoPuertaPuerta()) {
                    $valorPuertaPuerta += $cliente->getImporteFijoPuertaPuerta();
                }else{
                    $excedentePuertaApuerta = $this->calcularPrecioPorExcesoRangoInteriorPuertaPuerta($bultosOPeso, $bultosOPeso - $cliente->getKgFijoPuertaPuerta(), $cliente, $servicio, $cpCordonRetiro->getCordon(), $cpCordonEntrega->getCordon(), $cpCordonEntrega->getTipoServicio());
                    $valorPuertaPuerta = $precio->getPrecio()*$cpCordonEntrega->getKms();
                    //die("EXCEDENTE: ".$excedentePuertaApuerta);
                    //die("VALOR P TO P: ".$precio->getPrecio()." * KMS: ".$cpCordonEntrega->getKms());
                    //die("VALOR P T P: ".$valorPuertaPuerta." - IMPORTE FIJO: ".$cliente->getImporteFijoPuertaPuerta()." Excedente: ".$excedentePuertaApuerta);
                    $valorPuertaPuerta += $cliente->getImporteFijoPuertaPuerta()+$excedentePuertaApuerta;
                }
                return $valorPuertaPuerta;
            }
        }else if($cpCordonEntrega->getSubZona()=='GBA'){
            $precio = $this->getPrecioMaxBultosOPeso($bultosOPeso, $cliente, $servicio, $cpCordonRetiro->getCordon(), $cpCordonEntrega->getCordon());
            $costoFlete = $precio->getPrecio();
            //die($costoFlete.'*'.$kms);
            return $costoFlete*$kms;
        }else{
            $costoFlete = 0;
            $precio = $this->getPrecioMaxBultosOPeso($bultosOPeso, $cliente, $servicio, $cpCordonRetiro->getCordon(), $cpCordonEntrega->getCordon());
            if($precio) {
                $costoFlete = $precio->getPrecio();
                //die("BULTOS O PESO: ".$bultosOPeso.'>'." RANGO: ".$precio->getRango());
                if ($bultosOPeso > $precio->getRango()) {
                    //SACO ESTA LINEA PARA QUE TOME EL RANGO POR EL PESO COMPLETO NO POR LA DIFERENCIA DE $bultosOPeso - $precio->getRango()
                    //$costoFlete += $this->calcularPrecioPorExcesoRango($bultosOPeso - $precio->getRango(), $cliente, $servicio, $cpCordonRetiro->getCordon(), $cpCordonEntrega->getCordon());
                    $costoFlete += $this->calcularPrecioPorExcesoRango($bultosOPeso, $bultosOPeso - $precio->getRango(), $cliente, $servicio, $cpCordonRetiro->getCordon(), $cpCordonEntrega->getCordon());
                }
            }
            return $costoFlete;
        }

    }

    /**
     * @param $bultosOPesoExcedente
     * @param $cliente
     * @param $servicio
     * @param $cordonRetiro
     * @param $cordonEntrega
     *
     * @return float
     */
    private function calcularPrecioPorExcesoRango($bultosOPesoExcedente, $pesoExceso ,$cliente, $servicio, $cordonRetiro, $cordonEntrega){

        //die("PESO: ".$bultosOPesoExcedente.' exceso: '.$pesoExceso);

        $repoRango = $this->em->getRepository('PresisGeneralBundle:PesoRangoExcedente')->getPrecios($cliente);

        if ($repoRango){
            $criteria=Criteria::create()
                ->where(Criteria::expr()->gte("rangoHasta",$bultosOPesoExcedente))
                ->andWhere(Criteria::expr()->eq("cordonEntrega",$cordonEntrega))
                ->andWhere(Criteria::expr()->eq("cordonRetiro",$cordonRetiro))
                ->andWhere(Criteria::expr()->eq("servicio",$servicio))
                ->orderBy(Array("rangoHasta"=>"asc"))
                ->setMaxResults(1);

            $precios = $repoRango->matching($criteria);

            $precio=$precios->get(0);

            $excedente = 0;

            //die("DDD: ".$precio->getCostoRangoExcedente());

            if($precio) {
                $excedente = $pesoExceso*$precio->getCostoRangoExcedente();
                //$excedente = ceil($bultoExcedente / $repoRango->getRangoHasta()) * $repoRango->getCostoRangoExcedente();
            }
            return $excedente;

        }else{

            return -1;
        }
    }

    /**
     * @param $bultosOPesoExcedente
     * @param $cliente
     * @param $servicio
     * @param $cordonRetiro
     * @param $cordonEntrega
     *
     * @return float
     */
    private function calcularPrecioPorExcesoRangoInterior($bultosOPesoExcedente, $pesoExceso ,$cliente, $servicio, $cordonRetiro, $cordonEntrega, $tipoServicio){

        //die("PESO: ".$bultosOPesoExcedente.' exceso: '.$pesoExceso);

        $repoRango = $this->em->getRepository('PresisGeneralBundle:PesoRangoExcedente')->getPrecios($cliente);

        if ($repoRango){
            $criteria=Criteria::create()
                ->where(Criteria::expr()->gte("rangoHasta",$bultosOPesoExcedente))
                ->andWhere(Criteria::expr()->eq("cordonEntrega",$cordonEntrega))
                ->andWhere(Criteria::expr()->eq("cordonRetiro",$cordonRetiro))
                ->andWhere(Criteria::expr()->eq("servicio",$servicio))
                ->andWhere(Criteria::expr()->eq("tipoServicio",$tipoServicio))
                ->orderBy(Array("rangoHasta"=>"asc"))
                ->setMaxResults(1);

            $precios = $repoRango->matching($criteria);

            $precio=$precios->get(0);

            $excedente = 0;

            if($precio) {
                $excedente = $precio->getCostoRangoExcedente();
            }
            return $excedente;

        }else{

            return -1;
        }
    }

    /**
     * @param $bultosOPesoExcedente
     * @param $cliente
     * @param $servicio
     * @param $cordonRetiro
     * @param $cordonEntrega
     *
     * @return float
     */
    private function calcularPrecioPorExcesoRangoInteriorPuertaPuerta($bultosOPesoExcedente, $pesoExceso ,$cliente, $servicio, $cordonRetiro, $cordonEntrega, $tipoServicio){

        //die("PESO: ".$bultosOPesoExcedente.' exceso: '.$pesoExceso);

        $repoRango = $this->em->getRepository('PresisGeneralBundle:PesoRangoExcedente')->getPrecios($cliente);

        if ($repoRango){
            $criteria=Criteria::create()
                ->where(Criteria::expr()->gte("rangoHasta",$bultosOPesoExcedente))
                ->andWhere(Criteria::expr()->eq("cordonEntrega",$cordonEntrega))
                ->andWhere(Criteria::expr()->eq("cordonRetiro",$cordonRetiro))
                ->andWhere(Criteria::expr()->eq("servicio",$servicio))
                ->andWhere(Criteria::expr()->eq("tipoServicio",$tipoServicio))
                ->orderBy(Array("rangoHasta"=>"asc"))
                ->setMaxResults(1);

            $precios = $repoRango->matching($criteria);

            $precio=$precios->get(0);

            $excedente = 0;

            //die("DDD: ".$precio->getCostoRangoExcedente());

            if($precio) {
                $excedente = $pesoExceso*$precio->getCostoRangoExcedente();
                //$excedente = ceil($bultoExcedente / $repoRango->getRangoHasta()) * $repoRango->getCostoRangoExcedente();
                //die("PESO EXCEDENTE: ".$pesoExceso."* PRECIO: ".$precio->getCostoRangoExcedente());
            }
            return $excedente;

        }else{

            return -1;
        }
    }
}