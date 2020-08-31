<?php

namespace Anteris\Autotask\Generator\Command;

use Anteris\Autotask\Generator\Helper\Api;
use Illuminate\Support\Env;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class MakeDefaultsCommand extends AbstractMakeCommand
{
    /** @var string The name of this command.  */
    protected static $defaultName = 'make:defaults';

    /**
     * Configures the command information.
     */
    protected function configure()
    {
        $this
            ->setDescription('Creates a new PHP class for the default Autotask services.')
            ->addOption('no-cache', null, InputOption::VALUE_NONE, 'Specifies whether any previous cached files should be ignored (still makes use of cache for efficiency, but clears afterward).')
            ->addOption('force', 'f', InputOption::VALUE_NONE, 'Overwrites previous classes if they exist.')
            ->addOption('output', 'o', InputOption::VALUE_REQUIRED, 'Sets the output directory for the generated classes.');

    }

    /**
     * Executes the command.
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->setupEnvironment($input, $output);
        $endpoints = Api::endpoints();

        try {
            foreach ($endpoints as $service) {
                $output->writeln('Generating classes for ' . $service);
                $this->generator->makeResource($service);
            }
        } catch (\Exception $error) {
            $output->writeln(
                '<error>There was an error creating that endpoint: ' .
                $error->getMessage() .
                '</error>'
            );
            return Command::FAILURE;
        }

        $output->writeln(
            '<info>Successfully created '.
            count($endpoints).
            ' endpoints in '.
            (microtime(true) - $_SERVER['REQUEST_TIME_FLOAT']).
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
