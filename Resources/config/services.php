<?php

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use function Symfony\Component\DependencyInjection\Loader\Configurator\service;
use Crisp\Bundle\Twig\CrispTwigExtension;
use Crisp\Bundle\Subscriber\CrispListener;

return static function (ContainerConfigurator $container) {
    $services = $container->services();

    $services->defaults()
        ->public(false);

    $services
        ->set('crisp.twig_extension', CrispTwigExtension::class)
        ->tag('twig.extension');

    $services
        ->set('crisp.listener', CrispListener::class)
        ->tag('kernel.event_listener', [
            'event'  => 'kernel.request',
            'method' => 'onKernelRequest',
        ])
        ->tag('kernel.event_listener', [
            'event'  => 'kernel.response',
            'method' => 'onKernelResponse',
        ])
        ->args([
            service('parameter_bag'),
            service('twig'),
            service('request_stack'),
        ]);
};