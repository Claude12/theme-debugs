<?php
  $data = $template_args['marker'];
  $location = $data['location'] ?: [];
  $placeName = array_key_exists('name', $location) ? $location['name'] : null;
  $title = $data['title'] ?: $placeName;
  $content = $data['description'] ?: null;
?>

<div class="km-google-map-popup">

  <?php // Title ?>
  <?php if($title) : ?>
    <h2 class="km-gmp-title"><?php echo $title; ?></h2>
  <?php endif; ?>

  <?php // Title ?>
  <?php if($content) : ?>
    <p class="km-gmp-content"><?php echo $content; ?></p>
  <?php endif; ?>

</div>