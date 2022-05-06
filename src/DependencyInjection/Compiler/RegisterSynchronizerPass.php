<?php
/*
 * (c) webfactory GmbH <info@webfactory.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Webfactory\ContentMappingBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Fills the Synchronizer\Registry with services tagged as "contentmapping.synchronizer".
 */
final class RegisterSynchronizerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        $definition = $container->getDefinition('contentmapping.synchronizer_registry');

        foreach ($container->findTaggedServiceIds('contentmapping.synchronizer') as $id => $tags) {
            foreach ($tags as $tag) {
                if (!isset($tag['objectclass'])) {
                    throw new \Exception('The contentmapping.synchronizer tag requires the objectclass attribute.');
                }

                $definition->addMethodCall('addSynchronizer', [$tag['objectclass'], $id]);

                // Prevents Symfony from optimizing these services away if they're anonymous
                $container->getDefinition($id)->setPublic(true);
            }
        }
    }
}
