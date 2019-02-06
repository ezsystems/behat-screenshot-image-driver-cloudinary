<?php
/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Bex\Behat\ScreenshotExtension\Driver\CloudinaryClient;

use Cloudinary\Uploader;
use Exception;

class CloudinaryClient implements CloudinaryClientInterface
{
    const CLOUD_NAME_KEY = 'cloud_name';
    const PRESET_KEY = 'preset';

    /** @var string */
    private $preset;

    public function upload($filepath)
    {
        try {
            $response = Uploader::upload($filepath);
            $response['success'] = true;
        } catch (Exception $e) {
            $response['success'] = false;
            $response['failureReason'] = $e->getMessage();
        }

        return $response;
    }

    public function uploadUnsigned($filepath)
    {
        try {
            $response = Uploader::unsigned_upload($filepath, $this->preset);
            $response['success'] = true;
        } catch (Exception $e) {
            $response['success'] = false;
            $response['failureReason'] = $e->getMessage();
        }

        return $response;
    }

    public function configure($values)
    {
        if (!array_key_exists(self::CLOUD_NAME_KEY, $values) || !array_key_exists(self::PRESET_KEY, $values)) {
            throw new \Exception('Unsigned upload requires cloud_name and preset keys');
        }

        \Cloudinary::config($values);
        $this->preset = $values[self::PRESET_KEY];
    }
}
