<?php

namespace Anteris\Autotask\Generator\Commands;

use Anteris\Autotask\Generator\Generator;
use Illuminate\Support\Env;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

abstract class AbstractMakeCommand extends Command
{
    /** @var Generator API class generator. */
    protected Generator $generator;

    /**
     * Gets login information for Autotask and creates the generator.
     *
     * @author Aidan Casey <aidan.casey@anteris.com>
     */
    public function setupEnvironment(InputInterface $input, OutputInterface $output)
    {
        /**
         * If we have setup this environment before, do not ask the same old
         * questions
         */
        $username   = Env::get('AUTOTASK_API_USERNAME');
        $secret     = Env::get('AUTOTASK_API_SECRET');
        $ic         = Env::get('AUTOTASK_API_INTEGRATION_CODE');
        $outputDir  = $input->getOption('output') ?? Env::get('AUTOTASK_GENERATOR_DIRECTORY', getcwd());
        $force      = $input->hasOption('force') ? $input->getOption('force') : false;
        $noCache    = $input->hasOption('no-cache') ? $input->getOption('no-cache') : false;

        /**
         * Here, however, we clearly need to ask some questions about the
         * environment.
         */
        if (! $username || ! $secret || ! $ic) {
            $helper = $this->getHelper('question');

            $usernameQuestion   = new Question('Please enter an API username to perform these requests with: ');

            $secretQuestion = new Question('Please enter the secret for this API user: ');
            $secretQuestion->setHidden(true);

            $icQuestion = new Question('Last one... please enter an integration code for this API user: ');
            $icQuestion->setHidden(true);

            $username = $helper->ask($input, $output, $usernameQuestion);
            $secret   = $helper->ask($input, $output, $secretQuestion);
            $ic       = $helper->ask($input, $output, $icQuestion);

            file_put_contents(
                Env::get('AUTOTASK_GENERATOR_DIRECTORY', getcwd()) . '/.env',
                "AUTOTASK_API_USERNAME=\"$username\"\n" .
                "AUTOTASK_API_SECRET=\"$secret\"\n" .
                "AUTOTASK_API_INTEGRATION_CODE=\"$ic\"\n"
            );
        }

        /**
         * Return a new instance of the generator.
         */
        $this->generator = new Generator(
            $username,
            $secret,
            $ic,
            $outputDir,
            $force,
            ! ($noCache)
        );
    }
}
