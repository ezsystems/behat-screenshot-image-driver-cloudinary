<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Bex\Behat\ScreenshotExtension\Driver\CloudinaryClient;

interface CloudinaryClientInterface
{
    /**
     * Uploads file to Cloudinary using the signed API upload.
     *
     * @param $filepath
     *
     * @return string
     */
    public function upload($filepath);

    /**
     * Uploads file to Cloudinary using the unsigned API upload.
     *
     * @param $filepath
     *
     * @return string
     */
    public function uploadUnsigned($filepath);

    /**
     * Sets preset and cloud name (for unsigned API upload).
     *
     * @param string[] $values Cloudinary API values
     */
    public function configure($values);
}
