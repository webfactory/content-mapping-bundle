<?php

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Webfactory\ContentMappingBundle\Command\ListSynchronizersCommand;
use Webfactory\ContentMappingBundle\Command\SynchronizeCommand;
use Webfactory\ContentMappingBundle\Synchronizer\Registry;

return static function(ContainerConfigurator $container) {
    $services = $container->services();

    $services->set('webfactory_content_mapping.command.synchronize_command', SynchronizeCommand::class)
        ->args([
            service('contentmapping.synchronizer_registry'),
            service('logger'),
        ])
        ->tag('monolog.logger', ['channel' => 'contentmapping'])
        ->tag('console.command');

    $services->set('webfactory_content_mapping.command.list_synchronizers_command', ListSynchronizersCommand::class)
        ->args([service('contentmapping.synchronizer_registry')])
        ->tag('console.command');

    $services->set('contentmapping.synchronizer_registry', Registry::class)
        ->args([service('service_container')]);
};
