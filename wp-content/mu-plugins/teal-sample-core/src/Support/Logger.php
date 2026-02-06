<?php
// wp-content/mu-plugins/teal-sample-core/src/Support/Logger.php

declare(strict_types=1);

namespace TealSample\Support;

final class Logger
{
  public function info(string $message, array $context = []): void
  {
    $this->write('INFO', $message, $context);
  }

  public function error(string $message, array $context = []): void
  {
    $this->write('ERROR', $message, $context);
  }

  private function write(string $level, string $message, array $context): void
  {
    if (!defined('WP_DEBUG') || !WP_DEBUG) {
      return;
    }

    $payload = [
      'level' => $level,
      'message' => $message,
      'context' => $context,
    ];

    error_log('[TealSample] ' . wp_json_encode($payload));
  }
}
