<?php

use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Config\Loader\LoaderInterface;

class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = array(
            new Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
            new Symfony\Bundle\SecurityBundle\SecurityBundle(),
            new Symfony\Bundle\TwigBundle\TwigBundle(),
            new FOS\JsRoutingBundle\FOSJsRoutingBundle(),

            new Symfony\Bundle\MonologBundle\MonologBundle(),
            new Symfony\Bundle\SwiftmailerBundle\SwiftmailerBundle(),
            new Symfony\Bundle\AsseticBundle\AsseticBundle(),
            new Doctrine\Bundle\DoctrineBundle\DoctrineBundle(),
            new \FOS\UserBundle\FOSUserBundle(),
            new Sensio\Bundle\FrameworkExtraBundle\SensioFrameworkExtraBundle(),
            new Presis\ServicioBundle\PresisServicioBundle(),
            new Presis\GeneralBundle\PresisGeneralBundle(),
            new Presis\RetiroBundle\PresisRetiroBundle(),
            new Presis\UserBundle\PresisUserBundle(),
            new Presis\ApiBundle\PresisApiBundle(),
            new FOS\RestBundle\FOSRestBundle(),
            new JMS\SerializerBundle\JMSSerializerBundle(),
            new Doctrine\Bundle\FixturesBundle\DoctrineFixturesBundle(),
            new Presis\FixtureBundle\PresisFixtureBundle(),
            new Braincrafted\Bundle\BootstrapBundle\BraincraftedBootstrapBundle(),
            new RaulFraile\Bundle\LadybugBundle\RaulFraileLadybugBundle(),
            new Liuggio\ExcelBundle\LiuggioExcelBundle(),
            new Nelmio\ApiDocBundle\NelmioApiDocBundle(),
            new Ps\PdfBundle\PsPdfBundle(),
            new Knp\Bundle\MenuBundle\KnpMenuBundle(),
            new Presis\GuiaBundle\PresisGuiaBundle(),
            new Presis\DistribuidorBundle\DistribuidorBundle(),
            new Presis\EstadoBundle\EstadoBundle(),
            new Presis\TrackerBundle\TrackerBundle(),
            new Presis\TipoDNIBundle\TipoDNIBundle(),
            new Presis\ReclamoBundle\ReclamoBundle(),
            new Presis\DatosEnviosBundle\DatosEnviosBundle(),
            new Presis\RemitentesBundle\RemitentesBundle(),
            new Presis\DestinatariosBundle\DestinatariosBundle(),
            new Presis\ExpresoBundle\ExpresoBundle(),
            new Presis\RecorridoBundle\RecorridoBundle(),
            new Presis\RendicionBundle\RendicionBundle(),
            new Presis\PlanillaBundle\PlanillaBundle(),
            new Presis\MenuBundle\PresisMenuBundle(),
            new Presis\ImportarBundle\ImportarBundle(),
            new SC\DatetimepickerBundle\SCDatetimepickerBundle(),
            new Presis\CecosBundle\CecosBundle(),
            new Presis\ConstanciaRetiroBundle\ConstanciaRetiroBundle(),
            new Genemu\Bundle\FormBundle\GenemuFormBundle(),
            new Presis\FirmasBundle\FirmasBundle(),
            new Presis\FotoBundle\FotoBundle(),
            new Presis\GestionCelBundle\GestionCelBundle(),
            new Presis\MovistarBundle\MovistarBundle(),
        );

        if (in_array($this->getEnvironment(), array('dev', 'test'))) {
            $bundles[] = new Symfony\Bundle\WebProfilerBundle\WebProfilerBundle();
            $bundles[] = new Sensio\Bundle\DistributionBundle\SensioDistributionBundle();
            $bundles[] = new Sensio\Bundle\GeneratorBundle\SensioGeneratorBundle();
        }

        return $bundles;
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load(__DIR__.'/config/config_'.$this->getEnvironment().'.yml');
    }

    public function __construct($environment, $debug)
    {
        date_default_timezone_set( 'America/Argentina/Buenos_Aires' );
        parent::__construct($environment, $debug);
    }
}
