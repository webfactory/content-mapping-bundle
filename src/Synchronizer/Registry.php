<?php
/*
 * (c) webfactory GmbH <info@webfactory.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Webfactory\ContentMappingBundle\Synchronizer;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Webfactory\ContentMapping\Synchronizer;

/**
 * Registry, that holds the id of the matching service for an objectclass-name. That way we can fetch (and perhaps
 * instantiate with high costs) services late at runtime.
 *
 * @final by default.
 */
final class Registry
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var array(string objectClass => string serviceId)
     */
    private $services = array();

    /**
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @param string $objectclass
     * @param string $serviceId
     */
    public function addSynchronizer($objectclass, $serviceId)
    {
        $this->services[$objectclass] = $serviceId;
    }

    /**
     * @return string[]
     */
    public function getObjectclasses()
    {
        return array_keys($this->services);
    }

    /**
     * @param string $objectclass The objectclass to retrieve the Synchronizer for.
     * @return Synchronizer
     */
    public function getSynchronizer($objectclass)
    {
        if (array_key_exists($objectclass, $this->services) === false) {
            throw new \RuntimeException('No Synchronizer for objectclass "' . $objectclass . '" configured.');
        }

        $serviceId = $this->services[$objectclass];
        return $this->container->get($serviceId);
    }
}
