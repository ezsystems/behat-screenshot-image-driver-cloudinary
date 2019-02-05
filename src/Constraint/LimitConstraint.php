<?php
/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Bex\Behat\ScreenshotExtension\Driver\Constraint;

use FilesystemIterator;

class LimitConstraint implements Constraint
{
    /** @var int */
    private $limit;

    /** @var string */
    private $savePath;

    public function __construct($limit, $savePath)
    {
        $this->limit = $limit;
        $this->savePath = $savePath;
    }

    public function canUpload()
    {
        return $this->getNumberOfScreenshotsTaken() < $this->limit;
    }

    public function getReason()
    {
        return sprintf('Limit of %s screenshots exceeded', $this->limit);
    }

    private function getNumberOfScreenshotsTaken()
    {
        $fi = new FilesystemIterator($this->savePath, FilesystemIterator::SKIP_DOTS);

        return iterator_count($fi);
    }
}
