<?php
  $data = $args['data'];

  if(!$data) return;

  $link = $data['url'];
  $title = $data['title'];

?>

<?php if($title && !wp_http_validate_url($link)) : ?>
  <h3 class="km-si-title"><?php echo $title; ?></h3>
<?php endif; ?>

<?php if($title && wp_http_validate_url($link) ) : ?>
  <h3 class="km-si-title"><a href="<?php echo $link; ?>"><?php echo $title; ?></a></h3>
<?php endif; ?>