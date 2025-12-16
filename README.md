# content-mapping-bundle

Symfony bundle for [webfactory/content-mapping](https://github.com/webfactory/content-mapping). If you configure your
Synchronizers as services, you can use the provided console commands to list and start them. This is useful e.g. for
cronjobs.

## Installation

Install the package via composer

    composer require webfactory/content-mapping-bundle

and enable the bundle in your app kernel:
    
```php    
<?php
// app/AppKernel.php

public function registerBundles()
{
    // ...
    $bundles[] = new Webfactory\ContentMappingBundle\WebfactoryContentMappingBundle();
    // ...
}
```

## Usage

First, register your Synchronizers as a service, e.g. in your `config/services.php`:

```php
<?php

use MyVendor\MyBundle\ContentMapping\MyEntityMapper;
use MyVendor\MyBundle\Entity\MyEntity;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Webfactory\ContentMapping\SourceAdapter\Doctrine\GenericDoctrineSourceAdapter;
use Webfactory\ContentMapping\Synchronizer;
use function Symfony\Component\DependencyInjection\Loader\Configurator\service;

return static function (ContainerConfigurator $container): void {
    $services = $container->services();

    $services->set('my_entity.source_adapter', GenericDoctrineSourceAdapter::class)
        ->args([
            service('doctrine.orm.entity_manager')->call('getRepository', [MyEntity::class]), // Doctrine Repository
            'findForSolrIndex', // Name of the repository method to query
        ]);

    $services->set(MyEntityMapper::class)
        ->autowire();

    $services->set('my_entity.synchronizer', Synchronizer::class)
        ->args([
            service('my_entity.source_adapter'), // SourceAdapter
            service(MyEntityMapper::class), // Mapper
            service('contentmapping.destinationadapter.solarium'), // DestinationAdapter
            service('logger'), // PSR3-logger
        ])
        ->tag('monolog.logger', ['channel' => 'solr'])
        ->tag('contentmapping.synchronizer', ['objectclass' => MyEntity::class]);

    // other Synchronizers...
};
```

If you've tagged your service as in the example above, you can use the console command
  
    app/console content-mapping:list-synchronizers

to list your registered synchronizers denoted by their objectclass, and

    app/console content-mapping:synchronize

to start them. Use `-o objectclass` to start only the Synchronizer for the `objectclass` and `-f` to force updates in
the destination systems even if no changes are detected. Be aware that `objectclass` is not the name of your entity class
you'd like to synchronize, but the value you defined in the service definition (see above). Note that backslashes in your
`objectclass` need to be escaped (with backslashes).

## Credits, Copyright and License

This project was started at webfactory GmbH, Bonn.

- <https://www.webfactory.de>

Copyright 2015-2025 webfactory GmbH, Bonn. Code released under [the MIT license](LICENSE).
