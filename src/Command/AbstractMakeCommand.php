<?php

namespace Anteris\Autotask\Generator\Command;

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
     */
    public function setupEnvironment(InputInterface $input, OutputInterface $output)
    {
        // If we've set this up in the environment before,
        // don't ask the same old questions
        $username = Env::get('AUTOTASK_API_USERNAME');
        $secret   = Env::get('AUTOTASK_API_SECRET');
        $ic       = Env::GET('AUTOTASK_API_INTEGRATION_CODE');

        if (!$username || !$secret || !$ic) {
            // Ask some questions
            $helper = $this->getHelper('question');

            $usernameQuestion   = new Question('Please enter an API username to perform these requests with: ');

            $secretQuestion = new Question('Please enter the secret for this API user: ');
            $secretQuestion->setHidden(true);

            $icQuestion = new Question('Last one... please enter an integration code for this API user: ');
            $icQuestion->setHidden(true);

            $username = $helper->ask($input, $output, $usernameQuestion);
            $secret = $helper->ask($input, $output, $secretQuestion);
            $ic = $helper->ask($input, $output, $icQuestion);

            file_put_contents(
                Env::get('AUTOTASK_GENERATOR_DIRECTORY', getcwd()) . '/.env',
                "AUTOTASK_API_USERNAME=\"$username\"\n" .
                "AUTOTASK_API_SECRET=\"$secret\"\n" .
                "AUTOTASK_API_INTEGRATION_CODE=\"$ic\"\n"
            );
        }

        $this->generator = new Generator($username, $secret, $ic);
    }
}
