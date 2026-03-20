<?php

$privacyManager = get_field('privacy_manager','option');
$modalBg = $privacyManager['modal_bg']['picker']['slug'] ?: null;
$mainColour = $privacyManager['main_colour']['picker']['slug'] ?: null;
$headingBg = $privacyManager['heading_bg']['picker']['slug'] ?: null;
$headingColour = $privacyManager['heading_colour']['picker']['slug'] ?: null;
$buttonBg = $privacyManager['button_bg']['picker']['slug'] ?: null;
$buttonColour = $privacyManager['button_colour']['picker']['slug'] ?: null;
$overlayBg = $privacyManager['overlay_bg']['picker']['slug'] ?: null;
$overlayOpacity = $privacyManager['overlay_opacity'] ?: 0.8;

$cookies = [
  [
    "name" => 'functional',
    "label" => 'Functional cookies',
    "description" => 'In some circumstances, we may use functionality cookies. Functionality cookies allow us to remember the choices you make on our site and to provide enhanced and more personalised features, such as customising a certain web page, remembering if we have asked you to participate in a promotion and for other services you request like watching a video or commenting on a blog. All of these features help us to improve your visit to the site.', 
    "checked" => true // default value
  ],
  [
    "name" => 'analytical',
    "label" => 'Analytical cookies',
    "description" => 'Analytical cookies are used to track visitors on the website, monitoring how they browse, how long their session lasts and what they are looking at. Analytical cookies also measure visitor demographics. They are essential for monitoring, optimising and managing the performance of a website.',
    "checked" => true
  ],
  // [
  //   "name" => 'targeting',
  //   "label" => 'Targeting Cookies',
  //   "description" => 'orem ipsum dolor sit amet consectetur adipisicing elit. Nihil sapiente ipsum, consectetur labore veritatis neque voluptas expedita eius adipisci earum. Aliquam sed veniam fuga nisi cumque. Dicta soluta a voluptatem?',
  //   "checked" => true
  // ]
];

?>


<div class="privacy-manager">

  <div class="pm-overlay<?php buildClass($overlayBg,'background-colour'); ?>" style="opacity: <?php echo $overlayOpacity; ?>"></div>

  <div class="pm-modal<?php buildClass($modalBg,'background-colour');buildClass($mainColour); ?>">

    <div class="pm-heading<?php buildClass($headingBg,'background-colour'); ?>">
      <p class="pm-title<?php buildClass($headingColour); ?>">Manage cookie settings</p>
      <button class="toggle-cookie-pref pm-close button-reset" title="Close">
        <span class="pm-icon-wrap<?php buildClass($buttonBg,'background-colour'); buildClass($buttonColour,'fill'); ?>">
          <svg>
            <use xlink:href="#cross"></use>
          </svg>
        </span>
      </button>
      <div class="pm-updated-info <?php buildClass($buttonColour);buildClass($buttonBg,'background-colour'); ?>">
       <span class="pm-update-icon<?php buildClass($buttonColour,'fill') ?>">
        <svg>
          <use xlink:href="#pm-accepted"></use>
        </svg>
      </span>
      <p>Preferences updated.</p>
     </div>
    </div>

    <form class="pm-form" id="pm-form">

      <?php foreach ($cookies as $cookie) : ?>
        <div class="pmf-item<?php if($cookie['name'] === 'functional') echo ' pmf-locked'; ?>">
          <div class="pmf-head">
            <div class="pmf-result">
              <span class="pmf-icon-wrap<?php buildClass($mainColour,'fill'); ?>">
                <svg class="pm-accepted">
                  <use xlink:href="#pm-accepted"></use>
                </svg>
                <svg class="pm-denied">
                  <use xlink:href="#pm-denied"></use>
                </svg>
              </span>
            </div>
            <p class="pmf-choice-title"><?php echo $cookie['label']; ?></p>
      
              <div class="pmf-choice<?php buildClass($buttonColour);buildClass($buttonBg,'background-colour'); ?>">
                <input type="checkbox" name="<?php echo $cookie['name']; ?>" <?php if($cookie['checked']) echo 'checked'; ?>  <?php if($cookie['name'] === 'functional') echo 'disabled'; ?> />
                <label class="pmf-label<?php buildClass($buttonColour,'background-colour'); ?>" for="<?php echo $cookie['name']; ?>"></label>
              </div>
        
          </div>
          <?php if($cookie['description']) : ?>
            <p class="pmf-info"><?php echo $cookie['description'];?></p>
           <?php endif; ?>
        </div>
      <?php endforeach; ?>

      <div class="pmf-controls">
        <input class="button-reset pmf-control<?php buildClass($buttonColour);buildClass($buttonBg,'background-colour'); ?>" type="submit" value="Update preferences" />
        <button class="button-reset pmf-control<?php buildClass($buttonColour);buildClass($buttonBg,'background-colour'); ?>" id="pm-reset">Restore defaults</button>
      </div>

    </form>

  </div>
</div>