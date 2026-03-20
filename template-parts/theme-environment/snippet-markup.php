<?php

  $data = get_field('environment','option') ?: null;
  if(!$data) return null;
  $featureEnabled = $data['notify_users'];

  if(!$featureEnabled) return;

  $config = km_define_env();
  $env = $config['env'] ?: null;
  $render = $config['render'];
  $enforced = $config['enforced'];

  if($env === null || !$render) return;
  $envTitle = $env === 'dev' ? 'development' : 'live';
  
?>


<div class="km-environment-alert km-env-<?php echo $env; ?>">
  <p><span class="km-env-desktop">You are currently working on</span> <span class="km-env-bold"><?php echo $envTitle; ?></span> environment. <?php if($enforced) echo '<strong>ENFORCED</strong>'; ?></p>
</div>





