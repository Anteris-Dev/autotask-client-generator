<?php

namespace Anteris\Autotask\Generator\Commands;

use Illuminate\Support\Env;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class MakeSupportFilesCommand extends AbstractMakeCommand
{
    /** @var string The name of this command.  */
    protected static $defaultName = 'make:support-files';

    /**
     * Configures the command information.
     */
    protected function configure()
    {
        $this
            ->setDescription('Creates support classes for Autotask services..')
            ->addOption('output', 'o', InputOption::VALUE_REQUIRED, 'Sets the output directory for the generated classes.')
            ->addOption('force', 'f', InputOption::VALUE_NONE, 'Overwrites previous classes if they exist.');
    }

    /**
     * Creates new support files.
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->setupEnvironment($input, $output);

        try {
            $output->writeln('Re-generating support classes');
            
            $this->generator->makeClient();
            $this->generator->makeSupport();
        } catch (\Exception $error) {
            $output->writeln(
                '<error>There was an error re-generating support files: ' .
                $error->getMessage() .
                '</error>'
            );
            return Command::FAILURE;
        }

        $output->writeln('<info>Successfully re-generated support files!');
        return Command::SUCCESS;
    }
}
