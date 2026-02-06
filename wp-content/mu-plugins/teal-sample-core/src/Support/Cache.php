<?php
// wp-content/mu-plugins/teal-sample-core/src/Support/Cache.php

declare(strict_types=1);

namespace TealSample\Support;

final class Cache
{
  private string $group;

  public function __construct(string $group = 'teal_sample')
  {
    $this->group = $group;
  }

  public function get(string $key)
  {
    $value = wp_cache_get($key, $this->group, false, $found);
    return $found ? $value : null;
  }

  public function set(string $key, $value, int $ttl = 300): bool
  {
    return wp_cache_set($key, $value, $this->group, $ttl);
  }

  public function delete(string $key): bool
  {
    return wp_cache_delete($key, $this->group);
  }

  public function key(string $prefix, array $parts): string
  {
    return $prefix . ':' . md5(wp_json_encode($parts));
  }
}
