<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Bex\Behat\ScreenshotExtension\Driver;

use Bex\Behat\ScreenshotExtension\Driver\CloudinaryClient\CloudinaryClient;
use Bex\Behat\ScreenshotExtension\Driver\CloudinaryClient\CloudinaryClientInterface;
use Bex\Behat\ScreenshotExtension\Driver\Constraint\Constraint;
use Bex\Behat\ScreenshotExtension\Driver\Constraint\LimitConstraint;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Filesystem\Filesystem;

class Cloudinary implements ImageDriverInterface
{
    const CONFIG_PARAM_PRESET = 'preset';
    const CONFIG_PARAM_CLOUD_NAME = 'cloud_name';
    const CONFIG_PARAM_LIMIT = 'limit';

    /** @var \Bex\Behat\ScreenshotExtension\Driver\CloudinaryClient\CloudinaryClientInterface */
    private $cloudinaryClient;

    /** @var ImageDriverInterface */
    private $localDriver;

    /** @var bool */
    private $isUnsignedUpload;

    /** @var \Bex\Behat\ScreenshotExtension\Driver\Constraint\Constraint */
    private $constraints;

    private $screenshotDirectory;

    public function __construct(ImageDriverInterface $localDriver = null, CloudinaryClientInterface $cloudinaryClient = null)
    {
        $this->localDriver = null !== $localDriver ? $localDriver : new Local();
        $this->cloudinaryClient = null !== $cloudinaryClient ? $cloudinaryClient : new CloudinaryClient();
    }

    /**
     * @param string $binaryImage
     * @param string $filename
     *
     * @return string File URL
     */
    public function upload($binaryImage, $filename)
    {
        $filename = $this->randomizeFilename($filename);

        if ($this->constraints && !$this->constraints->canUpload()) {
            return $this->constraints->getReason();
        }

        $filepath = $this->localDriver->upload($binaryImage, $filename);

        if ($this->isUnsignedUpload) {
            $response = $this->cloudinaryClient->uploadUnsigned($filepath);
        } else {
            $response = $this->cloudinaryClient->upload($filepath);
        }

        return $this->processResponse($response);
    }

    public function configure(ArrayNodeDefinition $builder)
    {
        $this->localDriver->configure($builder);

        $builder
            ->children()
                ->variableNode(self::CONFIG_PARAM_CLOUD_NAME)->end()
                ->variableNode(self::CONFIG_PARAM_PRESET)->end()
                ->scalarNode('mode')->defaultValue('')->end()
                ->integerNode('limit')->defaultValue(-1)->end()
                ->end()
            ->end();
    }

    public function load(ContainerBuilder $container, array $config)
    {
        $this->localDriver->load($container, $config);

        $this->screenshotDirectory = str_replace(
            '%paths.base%',
            $container->getParameter('paths.base'),
            $config[Local::CONFIG_PARAM_SCREENSHOT_DIRECTORY]
        );
        $this->ensureDirectoryExists($this->screenshotDirectory);

        if ($config[self::CONFIG_PARAM_LIMIT] >= 0) {
            $this->constraints = $this->createConstraints($config);
        }

        $this->isUnsignedUpload = array_key_exists(self::CONFIG_PARAM_PRESET, $config);
        if ($this->isUnsignedUpload) {
            $this->cloudinaryClient->configure([CloudinaryClient::CLOUD_NAME_KEY => $config[self::CONFIG_PARAM_CLOUD_NAME], CloudinaryClient::PRESET_KEY => $config[self::CONFIG_PARAM_PRESET]]);
        }
    }

    /**
     * Process Cloudinary API response.
     *
     * @param stringp[] $response Cloudinary API response
     *
     * @return string File URL
     */
    private function processResponse($response)
    {
        return $response['success'] ? $response['secure_url'] : sprintf('Failure: %s', $response['failureReason']);
    }

    private function randomizeFilename($filename)
    {
        return sprintf('%s-%s', str_replace('.', '', uniqid('', true)), $filename);
    }

    private function ensureDirectoryExists($directory)
    {
        try {
            $fs = new Filesystem();
            if (!$fs->exists($directory)) {
                $fs->mkdir($directory, 0770);
            }
        } catch (IOException $e) {
            throw new \RuntimeException(sprintf('Cannot create screenshot directory: %s', $directory));
        }
    }

    private function createConstraints($config): Constraint
    {
        return new LimitConstraint($config[self::CONFIG_PARAM_LIMIT], $this->screenshotDirectory);
    }
}
