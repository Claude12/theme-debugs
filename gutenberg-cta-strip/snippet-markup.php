<?php 
 
  $fromCS = array_key_exists('from_client_settings', $args) ? $args['from_client_settings'] : null; // from client settings
  $staticSrc = get_field('static_cta_strip','option');

  $buttons = [];

  if($fromCS) {
    $buttons = $staticSrc['links'] ?: [];
  } else {
    $buttons = get_field('buttons') ? get_field('buttons')['links'] : [];
  }
  
  //if(empty($buttons)) return; // cant have this as we still need breadcrumbs
  
  $bg = $fromCS ? $staticSrc['background_block'] : get_field('background')['background_block'];
  $spaceAround = $fromCS ? $staticSrc['space_around'] : get_field('space_around');
  $width = $fromCS ? $staticSrc['width'] : get_field('width');
  $wrapProps = [];
  $centered = $fromCS ? $staticSrc['centralise'] : get_field('centralise');
  $renderLine = $fromCS ? $staticSrc['render_line'] : get_field('render_line');
  $addSeparatorString = $fromCS ? $staticSrc['add_separator_string'] : get_field('add_separator_string');

  $lineClasses = '';

  if($fromCS) {
    $lineClasses = 'kcs-line has-' . $staticSrc['line_colour'] . '-background-colour'; 
  } else {
    $lineClasses = createClasses(['kcs-line'],'line_colour', 'background-colour');
  }

  $hideBreadcrumbs = $fromCS ? $staticSrc['hide_breadcrumbs'] : get_field('hide_breadcrumbs');

  if($width) $wrapProps['max-width'] = $width . 'px';

  $wrapClasses = ['kcs-wrap'];
 
  array_push($wrapClasses, 'kcs-space-' . $spaceAround);
  if($addSeparatorString) array_push($wrapClasses, 'kcs-has-separator');
  if($centered) array_push($wrapClasses, 'kcs-centered');

?>

<?php if(!empty($buttons)) : ?>
  <section class="km-cta-strip<?php if(!empty($args['acf-classes'])) echo ' ' . implode(' ',$args['acf-classes']); ?>">
    <div class="<?php echo implode(' ', $wrapClasses); ?>" <?php echo populateStyleAttribute($wrapProps); ?>>
    
      <h2 class="sr-only">Quick Links</h2>
      
      <?php hm_get_template_part( get_template_directory() . '/template-parts/common/buttons/snippet-markup.php', [
          'links' => $buttons,
          'classPrefix' => 'kcs', // optional
          'extraButtonClass' => 'cta-hover-x' // optional
        ]);
      ?>   


      <?php if($renderLine) : ?>
        <span class="<?php echo $lineClasses; ?>">&nbsp;</span>
      <?php endif; ?>

    </div>
    
    <?php 
    
        if($bg['background']) {
          hm_get_template_part( get_template_directory() . '/template-parts/common/background-block/background-block.php', [
            'background' => $bg['background_configuration'],
          ]);
        }

      ?>   

  </section>
<?php endif; ?>

