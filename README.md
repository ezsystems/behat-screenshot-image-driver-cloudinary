ImageDriver-Cloudinary for Behat-ScreenshotExtension
=========================

This package is an image driver for the [bex/behat-screenshot](https://github.com/elvetemedve/behat-screenshot) behat extension which can upload the screenshot to [Cloudinary](http://cloudinary.com) and print the url of the uploaded image.

Installation
------------

Install by adding to your `composer.json`:

```bash
composer require --dev ezsystems/behat-screenshot-image-driver-cloudinary
```

Configuration
-------------

Enable the image driver in the Behat-ScreenshotExtension's config in `behat.yml`:

```yml
default:
  extensions:
    Bex\Behat\ScreenshotExtension:
      active_image_drivers: cloudinary
```

You can choose how to upload files:
- signed upload: export the Cloudinary values (cloud name, API Key and API Secret) as environmental variables
- unsigned upload: set the cloud_name and preset in configuration

```yml
default:
  extensions:
      Bex\Behat\ScreenshotExtension:
          active_image_drivers: cloudinary
          image_drivers:
              cloudinary:
                  screenshot_directory: /tmp/behat-screenshot/
                  cloud_name: X
                  preset: Y
```

Other options:
- limit: specifies the number of screenshots that can be taken in a single Behat run. It's based on number of files in `screenshot_directory`, so make sure that the folder is empty before the tests are run and cleaned only after the whole suite, not after every test. Limit checking can be disabled by exporting the `DISABLE_BEHAT_SCREENSHOT_LIMIT` variable.

Usage
-----

When a step fails a screenshot will be taken and uploaded to Cloudinary. URL of the uploaded image will be displayed in the Behat log.

```bash
  Scenario:                           # features/feature.feature:2
    Given I have a step               # FeatureContext::passingStep()
    When I have a failing step        # FeatureContext::failingStep()
      Error (Exception)
Screenshot has been taken. Open image at https://res.cloudinary.com/cloud_name/image/upload/IMAGE_LINK.png
    Then I should have a skipped step # FeatureContext::skippedStep()
```

## COPYRIGHT
Copyright (C) 1999-2021 Ibexa AS (formerly eZ Systems AS). All rights reserved.

## LICENSE
This source code is available separately under the following licenses:

A - Ibexa Business Use License Agreement (Ibexa BUL),
version 2.4 or later versions (as license terms may be updated from time to time)
Ibexa BUL is granted by having a valid Ibexa DXP (formerly eZ Platform Enterprise) subscription,
as described at: https://www.ibexa.co/product
For the full Ibexa BUL license text, please see:
- LICENSE-bul file placed in the root of this source code, or
- https://www.ibexa.co/software-information/licenses-and-agreements (latest version applies)

AND

B - GNU General Public License, version 2
Grants an copyleft open source license with ABSOLUTELY NO WARRANTY. For the full GPL license text, please see:
- LICENSE file placed in the root of this source code, or
- https://www.gnu.org/licenses/old-licenses/gpl-2.0.html
