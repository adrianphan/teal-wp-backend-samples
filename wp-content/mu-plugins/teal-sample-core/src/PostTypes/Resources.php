<?php
// wp-content/mu-plugins/teal-sample-core/src/PostTypes/Resource.php

declare(strict_types=1);

namespace TealSample\PostTypes;

use TealSample\Support\Logger;

final class Resource
{
  private Logger $logger;

  public function __construct(Logger $logger)
  {
    $this->logger = $logger;
  }

  public function register(): void
  {
    add_action('init', [$this, 'registerCpt']);
  }

  public function registerCpt(): void
  {
    $labels = [
      'name' => 'Resources',
      'singular_name' => 'Resource',
    ];

    register_post_type('resource', [
      'labels' => $labels,
      'public' => true,
      'show_in_rest' => true,
      'menu_icon' => 'dashicons-media-document',
      'supports' => ['title', 'editor', 'excerpt', 'thumbnail', 'revisions'],
      'has_archive' => true,
      'rewrite' => ['slug' => 'resources'],
      'capability_type' => ['resource', 'resources'],
      'map_meta_cap' => true,
    ]);

    register_taxonomy('resource_type', ['resource'], [
      'label' => 'Resource Types',
      'public' => true,
      'show_in_rest' => true,
      'rewrite' => ['slug' => 'resource-type'],
    ]);

    $this->logger->info('Registered CPT and taxonomy.');
  }
}
