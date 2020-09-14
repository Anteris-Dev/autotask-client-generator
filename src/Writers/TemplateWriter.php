<?php

namespace Anteris\Autotask\Generator\Writers;

use Twig\Environment;

/**
 * This class handles the writing of Twig templates to normal files.
 */
class TemplateWriter extends FileWriter
{
    /** @var Environment A Twig environment for writing templates. */
    protected Environment $twig;

    /**
     * Sets up the current class to begin writing template files!
     *
     * @author Aidan Casey <aidan.casey@anteris.com>
     */
    public function __construct(string $baseDir, Environment $twig)
    {
        $this->twig = $twig;
        parent::__construct($baseDir);
    }

    /**
     * Creates a new file from an existing template.
     *
     * @author Aidan Casey <aidan.casey@anteris.com>
     */
    public function createFileFromTemplate(
        string $filename,
        string $template,
        array $replacements = []
    ): void {
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
