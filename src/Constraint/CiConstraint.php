<?php
/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Bex\Behat\ScreenshotExtension\Driver\Constraint;

use OndraM\CiDetector\CiDetector;

class CiConstraint implements Constraint
{
    const MODE_NAME = 'ci';

    public function canUpload()
    {
        $ciDetector = new CiDetector();

        return $ciDetector->isCiDetected();
    }

    public function getReason()
    {
        return 'Screenshots disabled on non-CI environments.';
    }
}
