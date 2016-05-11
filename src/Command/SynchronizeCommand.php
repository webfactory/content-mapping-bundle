<?php

namespace Webfactory\ContentMappingBundle\Command;

use Monolog\Handler\HandlerInterface;
use Monolog\Logger;
use Monolog\Processor\PsrLogMessageProcessor;
use Symfony\Bridge\Monolog\Formatter\ConsoleFormatter;
use Symfony\Bridge\Monolog\Handler\ConsoleHandler;
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
     * @var Logger
     */
    private $logger;

    /**
     * @param Registry $synchronizerRegistry
     * @param Logger   $logger
     */
    public function __construct(Registry $synchronizerRegistry, Logger $logger)
    {
        parent::__construct();
        $this->synchronizerRegistry = $synchronizerRegistry;
        $this->logger = $logger;
    }

    /**
     * {@inheritDoc}
     */
    protected function configure()
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
            'Only start the synchronizer for this objectclass'
        );
    }

    /**
     * {@inheritDoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $consoleHandler = new ConsoleHandler($output);
        $consoleHandler->setFormatter(new ConsoleFormatter("%start_tag%[%datetime%] %channel%.%level_name%:%end_tag% %message%\n"));
        $this->logger->setHandlers([$consoleHandler]);
        $this->logger->pushProcessor(new PsrLogMessageProcessor());

        $force = $input->getOption('force');
        $only = $input->getOption('only');

        foreach ($this->synchronizerRegistry->getObjectclasses() as $objectclass) {
            if ($only && !in_array($objectclass, $only)) {
                $this->logger->debug("Skipping Synchronizer for object class $objectclass");
                continue;
            }

            $this->logger->debug('Use Synchronizer for object class ' . $objectclass);

            $this->synchronizerRegistry->getSynchronizer($objectclass)
                                       ->synchronize($objectclass, $force);
        }
    }
}
