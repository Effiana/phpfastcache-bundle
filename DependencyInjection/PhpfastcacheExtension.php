<?php

/**
 *
 * This file is part of phpFastCache.
 *
 * @license MIT License (MIT)
 *
 * For full copyright and license information, please see the docs/CREDITS.txt file.
 *
 * @author Georges.L (Geolim4)  <contact@geolim4.com>
 * @author PastisD https://github.com/PastisD
 * @author Khoa Bui (khoaofgod)  <khoaofgod@gmail.com> http://www.phpfastcache.com
 *
 */

namespace Phpfastcache\Bundle\DependencyInjection;

use Phpfastcache\Exceptions\PhpfastcacheDriverException;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * Class phpFastCacheExtension
 * @package Phpfastcache\Bundle\DependencyInjection
 */
class PhpfastcacheExtension extends Extension
{
    /**
     * {@inheritDoc}
     *
     * @throws \Phpfastcache\Exceptions\PhpfastcacheDriverCheckException
     * @throws \Phpfastcache\Exceptions\PhpfastcacheDriverException
     * @throws \Exception
     * @throws \Symfony\Component\DependencyInjection\Exception\InvalidArgumentException
     * @throws \Symfony\Component\DependencyInjection\Exception\ServiceCircularReferenceException
     * @throws \Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');

        /**
         * Includes services_dev.yml only
         * if we are in debug mode
         */
        if(in_array($container->getParameter('kernel.environment'), ['dev', 'test'])){
            $loader->load('services_dev.yml');
        }

        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        foreach ($config['drivers'] as $name => $driver) {
            $class = "Phpfastcache\\Drivers\\" . $driver['type'] . '\Driver';
            foreach ($driver['parameters'] as $parameter_name => $parameter) {
                if (!$class::isValidOption($parameter_name, $parameter)) {
                    throw new PhpfastcacheDriverException("Option $parameter_name in driver {$driver['type']} doesn't exists");
                }
            }
        }

        $container->setParameter('phpfastcache', $config);
    }
}
