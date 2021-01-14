<?php

declare(strict_types=1);

namespace Vanare\BehatCucumberJsonFormatter;

use Behat\Testwork\ServiceContainer\Extension as ExtensionInterface;
use Behat\Testwork\ServiceContainer\ExtensionManager;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Vanare\BehatCucumberJsonFormatter\Formatter\Formatter;

class Extension implements ExtensionInterface
{
    public function process(ContainerBuilder $container): void
    {
    }

    public function getConfigKey(): string
    {
        return 'cucumber_json';
    }

    public function initialize(ExtensionManager $extensionManager): void
    {
    }

    public function configure(ArrayNodeDefinition $builder): void
    {
        $builder->children()->scalarNode('fileNamePrefix')->defaultValue('');
        $builder->children()->scalarNode('outputDir')->defaultValue('build/tests');
        $builder->children()->scalarNode('fileName');
        $builder->children()->booleanNode('resultFilePerSuite')->defaultFalse();
    }

    public function load(ContainerBuilder $container, array $config): void
    {
        $definition = new Definition(Formatter::class);

        $definition->addArgument($config['fileNamePrefix']);
        $definition->addArgument($config['outputDir']);

        if (!empty($config['fileName'])) {
            $definition->addMethodCall('setFileName', [$config['fileName']]);
        }
        $definition->addMethodCall('setResultFilePerSuite', [$config['resultFilePerSuite']]);

        $container
            ->setDefinition('json.formatter', $definition)
            ->addTag('output.formatter')
        ;
    }
}
