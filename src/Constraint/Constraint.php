<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Bex\Behat\ScreenshotExtension\Driver\Constraint;

interface Constraint
{
    public function canUpload();

    public function getReason();
}
