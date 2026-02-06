<?php
// wp-content/mu-plugins/teal-sample-core/src/Rest/ResourceController.php

declare(strict_types=1);

namespace TealSample\Rest;

use TealSample\Plugin;
use TealSample\Support\Cache;
use TealSample\Support\Logger;
use WP_REST_Request;
use WP_REST_Response;
use WP_Error;

final class ResourceController
{
  private Logger $logger;
  private Cache $cache;

  public function __construct(Logger $logger)
  {
    $this->logger = $logger;
    $this->cache = new Cache();
  }

  public function register(): void
  {
    add_action('rest_api_init', function (): void {
      register_rest_route('teal/v1', '/resources', [
        'methods' => 'GET',
        'callback' => [$this, 'index'],
        'permission_callback' => '__return_true',
        'args' => [
          'page' => [
            'type' => 'integer',
            'default' => 1,
            'sanitize_callback' => 'absint',
          ],
          'per_page' => [
            'type' => 'integer',
            'default' => 10,
            'sanitize_callback' => 'absint',
          ],
          'type' => [
            'type' => 'string',
            'required' => false,
            'sanitize_callback' => 'sanitize_text_field',
          ],
        ],
      ]);
    });
  }

  public function index(WP_REST_Request $request)
  {
    $page = max(1, (int) $request->get_param('page'));
    $perPage = (int) $request->get_param('per_page');
    $perPage = max(1, min(50, $perPage));
    $type = (string) $request->get_param('type');

    $opts = get_option(Plugin::OPTION_KEY, ['cache_ttl' => 300]);
    $ttl = isset($opts['cache_ttl']) ? (int) $opts['cache_ttl'] : 300;

    $cacheKey = $this->cache->key('resources', [
      'page' => $page,
      'perPage' => $perPage,
      'type' => $type,
    ]);

    $cached = $this->cache->get($cacheKey);
    if (is_array($cached)) {
      return new WP_REST_Response($cached, 200);
    }

    $taxQuery = [];
    if ($type !== '') {
      $taxQuery[] = [
        'taxonomy' => 'resource_type',
        'field' => 'slug',
        'terms' => $type,
      ];
    }

    $query = new \WP_Query([
      'post_type' => 'resource',
      'post_status' => 'publish',
      'paged' => $page,
      'posts_per_page' => $perPage,
      'no_found_rows' => false,
      'tax_query' => $taxQuery,
    ]);

    if (is_wp_error($query)) {
      return new WP_Error('teal_query_error', 'Could not load resources.', ['status' => 500]);
    }

    $items = array_map(function (\WP_Post $post): array {
      return [
        'id' => (int) $post->ID,
        'title' => get_the_title($post),
        'excerpt' => get_the_excerpt($post),
        'link' => get_permalink($post),
        'modified_gmt' => get_post_modified_time('c', true, $post),
      ];
    }, $query->posts);

    $payload = [
      'meta' => [
        'page' => $page,
        'per_page' => $perPage,
        'total' => (int) $query->found_posts,
        'total_pages' => (int) $query->max_num_pages,
      ],
      'data' => $items,
    ];

    $this->cache->set($cacheKey, $payload, $ttl);
    $this->logger->info('Served resources endpoint.', ['page' => $page, 'perPage' => $perPage, 'type' => $type]);

    return new WP_REST_Response($payload, 200);
  }
}
