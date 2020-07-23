<?php

namespace Presis\ServicioBundle\Controller;

use Presis\ServicioBundle\Entity\ListaFile;
use Presis\ServicioBundle\Entity\Precio;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class ListaFileController extends Controller
{
    public function showAction($id,$lista){

        echo "";
        $em = $this->getDoctrine()->getManager();

        $document = $em->getRepository('PresisServicioBundle:ListaFile')->find($id);
        //$precios = $em->getRepository('PresisServicioBundle:Precio');
        //$precios->de

        $phpExcelObject = $this->get('phpexcel')->createPHPExcelObject($document->getWebPath());
        $worksheet = $phpExcelObject->getSheet(0);
        $rownum=0;
        $arrrango=array();
        $queryBuilder = $em
            ->createQueryBuilder()
            ->delete('PresisServicioBundle:Precio', 'p')
            ->where('p.lista = :lista')
            ->setParameter(':lista',$lista);

        $queryBuilder->getQuery()->execute();

        foreach ($worksheet->getRowIterator() as $row) {
            $cellIterator = $row->getCellIterator();
            //$cellIterator->setIterateOnlyExistingCells(false); // Loop all cells, even if it is not set

            $cellnum=0;

            $pre=null;
            foreach ($cellIterator as $cell) {
                if ($rownum==0) {
                    if ($cellnum>=3){
                        
                        $arrrango[]=$cell->getValue();
                    }
                }else{
                    if ($cellnum==0){
                        $pre=new Precio();
                        $pre->setCordonEntrega($em->getRepository("PresisServicioBundle:Cordon")->findOneByDescripcion($cell->getValue()));

                    }
                    if ($cellnum==1){

                        $pre->setCordonRetiro($em->getRepository("PresisServicioBundle:Cordon")->findOneByDescripcion($cell->getValue()));

                    }
                    if ($cellnum==2){

                        $pre->setServicio($em->getRepository("PresisServicioBundle:Servicio")->findOneByCodServ($cell->getValue()));

                    }
                    if ($cellnum==3){
                        $pre->setRango($arrrango[$cellnum-3]);
                        $pre->setPrecio($cell->getValue());

                    }
                    if ($cellnum==3){
                        $pre->setRango($arrrango[$cellnum-3]);
                        $pre->setPrecio($cell->getValue());
                        $pre->setLista($lista);
                        $em->persist($pre);
                    }
                    if ($cellnum>3){
                        $pre2=new Precio();
                        $pre2->setPrecio($cell->getValue());
                        $pre2->setRango($arrrango[$cellnum-3]);
                        $pre2->setCordonEntrega($pre->getCordonEntrega());
                        $pre2->setLista($lista);
                        $pre2->setCordonRetiro($pre->getCordonRetiro());
                        $pre2->setServicio($pre->getServicio());
                        $em->persist($pre2);
                    }
                }

                $cellnum++;

            }
            $rownum++;
        }
        $em->flush();
        $flash = $this->get('braincrafted_bootstrap.flash');
        $flash->success('Lista de precios importada con éxito.');
        return $this->redirect($this->generateUrl('lista_upload'));
    }

    public function uploadAction(Request $request){

        $user=$this->get('security.context')->getToken()->getUser();

        $document = new ListaFile();

        if($user->hasRole('ROLE_ADMIN') || $user->hasRole('ROLE_ADMINISTRACION')){
            $form = $this->createFormBuilder($document)
                ->add('name','text',array('label'=>'Nombre'))
                ->add('lista')
                ->add('file','file',array('label'=>'Archivo'))
                ->add('submit', 'submit', array('label' => 'Subir archivo','attr' => array('class'=> 'btn btn-success')))
                ->getForm();
        }else{
            $form = $this->createFormBuilder($document)
                ->add('name','text',array('label'=>'Nombre'))
                ->add('lista','entity', array(
                    'class' => 'PresisGeneralBundle:Cliente',
                    'choices' => $user->getVendedor()->getClientes(),
                ))
            ->add('file','file',array('label'=>'Archivo'))
                ->add('submit', 'submit', array('label' => 'Subir archivo','attr' => array('class'=> 'btn btn-success')))
                ->getForm();
        }

        $form->handleRequest($request);

        if ($form->isValid()) {
            $user=$this->get('security.context')->getToken()->getUser();
            $em = $this->getDoctrine()->getManager();

            $em->persist($document);
            $em->flush();

            //     $form2=$form2->add('submit','submit');
            //  $form2=$form2->getForm();

            return $response = $this->forward('PresisServicioBundle:ListaFile:show', array(
                'id'  => $document->getId(),
                'lista' =>$document->getLista()

            ));
        }

        return $this->render('PresisServicioBundle:Lista:upload.html.twig', array(
            'entity' => $document,
            'form'   => $form->createView(),
        ));
    }
}
