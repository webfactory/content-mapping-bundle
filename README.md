# content-mapping-bundle #

Symfony bundle for [webfactory/content-mapping](https://github.com/webfactory/content-mapping). If you configure your
Synchronizers as services, you can use the provided console commands to list and start them. This is useful e.g. for
cronjobs.


## Installation ##

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


## Usage ##

First, register your Synchronizers as a service, e.g. in your services.xml:

```xml
<!-- Synchronizer for MyEntity --->
<service class="Webfactory\ContentMapping\Synchronizer">
    <!-- SourceAdapter -->
    <argument type="service">
        <service class="Webfactory\ContentMapping\SourceAdapter\Doctrine\GenericDoctrineSourceAdapter">
            <!-- Doctrine Repository -->
            <argument type="service">
                <service class="MyVendor\MyBundle\Entity\MyEntityRepository" factory-service="doctrine.orm.entity_manager" factory-method="getRepository">
                    <argument>MyVendorMyEntityBundle:MyEntity</argument>
                </service>
            </argument>
            <!-- Name of the repository method to query -->
            <argument type="string">findForSolrIndex</argument>
        </service>
    </argument>

    <!-- Mapper -->
    <argument type="service">
        <service class="MyVendor\MyBundle\ContentMapping\MyEntityMapper" />
    </argument>

    <!-- DestinationAdapter -->
    <argument type="service" id="contentmapping.destinationadapter.solarium"/>

    <!-- PSR3-logger -->
    <argument type="service" id="logger" />
    <tag name="monolog.logger" channel="solr" />
    
    <!-- Tag to mark the service as a Synchronizer -->
    <tag name="contentmapping.synchronizer" objectclass="\JugendFuerEuropa\Bundle\JugendInAktionBundle\Entity\Mitarbeiter" />
</service>

<!-- other Synchronizers --->
```

If you've tagged your service as in the example above, you can use the console command
  
    app/console content-mapping:list-synchronizers

to list your registered synchronizers denoted by their objectclass, and

    app/console content-mapping:synchronize

to start them. Use `-o objectclass` to start only the Synchronizer for the `objectclass` and `-f` to force updates in
the destination systems even if no changes are detected. Be aware that `objectclass` is not the name of your entity class
you'd like to synchronize, but the value you defined in the service definition (see above).


## Credits, Copyright and License ##

This project was started at webfactory GmbH, Bonn.

- <https://www.webfactory.de>
- <https://twitter.com/webfactory>

Copyright 2015-2018 webfactory GmbH, Bonn. Code released under [the MIT license](LICENSE).
