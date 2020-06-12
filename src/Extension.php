<?php

namespace Vanare\BehatCucumberJsonFormatter;

use Behat\Testwork\ServiceContainer\Extension as ExtensionInterface;
use Behat\Testwork\ServiceContainer\ExtensionManager;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

class Extension implements ExtensionInterface
{
    /**
     * @param ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
    }

    /**
     * @return string
     */
    public function getConfigKey()
    {
        return 'cucumber_json';
    }

    /**
     * @param ExtensionManager $extensionManager
     */
    public function initialize(ExtensionManager $extensionManager)
    {
    }

    /**
     * @param ArrayNodeDefinition $builder
     */
    public function configure(ArrayNodeDefinition $builder)
    {
        $builder->children()->scalarNode('fileNamePrefix')->defaultValue('report');
        $builder->children()->scalarNode('outputDir')->defaultValue('build/tests');
        $builder->children()->scalarNode('fileName');
        $builder->children()->booleanNode('resultFilePerSuite')->defaultFalse();
    }

    /**
     * @param ContainerBuilder $container
     * @param array            $config
     */
    public function load(ContainerBuilder $container, array $config)
    {
        $definition = new Definition('Vanare\\BehatCucumberJsonFormatter\\Formatter\\Formatter');

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
