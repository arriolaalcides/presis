<?php

namespace Presis\ImportarBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('ImportarBundle:Default:index.html.twig');
    }

    public function downloadAction($filename)
    {
        $request = $this->get('request');
        $path = $this->get('kernel')->getRootDir(). "/web/download/";
        $content = file_get_contents($path.$filename);

        $response = new Response();

        //set headers
        $response->headers->set('Content-Type', 'mime/type');
        $response->headers->set('Content-Disposition', 'attachment;filename="'.$filename);

        $response->setContent($content);
        return $response;
    }
}
