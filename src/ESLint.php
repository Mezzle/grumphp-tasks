<?php
/**
 * @copyright (c) 2006-2017 Stickee Technology Limited
 */

namespace Stickee\GrumPHP;

use GrumPHP\Collection\FilesCollection;
use GrumPHP\Collection\ProcessArgumentsCollection;
use GrumPHP\Runner\TaskResult;
use GrumPHP\Runner\TaskResultInterface;
use GrumPHP\Task\AbstractExternalTask;
use GrumPHP\Task\Context\ContextInterface;
use GrumPHP\Task\Context\GitPreCommitContext;
use GrumPHP\Task\Context\RunContext;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class ESLint
 *
 * @package Stickee\GrumPHP
 */
final class ESLint extends AbstractExternalTask
{
    /**
     * getName
     *
     * @return string
     */
    public function getName()
    {
        return 'eslint';
    }

    /**
     * getconfiguration
     *
     * @return array
     */
    public function getConfiguration()
    {
        $configured = $this->grumPHP->getTaskConfiguration($this->getName());

        return $this->getConfigurableOptions()->resolve($configured);
    }

    /**
     * getConfigurableOptions
     *
     * @return OptionsResolver
     */
    public function getConfigurableOptions()
    {
        $resolver = new OptionsResolver();
        $resolver->setDefaults(
            [
                'no_eslintrc' => false,
                'config' => null,
                'debug' => false,
            ]
        );

        $resolver->addAllowedTypes('no_eslintrc', ['bool']);
        $resolver->addAllowedTypes('config', ['null', 'string']);
        $resolver->addAllowedTypes('debug', ['bool']);

        return $resolver;
    }

    /**
     * This methods specifies if a task can run in a specific context.
     *
     * @param ContextInterface $context
     *
     * @return bool
     */
    public function canRunInContext(ContextInterface $context)
    {
        return ($context instanceof GitPreCommitContext || $context instanceof RunContext);
    }

    /**
     * @param ContextInterface $context
     *
     * @return TaskResultInterface
     */
    public function run(ContextInterface $context)
    {
        $files = $context->getFiles()->name('*.js');
        if (0 === count($files)) {
            return TaskResult::createSkipped($this, $context);
        }

        $config = $this->getConfiguration();

        $arguments = $this->processBuilder->createArgumentsForCommand('eslint');
        $arguments->add('--format=table');
        $arguments->addOptionalArgument('--no-eslintrc', $config['no_eslintrc']);
        $arguments->addOptionalArgument('--config %s', $config['config']);
        $arguments->addOptionalArgument('--debug', $config['debug']);

        if ($context instanceof RunContext && $config['config'] !== null) {
            return $this->runOnAllFiles($context, $arguments);
        }

        return $this->runOnChangedFiles($context, $arguments, $files);
    }

    /**
     * @param ContextInterface $context
     * @param ProcessArgumentsCollection $arguments
     * @param FilesCollection $files
     *
     * @return TaskResult
     */
    private function runOnChangedFiles(
        ContextInterface $context,
        ProcessArgumentsCollection $arguments,
        FilesCollection $files
    ) {
        $hasErrors = false;
        $messages = [];

        foreach ($files as $file) {
            $fileArguments = new ProcessArgumentsCollection($arguments->getValues());
            $fileArguments->add($file);
            $process = $this->processBuilder->buildProcess($fileArguments);
            $process->run();

            if (!$process->isSuccessful()) {
                $hasErrors = true;
                $messages[] = $this->formatter->format($process);
            }
        }

        if ($hasErrors) {
            $errorMessage = sprintf("You have ESLint Errors:\n\n%s", implode("\n", $messages));

            return TaskResult::createFailed($this, $context, $errorMessage);
        }

        return TaskResult::createPassed($this, $context);
    }

    /**
     * @param ContextInterface $context
     * @param ProcessArgumentsCollection $arguments
     *
     * @return TaskResult
     */
    private function runOnAllFiles(ContextInterface $context, ProcessArgumentsCollection $arguments)
    {
        $process = $this->processBuilder->buildProcess($arguments);
        $process->run();

        if (!$process->isSuccessful()) {
            $errorMessage = $this->formatter->format($process);

            return TaskResult::createFailed($this, $context, $errorMessage);
        }

        return TaskResult::createPassed($this, $context);
    }
}
