<?php

while (have_posts()) {
  the_post();
  get_template_part('parse','blocks',[
    'data' => get_the_content(),
    'render-html' => true
  ]);
}

 