<?php
// blocks/stat-card/render.php

declare(strict_types=1);

if (!defined('ABSPATH')) {
  exit;
}

$label = isset($attributes['label']) ? sanitize_text_field((string) $attributes['label']) : 'Stat';
$value = isset($attributes['value']) ? sanitize_text_field((string) $attributes['value']) : '';
$note  = isset($attributes['note']) ? sanitize_text_field((string) $attributes['note']) : '';

$labelId = 'stat-card-label-' . wp_generate_uuid4();

?>
<section class="teal-stat-card" aria-labelledby="<?php echo esc_attr($labelId); ?>">
  <div class="teal-stat-card__inner">
    <h3 id="<?php echo esc_attr($labelId); ?>" class="teal-stat-card__label">
      <?php echo esc_html($label); ?>
    </h3>

    <?php if ($value !== ''): ?>
      <p class="teal-stat-card__value">
        <?php echo esc_html($value); ?>
      </p>
    <?php endif; ?>

    <?php if ($note !== ''): ?>
      <p class="teal-stat-card__note">
        <?php echo esc_html($note); ?>
      </p>
    <?php endif; ?>
  </div>
</section>
