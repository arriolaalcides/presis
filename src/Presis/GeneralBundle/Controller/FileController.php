<?php

namespace Presis\GeneralBundle\Controller;

use Doctrine\Common\Collections\ArrayCollection;
use Presis\GeneralBundle\Entity\Document;
use Presis\GeneralBundle\Entity\ImpoTest;
use Presis\GeneralBundle\Form\ImpoTestType;
use Presis\RetiroBundle\Entity\Comprador;
use Presis\RetiroBundle\Entity\Retiro;
use Presis\RetiroBundle\Entity\Sender;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\Common\Collections\Criteria;

class FileController extends Controller
{
    protected function getPrecioManager()
    {
        return $this->container->get('presis_precio');
    }

    public function showAction($id){
        $em = $this->getDoctrine()->getManager();

        $document = $em->getRepository('PresisGeneralBundle:Document')->find($id);
        $phpExcelObject = $this->get('phpexcel')->createPHPExcelObject($document->getWebPath());
        $worksheet = $phpExcelObject->getSheet(0);
        $rownum = 0;
        $arrayCells=new ArrayCollection();
        $arrayValido=array();
        $arrayMensaje=array();

        $valcols=true;
        $totpeso=0;
        $peso=0;
        $reg=null;
        $rec=null;
        $idant=0;
        $codserv="";
        $codsucu="";
        $valido=true;
        $valitipo=true;
        $valdimen=true;
        $valpeso=true;
        $valcate=true;
        $clidoc=$document->getCliente();
        foreach ($worksheet->getRowIterator() as $row) {
            $tipo=-1;

            $cellnum = 0;

            $cellIterator = $row->getCellIterator();
            $cellIterator->setIterateOnlyExistingCells(false); // Loop all cells, even if it is not set


            foreach ($cellIterator as $cell) {

                if ($rownum==0){


                    if ($cellnum==0){
                        if ($cell->getValue()<>'id'){
                            $valcols=false;
                        }
                    }
                    if ($cellnum==1){
                        if ($cell->getValue()<>'ape_nom'){
                            $valcols=false;
                        }
                    }
                    if ($cellnum==2){
                        if ($cell->getValue()<>'calle'){
                            $valcols=false;
                        }
                    }
                    if ($cellnum==3){
                        if ($cell->getValue()<>'altura'){
                            $valcols=false;
                        }
                    }
                    if ($cellnum==4){
                        if ($cell->getValue()<>'piso'){
                            $valcols=false;
                        }
                    }
                    if ($cellnum==5){
                        if ($cell->getValue()<>'dpto'){
                            $valcols=false;
                        }
                    }
                    if ($cellnum==6){
                        if ($cell->getValue()<>'other_info'){
                            $valcols=false;
                        }
                    }
                    if ($cellnum==7){
                        if ($cell->getValue()<>'cp'){
                            $valcols=false;
                        }
                    }

                    if ($cellnum==8){
                        if ($cell->getValue()<>'peso'){
                            $valcols=false;
                        }
                    }
                    if ($cellnum==9){
                        if ($cell->getValue()<>'alto'){
                            $valcols=false;
                        }
                    }
                    if ($cellnum==10){
                        if ($cell->getValue()<>'ancho'){
                            $valcols=false;
                        }
                    }
                    if ($cellnum==11){
                        if ($cell->getValue()<>'largo'){
                            $valcols=false;
                        }
                    }
                    if ($cellnum==12){
                        if ($cell->getValue()<>'Categoria'){
                            $valcols=false;
                        }
                    }
                    if ($cellnum==13){
                        if ($cell->getValue()<>'sucursal'){
                            $valcols=false;
                        }
                    }
                    if ($cellnum==14){
                        if ($cell->getValue()<>'fragil'){
                            $valcols=false;
                        }
                    }
                    if ($cellnum==15){
                        if ($cell->getValue()<>'codserv'){
                            $valcols=false;
                        }
                    }
                    if ($cellnum==16){
                        if ($cell->getValue()<>'email'){
                            $valcols=false;
                        }
                    }
                    if ($cellnum==17){
                        if ($cell->getValue()<>'celular'){
                            $valcols=false;
                        }
                    }
                    if ($cellnum==18){
                        if ($cell->getValue()<>'franja'){
                            $valcols=false;
                        }
                    }

                    $cellnum+=1;
                }

                if ($rownum <> 0) {
                    $pesotipo = $worksheet->getCell('I' . ($rownum+1))->getValue();
                    $altotipo = $worksheet->getCell('J' . ($rownum+1))->getValue();
                    $anchotipo = $worksheet->getCell('K' . ($rownum+1))->getValue();
                    $largotipo = $worksheet->getCell('L' . ($rownum+1))->getValue();
                    $catetipo = $worksheet->getCell('M' . ($rownum+1))->getValue();
                    $tipo=0;
                    if (!($pesotipo===NULL) && $altotipo===NULL && $anchotipo===NULL && $largotipo===NULL && $catetipo===NULL){
                        $tipo=2;

                    }
                    if ($pesotipo===NULL && !($altotipo===NULL) && !($anchotipo===NULL) && !($largotipo===NULL) && $catetipo===NULL){
                        $tipo=1;

                    }
                    if (($pesotipo===NULL) && $altotipo===NULL && $anchotipo===NULL && $largotipo===NULL && !($catetipo===NULL)){
                        $tipo=3;

                    }
                    if (!($pesotipo===NULL) && !($altotipo===NULL) && !($anchotipo===NULL) && !($largotipo===NULL) && $catetipo===NULL){
                        $tipo=4;

                    }
            //        var_dump($tipo);

                    if ($valcols) {

                        if ($cellnum == 0) {
                            $id=$cell->getValue();


                            if ($id<>$idant){
                                if ($idant==0){
                                 $numirow=0;
                                }else {
                                    $numirow = $idant - 1;
                                }

                                if (($reg)){

                                    $arrayMensaje[$numirow]="";
                                    $reg->setPeso(round($totpeso,3));
                                    $reg->setComprador($rec);
                                    if (!($reg->getComprador()->getApenom())) {
                                        $valido = false;
                                        $arrayMensaje[$numirow] = "Debe completar el apellido";
                                    }
                                    if (!$valpeso){
                                        $valido=false;
                                        $arrayMensaje[$numirow] = "Error en el peso";

                                    }
                                    if (!$valcate && $valido){
                                        $valido=false;
                                        $arrayMensaje[$numirow] = "Error en la categoria";

                                    }
                                    if (!$valdimen && $valido){
                                        $valido=false;
                                        $arrayMensaje[$numirow] = "Error en las dimensiones";

                                    }
                                    if ($valido) {
                                        if (!($reg->getComprador()->getCalle())) {
                                            $valido = false;
                                            $arrayMensaje[$numirow] = "Debe completar la calle";
                                        }
                                    }
                                    if ($valido) {

                                        if (!($reg->getComprador()->getAltura())) {
                                            $valido = false;
                                            $arrayMensaje[$numirow] = "Debe completar la altura";
                                        }
                                    }
                                    if ($valido) {
                                        if (!($reg->getComprador()->getCp())) {
                                            $valido = false;
                                            $arrayMensaje[$numirow] = "Debe completar el cp";
                                        }
                                    }

                                    $user=$this->get('security.context')->getToken()->getUser();
                                    if ($valido) {
                                        $sucuvv = $this->getPrecioManager()->validarSucursal($clidoc, $codsucu);
                                        if (!$sucuvv) {
                                            $valido = false;
                                            $arrayMensaje[$numirow] = "Sucursal invalida";
                                        } else {
                                            $reg->setSucursal($sucuvv);
                                        }
                                    }
                                    if ($valido) {
                                        $servv = $this->getPrecioManager()->validarServicio($user, $reg->getComprador()->getCp(), $reg->getSucursal()->getCodSuc(), $codserv);
                                        if (!$servv) {
                                            $valido = false;
                                            $arrayMensaje[$numirow] = "Servicio Invalido";
                                        }else{
                                            $reg->setServicio($servv);
                                        }
                                    }
                                    if ($valido) {
                                        $precio = $this->getPrecioManager()->calcularPrecio2($reg->getServicio(), $reg->getPeso(), $clidoc, $reg->getSucursal(), $reg->getComprador()->getCp());
                                        $reg->setPrecio($precio);
                                        // $reg->setPrecio($precio);
                                        if ($precio==-1){
                                            $valido=false;
                                            $arrayMensaje[$numirow] = "Ha ocurrido un error al calcular el precio";

                                        }
                                    }else{
                                        $precio=-1;
                                        $reg->setPrecio($precio);
                                        $valido=false;

                                    }
                                    //   $reg->setPrecio($precio);

                                    $arrayCells->add($reg);
                                    $arrayValido[$numirow]=$valido;

                                    //    echo $reg->getPrecio();
                                    //  $em->persist($reg);
                                    // $em->flush();
                                    $codserv="";
                                    $codsucu="";
                                    $valido=true;
                                    $totpeso=0;
                                    $valitipo=true;
                                    $valdimen=true;
                                    $valpeso=true;
                                    $valcate=true;
                                    $tipo=0;
                                    $peso=0;
                                }
                                $reg = new Retiro();
                                $rec=new Comprador();
                                $idant=$id;
                            }
                        }
                        if ($cellnum == 1) {
                            $rec->setApenom($cell->getValue());

                        }
                        if ($cellnum == 2) {
                            $rec->setCalle($cell->getValue());

                        }
                        if ($cellnum == 3) {
                            $rec->setAltura($cell->getValue());

                        }
                        if ($cellnum == 4) {
                            $rec->setPiso($cell->getValue());

                        }
                        if ($cellnum == 5) {

                            $rec->setDpto($cell->getValue());

                        }
                        if ($cellnum == 6) {
                            $rec->setOtherInfo($cell->getValue());

                        }
                        if ($cellnum == 7) {
                            $rec->setCp($cell->getValue());


                        }

                        if ($cellnum == 8) {
                           if ($tipo==2) {

                               if ($this->getPrecioManager()->validarPeso($cell->getValue())) {
                                   $totpeso = $totpeso + $cell->getValue();
                               }else {
                                   $valpeso = false;
                               }
                           }
                            if ($tipo==4) {

                                if ($this->getPrecioManager()->validarPeso($cell->getValue())) {
                                    $peso = $cell->getValue();
                                    $valpeso = true;
                                }else {
                                    $valpeso = false;
                                }
                            }

                        }
                        if ($cellnum == 9) {
                            if ($tipo==1 || $tipo==4) {
                                $dime1=$cell->getValue();
                            }


                        }
                        if ($cellnum == 10) {
                            if ($tipo==1 || $tipo==4) {
                                $dime2=$cell->getValue();
                            }


                        }
                        if ($cellnum == 11) {
                            if ($tipo==1) {
                                $dime3=$cell->getValue();
                                $dimensiones[0]["alto"]=$dime1;
                                $dimensiones[0]["largo"]=$dime2;
                                $dimensiones[0]["profundidad"]=$dime3;

                                if ($this->getPrecioManager()->validarDimensiones($dimensiones)){
                                    $totpeso=$totpeso+$this->getPrecioManager()->calcularPesoVolumetrico($dime1,$dime2,$dime3,$clidoc->getAforo());
                                }else{
                                    $valdimen=false;
                                }
                            }
                            if ($tipo==4) {
                                $dime3=$cell->getValue();
                                $dimensiones[0]["alto"]=$dime1;
                                $dimensiones[0]["largo"]=$dime2;
                                $dimensiones[0]["profundidad"]=$dime3;

                                if ($this->getPrecioManager()->validarDimensiones($dimensiones) && $valpeso){
                                    $totpeso=$totpeso+$this->getPrecioManager()->calcularMayorPeso($dime1,$dime2,$dime3,$peso,$clidoc->getAforo());
                                }else{
                                    $valdimen=false;
                                }
                            }


                        }
                        if ($cellnum == 12) {
                            if ($tipo==3) {
                                $cate=$cell->getValue();
                                $cate=$this->getPrecioManager()->validarCategoria($cate,$clidoc);
                                if ($cate){
                                    $totpeso=$totpeso+$cate->getPeso();
                                }else{
                                    $valcate=false;
                                }
                            }


                        }
                        if ($cellnum == 13) {

                            //ladybug_dump($cell->getValue());
                            //ladybug_dump($sucu);
                         //   $reg->setSucursal($cell->getValue());
                            $codsucu=$cell->getValue();

                        }
                        if ($cellnum == 14) {
                            $reg->setFragil($cell->getValue());

                        }
                        if ($cellnum == 15) {

                         //   $serv = $em->getRepository('PresisServicioBundle:Servicio')->findOneByCodServ($cell->getValue());

                           // $reg->setServicio($serv);
                            $codserv=$cell->getValue();


                        }
                        if ($cellnum == 16) {

                            //   $serv = $em->getRepository('PresisServicioBundle:Servicio')->findOneByCodServ($cell->getValue());

                            // $reg->setServicio($serv);
                            $email=$cell->getValue();


                        }
                        if ($cellnum == 17) {

                            //   $serv = $em->getRepository('PresisServicioBundle:Servicio')->findOneByCodServ($cell->getValue());

                            // $reg->setServicio($serv);
                            $celular=$cell->getValue();


                        }
                        if ($cellnum == 18) {

                            //   $serv = $em->getRepository('PresisServicioBundle:Servicio')->findOneByCodServ($cell->getValue());

                            // $reg->setServicio($serv);
                            $franja=$cell->getValue();


                        }

                        //  ladybug_dump($form2);
                        // die();
                        // $form2=$form2->get('impotests')->add('fila_'.$cellnum,new ImpoTestType($reg));
                        $cellnum += 1;



                    }else{
                        $valido=false;
                        $arrayMensaje[$rownum-1]="Columnas incorrectas";
                    }
                }


            }
            if ($rownum<>0) {

            }
            $rownum += 1;

        }


        if (($reg)){
            $numirow=$idant-1;
            $arrayMensaje[$numirow]="";
            $reg->setPeso(round($totpeso,3));
            $rec->setEmail($email);
            $rec->setCelular($celular);
            $reg->setComprador($rec);

            if (!$valpeso){
                $valido=false;
                $arrayMensaje[$numirow] = "Error en el peso";

            }
            if (!$valcate && $valido){
                $valido=false;
                $arrayMensaje[$numirow] = "Error en la categoria";

            }
            if (!$valdimen && $valido){
                $valido=false;
                $arrayMensaje[$numirow] = "Error en las dimensiones";

            }
            if (!($reg->getComprador()->getApenom())) {
                $valido = false;
                $arrayMensaje[$numirow] = "Debe completar el apellido";
            }
            if ($valido) {
                if (!($reg->getComprador()->getCalle())) {
                    $valido = false;
                    $arrayMensaje[$numirow] = "Debe completar la calle";
                }
            }
            if ($valido) {

                if (!($reg->getComprador()->getAltura())) {
                    $valido = false;
                    $arrayMensaje[$numirow] = "Debe completar la altura";
                }
            }
            if ($valido) {
                if (!($reg->getComprador()->getCp())) {
                    $valido = false;
                    $arrayMensaje[$numirow] = "Debe completar el cp";
                }
            }

            $user=$this->get('security.context')->getToken()->getUser();
            if ($valido) {
                $sucuvv = $this->getPrecioManager()->validarSucursal($clidoc, $codsucu);
                if (!$sucuvv) {
                    $valido = false;
                    $arrayMensaje[$numirow] = "Sucursal invalida";
                } else {
                    $reg->setSucursal($sucuvv);
                }
            }
            if ($valido) {
                $franjav = $this->getPrecioManager()->validarFranja($franja);
                if (!$franjav) {
                    $valido = false;
                    $arrayMensaje[$numirow] = "Franja invalida";
                } else {
                    $reg->setFranja($franjav);
                }
            }
            if ($valido) {
                $servv = $this->getPrecioManager()->validarServicio($user, $reg->getComprador()->getCp(), $reg->getSucursal()->getCodSuc(), $codserv);
                if (!$servv) {
                    $valido = false;
                    $arrayMensaje[$numirow] = "Servicio Invalido";
                }else{
                    $reg->setServicio($servv);
                }
            }
            if ($valido) {
                $precio = $this->getPrecioManager()->calcularPrecio($reg->getServicio(), $reg->getPeso(), $clidoc, $reg->getSucursal(), $reg->getComprador()->getCp());
                $reg->setPrecio($precio->getPrecio());
                $reg->setRango($precio->getRango());
                // $reg->setPrecio($precio);
                if ($precio===NULL){

                    $precio=-1;
                }else{
                    $precio=$precio->getPrecio();
                }
                if ($precio==-1){
                    $valido=false;
                    $arrayMensaje[$numirow] = "Ha ocurrido un error al calcular el precio";

                }
            }else{
                $precio=-1;
                $reg->setPrecio($precio);
                $valido=false;

            }
            //   $reg->setPrecio($precio);
            $codserv="";
            $codsucu="";
            $arrayCells->add($reg);
            $arrayValido[$numirow]=$valido;
            //    echo $reg->getPrecio();
            //  $em->persist($reg);
            // $em->flush();
        }

        return $this->render('PresisGeneralBundle:File:import.html.twig', array(
            'entities' => $arrayCells,
            'validos' =>$arrayValido,
            'mensajes'=>$arrayMensaje,

        ));
    }
    public function ajaxAction(){


        $em=$this->getDoctrine()->getManager();
        $documents=$em->getRepository("PresisGeneralBundle:Document")->findAll();
        $serializer = $this->get('jms_serializer');
        $json=$serializer->serialize($documents, "json");
        $datos='{"data":[';
        $json=substr($json,1,strlen($json));
        $json=$datos.$json."}";
        return new Response($json);


    }

    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('PresisGeneralBundle:Document')->findAll();

        return $this->render('PresisGeneralBundle:File:index.html.twig', array(
            'entities' => $entities,
        ));
    }

    public function showSelectedAction(Request $request){
        $em = $this->getDoctrine()->getManager();

        $checked=$request->get('check_list');
        $apellidos=$request->get('apellido_list');
        $validos=$request->get('valido2_list');
        $sucursales=$request->get('sucursal_list');
        $cps=$request->get('cp_list');
        $servicios=$request->get('servicio_list');
        $calles=$request->get('calle_list');
        $pesos=$request->get('peso_list');
        $fragiles=$request->get('fragil_list');
        $infos=$request->get('info_list');
        $alturas=$request->get('altura_list');
        $dptos=$request->get('dpto_list');
        $pisos=$request->get('piso_list');
        $email=$request->get('email_list');
        $celular=$request->get('celular_list');
        $franjaid=$request->get('franja_list');
        $user=$this->get('security.context')->getToken()->getUser();
        $cli=$user->getCliente();

        try{
        foreach ($checked as $selec){
            if ($validos[$selec]){

                $suc=$this->getPrecioManager()->validarSucursal($cli,$sucursales[$selec]);
                $serv=$this->getPrecioManager()->validarServicio($user,$cps[$selec],$sucursales[$selec],$servicios[$selec]);
                $precio=$this->getPrecioManager()->calcularPrecio($serv,$pesos[$selec],$cli,$suc,$cps[$selec]);

                if ($precio){
                    $ret=new Retiro();
                    $ret->setFragil($fragiles[$selec]);
                    $ret->setPrecio($precio->getPrecio());
                    $ret->setRango($precio->getRango());
                    $ret->setServicio($serv);
                    $ret->setSucursal($suc);
                    $ret->setCliente($cli);
                    $repoFranja = $this->getDoctrine()->getRepository('PresisRetiroBundle:FranjaEntrega');
                    $franja=$repoFranja->findOneById($franjaid[$selec]);
                    $ret->setFranja($franja);
                    $comprador=new Comprador();
                    $comprador->setOtherInfo($infos[$selec]);
                    $comprador->setAltura($alturas[$selec]);
                    $comprador->setApenom($apellidos[$selec]);
                    $comprador->setCalle($calles[$selec]);
                    $comprador->setCp($cps[$selec]);
                    $comprador->setDpto($dptos[$selec]);
                    $comprador->setPiso($pisos[$selec]);
                    $comprador->setEmail($email[$selec]);
                    $comprador->setCelular($celular[$selec]);
                    $ret->setComprador($comprador);
                    $repoCordon = $this->getDoctrine()->getRepository('PresisServicioBundle:CpCordon');
                    //$cordon=$repoCordon->findCordonInCp($cp);
                    $cpcordon=$repoCordon->findOneByCp($cps[$selec]);
                    $ret->setPrestador($this->getPrecioManager()->generatePrestador($cpcordon));
                    $ret->setCordonEntrega($cpcordon->getCordon());
                    $ret->setFechHora(new \DateTime('now'));
                    $ret->setPeso($pesos[$selec]);
                    $ret->setSender($this->getPrecioManager()->setSenderData($suc));
                    $em->persist($ret);
                }



            }
        }
            $em->flush();
        }catch (\Exception $e){
            die($e->getMessage());

        }

        return $this->redirect($this->generateUrl('retiro'));
    }

    public function uploadAction(Request $request){
        $document = new Document();
        $form = $this->createFormBuilder($document)
            ->add('name','text',array('label'=>'Nombre'))
            ->add('file','file',array('label'=>'Archivo'))
            ->add('submit', 'submit', array('label' => 'Subir archivo','attr' => array('class'=> 'btn btn-success')))
            ->getForm();

        $form->handleRequest($request);

        if ($form->isValid()) {
            $user=$this->get('security.context')->getToken()->getUser();
            $document->setCliente($user->getCliente());
            $em = $this->getDoctrine()->getManager();

                $em->persist($document);
                $em->flush();

       //     $form2=$form2->add('submit','submit');
          //  $form2=$form2->getForm();

            return $response = $this->forward('PresisGeneralBundle:File:show', array(
                'id'  => $document->getId(),

            ));
    }

        return $this->render('PresisGeneralBundle:File:new.html.twig', array(
            'entity' => $document,
            'form'   => $form->createView(),
        ));
    }
}
