<?php

namespace Anteris\Autotask\Generator\Command;

use Illuminate\Support\Env;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class MakeEndpointCommand extends AbstractMakeCommand
{
    /** @var string The name of this command.  */
    protected static $defaultName = 'make:endpoint';

    /**
     * Configures the command information.
     */
    protected function configure()
    {
        $this
            ->setDescription('Creates a new PHP class for interacting with an Autotask service.')
            ->addArgument('entity', InputArgument::REQUIRED, 'The entity to generate classes for.')
            ->addOption('output', 'o', InputOption::VALUE_REQUIRED, 'Sets the output directory for the generated class.')
            ->addOption('force', 'f', InputOption::VALUE_NONE, 'Overwrites previous classes if they exist.');
    }

    /**
     * Executes the command.
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->setupEnvironment($input, $output);
        $endpoint = $this->generator->endpoint();

        try {
            $outputDirectory = $input->getOption('output') ?? Env::get('AUTOTASK_GENERATOR_DIRECTORY', getcwd());
            $endpoint->setOutputDirectory($outputDirectory);

            if ($input->getOption('force')) {
                $endpoint->setOverwrite(true);
            }

            $endpoint->setEndpoint($input->getArgument('entity'));
            
            $output->writeln('Generating classes for ' . $input->getArgument('entity'));
            $endpoint->make();
        } catch (\Exception $error) {
            $output->writeln(
                '<error>There was an error creating that endpoint: ' .
                $error->getMessage() .
                '</error>'
            );
            return Command::FAILURE;
        }

        $output->writeln('<info>Successfully created endpoint!');

        // Regenerate support classes
        $command = $this->getApplication()->find('make:support-files');
        return $command->run(
            new ArrayInput([
                '-o' => $outputDirectory,
                '-f' => true,
            ]),
            $output
        );
    }
}
