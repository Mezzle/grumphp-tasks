<?php
/**
 * @copyright (c) 2017 Stickee Technology Limited
 * @copyright (c) 2018-2019 Martin Meredith <martin@sourceguru.net>
 */
namespace Mez\GrumPHP;

use GrumPHP\Collection\FilesCollection;
use GrumPHP\Collection\ProcessArgumentsCollection;
use GrumPHP\Runner\TaskResult;
use GrumPHP\Runner\TaskResultInterface;
use GrumPHP\Task\AbstractExternalTask;
use GrumPHP\Task\Context\ContextInterface;
use GrumPHP\Task\Context\GitPreCommitContext;
use GrumPHP\Task\Context\RunContext;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Process\Process;

/**
 * Class ESLint
 *
 * @package Mez\GrumPHP
 */
final class ESLint extends AbstractExternalTask
{
    /**
     * @var ContextInterface
     */
    private $runContext;

    /**
     * @var array
     */
    private $runConfiguration;

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
                'env' => null,
                'paths' => null,
                'extensions' => ['js'],
                'format' => 'table',
                'max_warnings' => -1,
                'debug' => false,
                'bin' => null,
            ]
        );

        $resolver->addAllowedTypes('no_eslintrc', ['bool']);
        $resolver->addAllowedTypes('config', ['null', 'string']);
        $resolver->addAllowedTypes('env', ['null', 'string']);
        $resolver->addAllowedTypes('paths', ['null', 'array']);
        $resolver->addAllowedTypes('extensions', ['null', 'array']);
        $resolver->addAllowedTypes('format', ['null', 'string']);
        $resolver->addAllowedTypes('max_warnings', ['integer']);
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
        return $context instanceof GitPreCommitContext || $context instanceof RunContext;
    }

    /**
     * @param ContextInterface $context
     *
     * @return TaskResultInterface
     */
    public function run(ContextInterface $context)
    {
        $this->runContext = $context;
        $this->runConfiguration = $this->getConfiguration();

        $files = $this->getFiles();
        if ($files->isEmpty()) {
            return TaskResult::createSkipped($this, $context);
        }

        $arguments = $this->buildProcessArguments($files);
        $arguments->addFiles($files);

        /** @var Process $process */
        $process = $this->processBuilder->buildProcess($arguments);
        $process->run();

        if (!$process->isSuccessful()) {
            $errorMessage = $this->formatter->format($process);

            return TaskResult::createFailed($this, $context, $errorMessage);
        }

        return TaskResult::createPassed($this, $context);
    }

    /**
     * @return FilesCollection
     */
    private function getFiles()
    {
        $files = $this->runContext->getFiles();

        if (!empty($this->runConfiguration['paths'])) {
            $files = $files->paths($this->runConfiguration['paths']);
        }

        return $files->extensions($this->runConfiguration['extensions']);
    }

    /**
     * @return ProcessArgumentsCollection
     */
    private function buildProcessArguments()
    {
        $arguments = $this->runConfiguration['bin'] !== null
            ? ProcessArgumentsCollection::forExecutable($this->runConfiguration['bin'])
            : $this->processBuilder->createArgumentsForCommand('eslint');

        $arguments->addRequiredArgument('--format=%s', $this->runConfiguration['format']);
        $arguments->addOptionalArgument('--no-eslintrc', $this->runConfiguration['no_eslintrc']);
        $arguments->addOptionalArgument('--config=%s', $this->runConfiguration['config']);
        $arguments->addOptionalArgument('--env=%s', $this->runConfiguration['env']);
        $arguments->add(\sprintf('--max-warnings=%d', $this->runConfiguration['max_warnings']));

        foreach ($this->runConfiguration['extensions'] as $extension) {
            if (!$extension) {
                continue;
            }

            $extension = '.' . $extension;

            $arguments->addOptionalArgumentWithSeparatedValue('--ext', $extension);
        }

        $arguments->addOptionalArgument('--debug', $this->runConfiguration['debug']);

        return $arguments;
    }
}
