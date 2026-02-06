<?php
// wp-content/mu-plugins/teal-sample-core/src/Plugin.php

declare(strict_types=1);

namespace TealSample;

use TealSample\Admin\SettingsPage;
use TealSample\PostTypes\Resource;
use TealSample\Rest\ResourceController;
use TealSample\Support\Logger;

final class Plugin
{
  public const OPTION_KEY = 'teal_sample_options';

  public function boot(): void
  {
    $logger = new Logger();

    (new Resource($logger))->register();
    (new SettingsPage($logger))->register();
    (new ResourceController($logger))->register();
  }
}
