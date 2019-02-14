<?php
/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Bex\Behat\ScreenshotExtension\Driver\Constraint;

class ConstraintList implements Constraint
{
    /** @var Constraint[] */
    private $constraints;

    public function __construct()
    {
        $this->constraints = [];
    }

    public function canUpload()
    {
        foreach ($this->constraints as $constraint) {
            if (!$constraint->canUpload()) {
                return false;
            }
        }

        return true;
    }

    public function getReason()
    {
        $reason = '';

        foreach ($this->constraints as $constraint) {
            if (!$constraint->canUpload()) {
                $reason = $reason . ' ' . $constraint->getReason();
            }
        }

        return $reason;
    }

    public function add(Constraint $constraint)
    {
        $this->constraints[] = $constraint;
    }
}
