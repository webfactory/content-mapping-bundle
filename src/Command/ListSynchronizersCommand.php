<?php

namespace Webfactory\ContentMappingBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Webfactory\ContentMappingBundle\Synchronizer\Registry;

/**
 * Lists the available Synchronizers, denoted by the object class they synchronize.
 */
class ListSynchronizersCommand extends Command
{
    /**
     * @var Registry
     */
    private $synchronizerRegistry;

    /**
     * @param Registry $synchronizerRegistry
     */
    public function __construct(Registry $synchronizerRegistry)
    {
        parent::__construct();
        $this->synchronizerRegistry = $synchronizerRegistry;
    }

    /**
     * {@inheritDoc}
     */
    protected function configure()
    {
        $this->setName('content-mapping:list-synchronizers')
             ->setDescription('Lists the available synchronizers');
    }

    /**
     * {@inheritDoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        foreach ($this->synchronizerRegistry->getObjectclasses() as $objectclass) {
            $output->writeln($objectclass);
        }
    }
}
