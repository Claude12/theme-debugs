<?php 

  $payload = $template_args['payload'];
  $data = $payload['item'];
  $config = $payload['config']; // if needed

  $btnData = [
    'link' => [
      'title' => $data['label'],
      'url' => '#0',
      'external' => false
    ],
    'extraButtonClass' => '',
    'visible' => true,
  ];


  hm_get_template_part( get_template_directory() . '/template-parts/common/buttons/snippet-markup.php', [
    'links' => [$btnData],
    // 'classPrefix' => 'hb', // optional
    'extraButtonClass' => $data['value'] . ' cta-hover-x',
  ] );

 

?>
