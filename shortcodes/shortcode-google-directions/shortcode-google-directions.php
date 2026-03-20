<?php

  $props = is_array($atts) ? $atts : [];
  $mode = array_key_exists('mode', $props) ? $props['mode'] : 'light';
  $label = array_key_exists('label', $props) ? $props['label'] : 'Get Directions';
  $maxWidth = array_key_exists('max', $props) ? $props['max'] : null;
  $defaultDestination = urlencode("Ketchup Marketing Unit 5, Buckminster Yard, Main Street, Buckminster, Leicestershire NG33 5SA");
  $destination = array_key_exists('destination', $props) ? urlencode($props['destination']) : $defaultDestination;
  $travelmode = array_key_exists('travelmode', $props) ? $props['travelmode'] : 'driving';
  $targetLink = 'https://www.google.com/maps/dir/?api=1&destination=' . $destination . '&travelmode=' . $travelmode . '&origin=';
?>

<form class="km-google-directions <?php echo $mode; ?>" <?php if($maxWidth) echo 'style="max-width:' . $maxWidth . '";'; ?>>
  <div class="km-gd-wrap">
    <input class="km-gd-param" name="gd-param" autocomplete="off" type="text" placeholder="Address or postcode" required/>
    <button name="km-gd-btn" class="km-gd-btn" data-dest="<?php echo $targetLink; ?>"><span class="km-gd-title"><?php echo $label; ?></span></button>
  </div>
</form>

<?php
/*

 With defaults: [km_directions]
 With options: [km_directions mode="dark" destination="London" travelmode="driving" max="250px" label="Go"]

 Label: button label
 mode: dark or light depending on background. DEFAULT: light
 destination: Optional. DEFAULT: client's address
 travelmode: Optional. driving/walking/bicycling DEFAULT: driving
 max: Optional. Caps form px or any other unit. Default: 100%

*/
?>