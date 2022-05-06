<?php

namespace Webfactory\ContentMappingBundle\Command;

use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Webfactory\ContentMappingBundle\Synchronizer\Registry;

/**
 * Symfony Console Command to start the Synchronizers.
 */
final class SynchronizeCommand extends Command
{
    /**
     * @var Registry
     */
    private $synchronizerRegistry;

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(Registry $synchronizerRegistry, LoggerInterface $logger = null)
    {
        parent::__construct();
        $this->synchronizerRegistry = $synchronizerRegistry;
        $this->logger = $logger ?? new NullLogger();
    }

    protected function configure(): void
    {
        $this->setName('content-mapping:synchronize')
             ->setDescription('Starts the synchronizer(s).');
        $this->addOption(
            'force',
            'f',
            InputOption::VALUE_NONE,
            'Forces the update of each object in the target system, even if the source objects do not have changed'
        );
        $this->addOption(
            'only',
            'o',
            InputOption::VALUE_IS_ARRAY | InputOption::VALUE_OPTIONAL,
            'Only start the synchronizer for this objectclass. See all objectclasses with `bin/console content-mapping:list-synchronizers`'
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $force = $input->getOption('force');
        $only = $input->getOption('only');

        foreach ($this->synchronizerRegistry->getObjectclasses() as $objectclass) {
            if ($only && !\in_array($objectclass, $only)) {
                $this->logger->debug("Skipping Synchronizer for object class $objectclass");
                continue;
            }

            $this->logger->debug('Use Synchronizer for object class '.$objectclass);

            $this->synchronizerRegistry->getSynchronizer($objectclass)
                                       ->synchronize($objectclass, $force);
        }

        return 0;
    }
}
