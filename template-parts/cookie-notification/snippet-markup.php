
<?php 

$cookieGroup = get_field('cookie_notification','option');
$classes = ['km-cookie-notification'];

$bgSlug = $cookieGroup['background']['picker']['slug'];
$colourSlug = $cookieGroup['colour']['picker']['slug'];

if($bgSlug) array_push($classes,'has-' . $bgSlug . '-background-colour');
if($colourSlug) array_push($classes,'has-' . $colourSlug . '-colour');

?>

<!-- Cookie notification -->
<div class="<?php echo implode(' ', $classes); ?>">
	<p>We use cookies to improve your browsing experience and the functionality of the website. View our <?php echo get_the_privacy_policy_link() ?> for more information.</p>
	<div class="kmc-buttons">
		<button class="kmc-cookies-accept" id="kmc-cookies-accept"><span>I understand</span></button>
		<button class="kmc-cookies-accept toggle-cookie-pref"><span>Manage preferences</span></button> 
	</div>
</div>	 