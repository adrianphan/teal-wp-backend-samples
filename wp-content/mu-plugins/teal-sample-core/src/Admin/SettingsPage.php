<?php
// wp-content/mu-plugins/teal-sample-core/src/Admin/SettingsPage.php

declare(strict_types=1);

namespace TealSample\Admin;

use TealSample\Plugin;
use TealSample\Support\Logger;

final class SettingsPage
{
  private Logger $logger;

  public function __construct(Logger $logger)
  {
    $this->logger = $logger;
  }

  public function register(): void
  {
    add_action('admin_menu', [$this, 'addMenu']);
    add_action('admin_init', [$this, 'registerSettings']);
  }

  public function addMenu(): void
  {
    add_options_page(
      'Teal Settings',
      'Teal Settings',
      'manage_options',
      'teal-sample-settings',
      [$this, 'render']
    );
  }

  public function registerSettings(): void
  {
    register_setting('teal_sample', Plugin::OPTION_KEY, [
      'type' => 'array',
      'sanitize_callback' => [$this, 'sanitize'],
      'default' => [
        'cache_ttl' => 300,
      ],
    ]);

    add_settings_section('teal_sample_main', 'Main', '__return_false', 'teal_sample');

    add_settings_field(
      'cache_ttl',
      'Cache TTL (seconds)',
      [$this, 'fieldCacheTtl'],
      'teal_sample',
      'teal_sample_main'
    );
  }

  public function sanitize($value): array
  {
    $value = is_array($value) ? $value : [];

    $ttl = isset($value['cache_ttl']) ? (int) $value['cache_ttl'] : 300;
    $ttl = max(30, min(3600, $ttl));

    return ['cache_ttl' => $ttl];
  }

  public function fieldCacheTtl(): void
  {
    $opts = get_option(Plugin::OPTION_KEY, ['cache_ttl' => 300]);
    $ttl = isset($opts['cache_ttl']) ? (int) $opts['cache_ttl'] : 300;

    echo '<input type="number" min="30" max="3600" name="' . esc_attr(Plugin::OPTION_KEY) . '[cache_ttl]" value="' . esc_attr((string) $ttl) . '" />';
  }

  public function render(): void
  {
    if (!current_user_can('manage_options')) {
      wp_die('You do not have permission to access this page.');
    }

    echo '<div class="wrap">';
    echo '<h1>Teal Settings</h1>';
    echo '<form method="post" action="options.php">';
    settings_fields('teal_sample');
    do_settings_sections('teal_sample');
    submit_button();
    echo '</form>';
    echo '</div>';
  }
}
