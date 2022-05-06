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
    private $services = [];

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function addSynchronizer(string $objectclass, string $serviceId)
    {
        $this->services[$objectclass] = $serviceId;
    }

    /**
     * @return string[]
     */
    public function getObjectclasses(): array
    {
        return array_keys($this->services);
    }

    public function getSynchronizer(string $objectclass): Synchronizer
    {
        if (false === \array_key_exists($objectclass, $this->services)) {
            throw new \RuntimeException('No Synchronizer for objectclass "'.$objectclass.'" configured.');
        }

        $serviceId = $this->services[$objectclass];

        return $this->container->get($serviceId);
    }
}
