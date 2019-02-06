<?php
/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace spec\Bex\Behat\ScreenshotExtension\Driver;

use Bex\Behat\ScreenshotExtension\Driver\CloudinaryClient\CloudinaryClient;
use Bex\Behat\ScreenshotExtension\Driver\Local;
use PhpSpec\ObjectBehavior;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class CloudinarySpec extends ObjectBehavior
{
    public function let(Local $local, CloudinaryClient $client)
    {
        $this->beConstructedWith($local, $client);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType('Bex\Behat\ScreenshotExtension\Driver\Cloudinary');
    }

    public function it_should_call_the_unsigned_api_with_the_correct_data(ContainerBuilder $container, Local $local, CloudinaryClient $client)
    {
        $this->load($container, ['screenshot_directory' => '/tmp/behat-screenshot/', 'cloud_name' => 'X', 'preset' => 'Y']);

        $local->upload('imgdata', 'img_file_name.png')->shouldBeCalled()->willReturn('/tmp/behat-screenshot/img_file_name.png');
        $client->uploadUnsigned('/tmp/behat-screenshot/img_file_name.png')->shouldBeCalled()->willReturn(['success' => true, 'secure_url' => 'cloudinary']);

        $this->upload('imgdata', 'img_file_name.png')->shouldReturn('cloudinary');
    }

    public function it_should_call_the_signed_api_with_the_correct_data(ContainerBuilder $container, Local $local, CloudinaryClient $client)
    {
        $this->load($container, ['screenshot_directory' => '/tmp/behat-screenshot/']);

        $local->upload('imgdata', 'img_file_name.png')->shouldBeCalled()->willReturn('/tmp/behat-screenshot/img_file_name.png');
        $client->upload('/tmp/behat-screenshot/img_file_name.png')->shouldBeCalled()->willReturn(['success' => true, 'secure_url' => 'cloudinary']);

        $this->upload('imgdata', 'img_file_name.png')->shouldReturn('cloudinary');
    }
}
