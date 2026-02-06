<?php
/**
 * Plugin Name: Teal Sample Core (MU)
 * Description: Sample MU-plugin showing CPT + REST endpoint + settings + caching patterns.
 * Author: Adrian Phan
 * Version: 1.0.0
 */

declare(strict_types=1);

namespace TealSample;

if (!defined('ABSPATH')) {
  exit;
}

require_once __DIR__ . '/src/Plugin.php';
require_once __DIR__ . '/src/Support/Cache.php';
require_once __DIR__ . '/src/Support/Logger.php';
require_once __DIR__ . '/src/PostTypes/Resource.php';
require_once __DIR__ . '/src/Rest/ResourceController.php';
require_once __DIR__ . '/src/Admin/SettingsPage.php';

add_action('plugins_loaded', static function (): void {
  (new \TealSample\Plugin())->boot();
});
