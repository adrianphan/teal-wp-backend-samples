<?php
// cli/teal-import-resources.php
// Usage: wp teal import-resources --file=/path/to/resources.csv

declare(strict_types=1);

if (!defined('WP_CLI') || !WP_CLI) {
  return;
}

final class Teal_Resource_Importer_Command
{
  /**
   * Import Resources from CSV.
   *
   * ## OPTIONS
   * [--file=<file>]
   * : Path to a CSV file with headers: slug,title,content,excerpt,type
   *
   * ## EXAMPLES
   * wp teal import-resources --file=./resources.csv
   */
  public function __invoke(array $args, array $assoc_args): void
  {
    $file = $assoc_args['file'] ?? '';
    if (!$file || !file_exists($file)) {
      \WP_CLI::error('Missing or invalid --file.');
    }

    $handle = fopen($file, 'r');
    if (!$handle) {
      \WP_CLI::error('Could not open file.');
    }

    $headers = fgetcsv($handle);
    if (!$headers) {
      fclose($handle);
      \WP_CLI::error('Empty CSV.');
    }

    $map = array_flip($headers);
    $required = ['slug', 'title', 'content', 'excerpt', 'type'];
    foreach ($required as $col) {
      if (!isset($map[$col])) {
        fclose($handle);
        \WP_CLI::error("Missing required column: {$col}");
      }
    }

    $created = 0;
    $updated = 0;
    $failed = 0;

    while (($row = fgetcsv($handle)) !== false) {
      $slug = sanitize_title((string) $row[$map['slug']]);
      $title = sanitize_text_field((string) $row[$map['title']]);
      $content = wp_kses_post((string) $row[$map['content']]);
      $excerpt = sanitize_text_field((string) $row[$map['excerpt']]);
      $type = sanitize_title((string) $row[$map['type']]);

      if ($slug === '' || $title === '') {
        $failed++;
        \WP_CLI::warning('Skipping row with missing slug/title.');
        continue;
      }

      $existing = get_page_by_path($slug, OBJECT, 'resource');

      $postarr = [
        'post_type' => 'resource',
        'post_status' => 'publish',
        'post_title' => $title,
        'post_content' => $content,
        'post_excerpt' => $excerpt,
        'post_name' => $slug,
      ];

      if ($existing instanceof \WP_Post) {
        $postarr['ID'] = $existing->ID;
        $result = wp_update_post($postarr, true);
        if (is_wp_error($result)) {
          $failed++;
          \WP_CLI::warning("Update failed for {$slug}: " . $result->get_error_message());
          continue;
        }
        $updated++;
        $postId = (int) $existing->ID;
      } else {
        $result = wp_insert_post($postarr, true);
        if (is_wp_error($result)) {
          $failed++;
          \WP_CLI::warning("Insert failed for {$slug}: " . $result->get_error_message());
          continue;
        }
        $created++;
        $postId = (int) $result;
      }

      if ($type !== '') {
        wp_set_object_terms($postId, [$type], 'resource_type', false);
      }

      \WP_CLI::log("Processed: {$slug}");
    }

    fclose($handle);

    \WP_CLI::success("Done. Created: {$created}, Updated: {$updated}, Failed: {$failed}");
  }
}

\WP_CLI::add_command('teal import-resources', 'Teal_Resource_Importer_Command');
