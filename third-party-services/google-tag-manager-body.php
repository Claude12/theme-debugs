<?php

$acceptAnalytics = getPrivacyPref('analytical') !== null ? getPrivacyPref('analytical') : true;
$gtmID = get_field('google_tag_manager_id','option') ?: null;

if($acceptAnalytics && $gtmID): ?>

  <!-- Google Tag Manager (noscript) -->
  <noscript><iframe src="https://www.googletagmanager.com/ns.html?id=<?php echo $gtmID; ?>"
  height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
  <!-- End Google Tag Manager (noscript) -->

  <?php endif;  ?>


