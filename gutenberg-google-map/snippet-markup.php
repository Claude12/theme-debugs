 

<?php 

  $markers = get_field('map_markers') ?: [];
  $mapType = get_field('map_type') ?: 'roadmap';
  $mapZoom = get_field('map_zoom') ?: 16;
  $bulkIcon = get_field('bulk_icon') ?: null;
  $height = get_field('height_on_large_screens') ?: null;

  $blockProps = [];

  if($height) $blockProps['height'] = $height;

?>
<article class="km-google-map<?php if(!empty($args['acf-classes'])) echo ' ' . implode(' ',$args['acf-classes']); ?>" <?php if(!empty($blockProps)) echo populateStyleAttribute($blockProps); ?>>

<?php // Markers will be replaced with the map. No need to worry about marker markup  ?>
    <?php foreach ($markers as &$marker) : ?>
      <?php
          $title = array_key_exists('title', $marker) ? $marker['title'] : null;
          $descr = array_key_exists('description', $marker) ? $marker['description'] : null;
          $visible = array_key_exists('visible', $marker) ? $marker['visible'] : true;
          $location = array_key_exists('location', $marker) ? is_array($marker['location']) ? $marker['location'] : [] : [];
          $lat = array_key_exists('lat', $location) ? $location['lat'] : null;
          $long = array_key_exists('lng', $location) ? $location['lng'] : null;
          $icon = array_key_exists('marker_icon', $marker) ? $marker['marker_icon'] ?:  $bulkIcon ?: null : null;
      ?>

      <?php if($visible && $lat && $long): ?>
        <div class="km-google-map-marker" 
        data-lat="<?php echo esc_attr($lat); ?>"
        data-long="<?php echo esc_attr($long); ?>"
        <?php if($icon) echo 'data-icon="'. $icon .'"';?>
        >

        <?php hm_get_template_part( get_template_directory() . '/template-parts/gutenberg-google-map/_popup.php', ['marker' => $marker]);   ?>

      </div>
    <?php endif; ?>


  <div class="km-gm-wrap"
  data-zoom="<?php echo esc_attr($mapZoom); ?>"
  data-type="<?php echo esc_attr($mapType); ?>"
 >


    <?php endforeach; unset($marker); ?>

  </div>

</article>