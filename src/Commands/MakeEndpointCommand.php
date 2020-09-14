<?php

namespace Anteris\Autotask\Generator\Commands;

use Illuminate\Support\Env;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class MakeEndpointCommand extends AbstractMakeCommand
{
    /** @var string The name of this command. */
    protected static $defaultName = 'make:endpoint';

    /**
     * Configures the command information.
     */
    protected function configure()
    {
        $this
            ->setDescription('Creates a new PHP class for interacting with an Autotask service.')
            ->addArgument('entity', InputArgument::REQUIRED, 'The entity to generate classes for.')
            ->addOption('no-cache', null, InputOption::VALUE_NONE, 'Specifies whether any previous cached files should be ignored (still makes use of cache for efficiency, but clears afterward).')
            ->addOption('output', 'o', InputOption::VALUE_REQUIRED, 'Sets the output directory for the generated class.')
            ->addOption('force', 'f', InputOption::VALUE_NONE, 'Overwrites previous classes if they exist.');
    }

    /**
     * Executes the command.
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->setupEnvironment($input, $output);

        try {
            $output->writeln('Generating classes for ' . $input->getArgument('entity'));
            $this->generator->makeResource($input->getArgument('entity'));
        } catch (\Exception $error) {
            $output->writeln(
                '<error>There was an error creating that endpoint: ' .
                $error->getMessage() .
                '</error>'
            );

            return Command::FAILURE;
        }

        $output->writeln(
            '<info>Successfully created endpoint in ' .
            (microtime(true) - $_SERVER['REQUEST_TIME_FLOAT']) .
            ' seconds!</info>'
        );
        $output->writeln('');

        // Regenerate support classes
        $command = $this->getApplication()->find('make:support-files');

        return $command->run(
            new ArrayInput([
                '-o' => $input->getOption('output') ?? Env::get('AUTOTASK_GENERATOR_DIRECTORY', getcwd()),
                '-f' => true,
            ]),
            $output
        );
    }
}
