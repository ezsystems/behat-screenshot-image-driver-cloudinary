<?php
/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
use Behat\Behat\Context\Context;
use Behat\Behat\Context\SnippetAcceptingContext;
use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use Bex\Behat\Context\TestRunnerContext;

/**
 * Defines application features from the specific context.
 */
class FeatureContext implements Context, SnippetAcceptingContext
{
    const CLOUDINARY_URL = 'https://res.cloudinary.com/';

    /** @var TestRunnerContext $testRunnerContext */
    private $testRunnerContext;

    /**
     * @BeforeScenario
     */
    public function gatherContexts(BeforeScenarioScope $scope)
    {
        $environment = $scope->getEnvironment();

        $this->testRunnerContext = $environment->getContext('Bex\Behat\Context\TestRunnerContext');
    }

    /**
     * @Then I should see the cloudinary image url
     */
    public function iShouldSeeTheCloudinaryImageUrl()
    {
        $output = $this->testRunnerContext->getStandardOutputMessage() .
            $this->testRunnerContext->getStandardErrorMessage();
        $this->assertOutputContainsMessage($output, self::CLOUDINARY_URL);
    }

    /**
     * @param $message
     */
    private function assertOutputContainsMessage($output, $message)
    {
        if (mb_strpos($output, $message) === false) {
            throw new RuntimeException('Behat output did not contain the given message. Output: ' . PHP_EOL . $output);
        }
    }
}
