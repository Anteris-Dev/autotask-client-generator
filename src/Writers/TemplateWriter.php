<?php

namespace Anteris\Autotask\Generator\Writers;

use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class TemplateWriter extends FileWriter
{
    protected Environment $twig;

    public function __construct(string $baseDir, Environment $twig)
    {
        $this->twig = $twig;
        parent::__construct($baseDir);
    }

    /**
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws LoaderError
     */
    public function createFileFromTemplate(string $filename, string $template, array $replacements = []): void
    {
        $this->createFile(
            $filename,
            $this->twig->render($template, $replacements)
        );
    }

    /**
     * @inheritdoc
     */
    public function newContext(): TemplateWriter
    {
        $context = new static($this->originalBaseDir, $this->twig);
        $context->setOverwrite($this->overwrite);

        return $context;
    }
}
