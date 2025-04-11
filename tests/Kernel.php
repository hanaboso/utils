<?php declare(strict_types=1);

namespace UtilsTests;

use Exception;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;
use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

/**
 * Class Kernel
 *
 * @package UtilsTests
 */
final class Kernel extends BaseKernel
{

    use MicroKernelTrait;

    public const string CONFIG_EXTS = '.{yaml}';

    /**
     * @return BundleInterface[]
     */
    public function registerBundles(): iterable
    {
        $contents = [
            FrameworkBundle::class => ['all' => TRUE],

        ];
        foreach ($contents as $class => $envs) {
            $envs;

            yield new $class();
        }
    }

    /**
     * @param ContainerBuilder $container
     * @param LoaderInterface  $loader
     *
     * @throws Exception
     */
    protected function configureContainer(ContainerBuilder $container, LoaderInterface $loader): void
    {
        $container->setParameter('container.dumper.inline_class_loader', TRUE);
        $loader->load(sprintf('%s/*%s', $this->getConfigDir(), self::CONFIG_EXTS), 'glob');
    }

    /**
     * @param RoutingConfigurator $routes
     */
    protected function configureRoutes(RoutingConfigurator $routes): void
    {
        $routes->import(sprintf('%s/*%s', $this->getRoutingDir(), self::CONFIG_EXTS), 'glob');
    }

    /**
     * @return string
     */
    private function getConfigDir(): string
    {
        return sprintf('%s/tests/testApp/config', $this->getProjectDir());
    }

    /**
     * @return string
     */
    private function getRoutingDir(): string
    {
        return sprintf('%s/tests/testApp/routing', $this->getProjectDir());
    }

}
