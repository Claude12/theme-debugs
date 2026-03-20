<?php
  $flexible_content = get_field('ketchup_modules');

  if (is_array($flexible_content) || is_object($flexible_content)){
    foreach($flexible_content as $module) {
      if(empty($module) || !is_array($module)) return;
      $mod = explode('_',$module['acf_fc_layout']);
      $dir = implode('-',$mod);
      set_query_var( 'data',  $module );
      get_template_part('template-parts/'. $dir . '/snippet' , 'markup');
    }
  }