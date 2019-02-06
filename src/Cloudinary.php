<?php
/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Bex\Behat\ScreenshotExtension\Driver;

use Bex\Behat\ScreenshotExtension\Driver\CloudinaryClient\CloudinaryClient;
use Bex\Behat\ScreenshotExtension\Driver\CloudinaryClient\CloudinaryClientInterface;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class Cloudinary implements ImageDriverInterface
{
    const CONFIG_PARAM_PRESET = 'preset';
    const CONFIG_PARAM_CLOUD_NAME = 'cloud_name';

    /** @var CloudinaryClientInterface */
    private $cloudinaryClient;

    /** @var ImageDriverInterface */
    private $localDriver;

    /** @var bool */
    private $isUnsignedUpload;

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
            ->end();
    }

    public function load(ContainerBuilder $container, array $config)
    {
        $this->localDriver->load($container, $config);

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
}
