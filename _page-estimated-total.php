
<?php get_header(); ?>

<section class="content index-content">
    <div class="global-container clearfix">

    	<h1 class="new-title" style="text-align: center;">ESTIMATED TOTAL</h1>

		<?php
			// get data from gravity forms
		    $lead_id = $_GET['entry'];
		    $lead = RGFormsModel::get_lead( $lead_id );
		    $form = GFFormsModel::get_form_meta( $lead['form_id'] );

		    $values = array();

		    if( is_array( $form['fields'] ) || is_object( $form['fields'] ) ){
			    foreach( $form['fields'] as $field ) {
			        $values[$field['id']] = array(
			            'id' => $field['id'],
			            'choices' => $field['choices'],
			            'value' => $lead[ $field['id'] ],
			        );
			    	if (count($field['choices']) > 0) {
			    		if( is_array( $field['choices'] ) || is_object( $field['choices'] ) ){
				    		foreach ($field['choices'] as $id => $choice) {
				    			$id = $id + 1;
				    			if($field['id'] == 27 && $id == 11){
					    			$id = 12;
				    			}
				    			if($field['id'] == 27 && $id == 10){
					    			$id = 11;
				    			}
				    			$choiceID = $field['id'].".".$id;
						        $values[$choiceID] = array(
						            'id' => $choiceID,
						            'value' => $lead[$choiceID],
						        );
				    		}
				    	}
			    	}
			    }
			}
			
		?>

		<?php

			// DATES

			// variable for final price output
			//$plan_price = array();

			// complete function twice for the users first and second choice of wedding day
			for ($choice = 1; $choice <= 2; $choice++) {

				// flag
				$hasitbeenfound = 'nope';

				// =============================================================================
				// exceptions
				// =============================================================================

				if ( $hasitbeenfound == 'nope' ) {

					$price_rows = get_field('planner_price_excep', 'options' );

					for ($i = 0; $i <= count($price_rows)-1; $i++) {

						$from = $price_rows[$price_row_id+$i]['planner_price_from'];
						$to = $price_rows[$price_row_id+$i]['planner_price_to'];

						$begin = new DateTime($from);
						$end = new DateTime($to);
						$end = $end->modify( '+1 day' );

						$interval = new DateInterval('P1D');
						$daterange = new DatePeriod($begin, $interval ,$end);

						foreach($daterange as $date){
						    // match user chosen date with date range
						    if ( $values[$choice][value] == $date->format("Y-m-d")) {
								$plan_price[$choice] = intval($price_rows[$i]['pricing']);
								$hasitbeenfound = 'yep 1';
						    }
						}
					}
				}

				// =============================================================================
				// price periods (excluding the last planner_price_period repeater row)
				// =============================================================================

				if ( $hasitbeenfound == 'nope' ) {

					$price_rows = get_field('planner_price_period', 'options' );

					for ($i = 0; $i <= count($price_rows)-2; $i++) {

						$from = $price_rows[$price_row_id+$i]['planner_price_date'];
						$to = $price_rows[$price_row_id+$i+1]['planner_price_date'];

						$begin = new DateTime($from);
						$end = new DateTime($to);
						$end = $end->modify( '+1 day' );

						$interval = new DateInterval('P1D');
						$daterange = new DatePeriod($begin, $interval, $end);

						foreach($daterange as $date){
						    // match user chosen date with date range
						    if ( $values[$choice]['value'] == $date->format("Y-m-d")) {

								// day of the week
						    	if ( date('w', strtotime( $values[$choice]['value'] )) == 1 ) {
									$plan_price[$choice] = intval($price_rows[$i]['pricing'][0]['mon']);
									$hasitbeenfound = 'yep 1';
						    	} elseif ( date('w', strtotime( $values[$choice]['value'] )) == 2 ) {
									$plan_price[$choice] = intval($price_rows[$i]['pricing'][0]['tue']);
									$hasitbeenfound = 'yep 2';
						    	} elseif ( date('w', strtotime( $values[$choice]['value'] )) == 3 ) {
									$plan_price[$choice] = intval($price_rows[$i]['pricing'][0]['wed']);
									$hasitbeenfound = 'yep 3';
						    	} elseif ( date('w', strtotime( $values[$choice]['value'] )) == 4 ) {
									$plan_price[$choice] = intval($price_rows[$i]['pricing'][0]['thur']);
									$hasitbeenfound = 'yep 4';
						    	} elseif ( date('w', strtotime( $values[$choice]['value'] )) == 5 ) {
									$plan_price[$choice] = intval($price_rows[$i]['pricing'][0]['fri']);
									$hasitbeenfound = 'yep 5';
						    	} elseif ( date('w', strtotime( $values[$choice]['value'] )) == 6 ) {
									$plan_price[$choice] = intval($price_rows[$i]['pricing'][0]['sat']);
									$hasitbeenfound = 'yep 6';
						    	} elseif ( date('w', strtotime( $values[$choice]['value'] )) == 0 ) {
									$plan_price[$choice] = intval($price_rows[$i]['pricing'][0]['sun']);
									$hasitbeenfound = 'yep 7';
						    	} else {
									$plan_price[$choice] = intval($price_rows[$i]['pricing'][0]['monthur']);
									$hasitbeenfound = 'yep 8';
						    	}
						    }
						}
					}
				}

				// =============================================================================
				// remaining period (excluding the last planner_price_period repeater row)
				// =============================================================================

				if ( $hasitbeenfound == 'nope' ) {

					$price_rows = get_field('planner_price_period', 'options' );

					// day of the week
			    	if ( date('w', strtotime( $values[$choice]['value'] )) == 1 ) {
						$plan_price[$choice] = intval($price_rows[count($price_rows)-1]['pricing'][0]['mon']);
						$hasitbeenfound = 'yep 01';
			    	} elseif ( date('w', strtotime( $values[$choice]['value'] )) == 2 ) {
						$plan_price[$choice] = intval($price_rows[count($price_rows)-1]['pricing'][0]['tue']);
						$hasitbeenfound = 'yep 02';
			    	} elseif ( date('w', strtotime( $values[$choice]['value'] )) == 3 ) {
						$plan_price[$choice] = intval($price_rows[count($price_rows)-1]['pricing'][0]['wed']);
						$hasitbeenfound = 'yep 03';
			    	} elseif ( date('w', strtotime( $values[$choice]['value'] )) == 4 ) {
						$plan_price[$choice] = intval($price_rows[count($price_rows)-1]['pricing'][0]['thur']);
						$hasitbeenfound = 'yep 04';
			    	} elseif ( date('w', strtotime( $values[$choice]['value'] )) == 5 ) {
						$plan_price[$choice] = intval($price_rows[count($price_rows)-1]['pricing'][0]['fri']);
						$hasitbeenfound = 'yep 05';
			    	} elseif ( date('w', strtotime( $values[$choice]['value'] )) == 6 ) {
						$plan_price[$choice] = intval($price_rows[count($price_rows)-1]['pricing'][0]['sat']);
						$hasitbeenfound = 'yep 06';
			    	} elseif ( date('w', strtotime( $values[$choice]['value'] )) == 0 ) {
						$plan_price[$choice] = intval($price_rows[count($price_rows)-1]['pricing'][0]['sun']);
						$hasitbeenfound = 'yep 07';
			    	} else {
						$plan_price[$choice] = intval($price_rows[count($price_rows)-1]['pricing'][0]['monthur']);
						$hasitbeenfound = 'yep 08';
			    	}
				}

				// CEREMONY TYPE

				// modify $plane_price to add cost of ceremony type

				$plan_option = get_field('planner_plan_repeater', 'options' );

				if ( $values[8][value] == 1 ) {
					$plan_price[$choice] = intval($plan_price[$choice]) + intval($plan_option[0]['price']);
				} elseif ( $values[8][value] == 2 ) {
					$plan_price[$choice] = intval($plan_price[$choice]) + intval($plan_option[1]['price']);
				} elseif ( $values[8][value] == 3 ) {
					$plan_price[$choice] = intval($plan_price[$choice]) + intval($plan_option[2]['price']);
				} elseif ( $values[8][value] == 4 ) {
					$plan_price[$choice] = intval($plan_price[$choice]) + intval($plan_option[3]['price']);
				}

			} // end loop
			$rows = get_field('planner_plan_repeater', 'options' );
			$find_row = $rows[ $values[8][value]-1 ];
			$plan_price = $find_row['price'];

		?>



		<span id="entry_id" class="visuallyhidden"><?php echo $_GET['entry']; ?></span>

    	<div class="booking-form">

			<?php if ( !isset( $_GET["role"] ) ) { ?>
	            <div class='gform_page_footer top' style="margin:0;border-top:0;border-bottom:1px solid #ccc;">
	                 <input type='button' id='planner_email' class='button planner_email' value='Email these estimates to me' />
	            </div>
			<?php } ?>

			<h3 class="title estimated-total-title">Your Estimated Total</h3>

			<?php for ($formCount=1; $formCount<=2; $formCount++) { ?>

				<p class="direction estimate-direction">Estimate for <?php echo date('l, d F o', strtotime($values[$formCount][value])) ?></p>

				<ul class="estimated-total clearfix">

					<!-- Plan -->

					<li class="clearfix">
						<?php
							$rows = get_field('planner_plan_repeater', 'options' );
							$find_row = $rows[ $values[8][value]-1 ];
						?>
						<span class="item">Exclusive Venue Hire</span>
						<span class="detail"><?php echo $find_row['planner_plan_item']; ?></span>
						<span class="price">&pound;<?php echo number_format( intval($plan_price[$formCount]), 2); ?></span>
					</li>

 					<!-- Daytime (number of guests) -->

					<?php $daytime_guests = $values[9][value]; ?>

					<!-- Canapés -->

					<?php if ( $values[7][value] != NULL ) { ?>
						<li class="clearfix">
							<?php
								$rows = get_field('planner_canapes_repeater', 'options' );
								$find_row = $rows[ $values[7][value] ];
								$canape_price = $find_row['planner_canapes_price'] * $daytime_guests;
							?>
							<span class="item"><?php the_field('planner_canapes_keyword', 'options'); ?></span>
							<span class="detail"><?php echo $values[9][value] . ' &times; ' . $find_row['planner_canapes_name']; ?></span>
							<span class="price">&pound;<?php echo number_format($canape_price, 2); ?></span>
						</li>
					<?php } ?>

					<!-- Breakfast -->

					<?php if ( $values[10][value] != NULL ) { ?>
						<li class="clearfix">
							<?php
								$rows = get_field('planner_breakfast_repeater', 'options' );
								$find_row = $rows[ $values[10][value] ];
								$breakfast_price = $find_row['planner_breakfast_price'] * $daytime_guests;
							?>
							<span class="item"><?php the_field('planner_breakfast_keyword', 'options'); ?></span>
							<span class="detail"><?php echo $values[9][value] . ' &times; ' . $find_row['planner_breakfast_name']; ?></span>
							<span class="price">&pound;<?php echo number_format($breakfast_price, 2); ?></span>
						</li>
					<?php } ?>

					<?php if ( $values[46][value] != NULL ) { 
						$child_2_price = 20 * $values[46][value];
					?>
						<li class="clearfix">
							<span class="item">Childrens 2 Course</span>
							<span class="detail"><?php echo $values[46][value]; ?></span>
							<span class="price">&pound;<?php echo number_format($child_2_price, 2); ?></span>
						</li>

					<?php } ?>

					<?php if ( $values[47][value] != NULL ) { 
						$child_3_price = 24 * $values[47][value];
					?>
						<li class="clearfix">
							<span class="item">Childrens 3 Course</span>
							<span class="detail"><?php echo $values[47][value]; ?></span>
							<span class="price">&pound;<?php echo number_format($child_3_price, 2); ?></span>
						</li>

					<?php } ?>

					<!-- Drinks -->

					<?php if ( $values[11][value] != NULL ) { ?>
						<li class="clearfix">
							<?php
								$rows = get_field('planner_drinks_repeater', 'options');
								$find_row = $rows[ $values[11][value] ];
								$drinks_price = $find_row['planner_drinks_price'] * $daytime_guests;
							?>
							<span class="item"><?php the_field('planner_drinks_keyword', 'options'); ?></span>
							<span class="detail"><?php echo $values[9][value] . ' &times; ' . $find_row['planner_drinks_name']; ?></span>
							<span class="price">&pound;<?php echo number_format($drinks_price, 2); ?></span>
						</li>
					<?php } ?>

					<!-- Extended Bar Hours -->

					<?php if ( $values[12 . '.' . 1][value] == Extend ) { ?>
						<li class="clearfix">
							<?php $bar_price = get_field('planner_bar_price', 'options'); ?>
							<span class="item">Extended Bar Hours</span>
							<span class="detail">&mdash;</span>
							<span class="price">&pound;<?php echo number_format($bar_price, 2); ?></span>
						</li>
					<?php } ?>

					<!-- Evening (number of guests) -->

					<?php $evening_guests = $values[13][value]; ?>

					<!-- Dinner (Evening Meal) -->

					<?php if ( $values[16][value] != NULL || $values[17][value] != NULL || $values[18][value] != NULL || $values[19][value] != NULL || $values[20][value] != NULL || $values[24][value] != NULL || $values[23][value] != NULL || $values[22][value] != NULL || $values[21][value] != NULL ) { ?>
						<?php $dinner_s_price = array(); ?>
						<?php for ($i = 1; $i <= 9; $i++) { ?>
							<?php if ( $values[$i+15][value] != NULL ) { ?>
								<?php
									$rows = get_field('planner_dinner_s_repeater', 'options' );
									$dinner_s_price[$i] = $rows[$i-1]['planner_dinner_s_price'] * $values[$i+15][value];
								?>
								<li class="clearfix">
									<span class="item">Simple Evening Menu</span>
									<span class="detail"><?php echo $values[$i+15][value] . ' &times; ' . $rows[$i-1]['planner_dinner_s_description']; ?></span>
									<span class="price">&pound;<?php echo number_format($dinner_s_price[$i], 2); ?></span>
								</li>
							<?php } ?>
						<?php } ?>

					<?php } elseif ( $values[14][value] != NULL ) { ?>
						<li class="clearfix">
							<?php
								$rows = get_field('planner_dinner_repeater', 'options' );
								$find_row = $rows[ $values[14][value] ];
								$dinner_price = $find_row['planner_dinner_price'];
							?>
							<span class="item"><?php the_field('planner_dinner_keyword', 'options'); ?></span>
							<span class="detail"><?php echo $find_row['planner_dinner_name']; ?></span>
							<span class="price">
								<?php
									$guestsTotal = $values[38][value] + ($values[13][value]/2);
									$dinnerSum = $dinner_price * $guestsTotal;
									echo '&pound;' . number_format($dinnerSum, 2);
								?>
							</span>
						</li>
					<?php } ?>

					<!-- Extras -->

					<?php $extras_price = 0; ?>
					<?php for ($i = 1; $i <= 12; $i++) {
							if ($values[43 . "." . $i][value] != NULL ) {
								$part = $i-1;

								$rows = get_field('planner_extras_repeater', 'options' );
								$extras_price += $rows[$part]['planner_extras_price'];
				?>

							<li class="clearfix">
								
								<span class="item"><?php echo $rows[$part]['planner_extras_name']; ?></span>
								<span class="detail">&mdash;</span>
								<span class="price">&pound;<?php echo number_format($rows[$part]['planner_extras_price'], 2); ?></span>
							</li>

						<?php } ?>
					<?php } ?>

					<?php //echo json_encode($values); ?>

					<!-- Bedrooms -->

					<?php $bedrooms_price = 0; ?>
					<?php for ($i = 1; $i <= 19; $i++) {

						$part = $i-1;

								
								if ($values[44 . "." . $i][value] != NULL ) {
				
								$rows = get_field('planner_bedrooms_repeater', 'options' );
								$bedrooms_price += $rows[$part]['planner_bedrooms_price'];
				?>

							<li class="clearfix part-<?php echo $part; ?> <?php echo $i; ?>">
								
								<span class="item"><?php echo $rows[$part]['planner_bedrooms_name']; ?></span>
								<span class="detail">&mdash;</span>
								<span class="price">&pound;<?php echo number_format($rows[$part]['planner_bedrooms_price'], 2); ?></span>
							</li>

					<?php } } ?>

					<!-- Sum Total -->

					<li class="sum-total clearfix">
						<span class="item">Estimated Total</span>
						<span class="detail"></span>
						<span class="price">
							<?php
								$sum =
								  $plan_price
								+ $canape_price
								+ $breakfast_price
								+ $child_2_price
								+ $child_3_price
								+ $drinks_price
								+ $bar_price
								+ $dinnerSum
								+ $extras_price
								+ $dinner_s_price[1]
								+ $dinner_s_price[2]
								+ $dinner_s_price[3]
								+ $dinner_s_price[4]
								+ $dinner_s_price[5]
								+ $dinner_s_price[6]
								+ $dinner_s_price[7]
								+ $dinner_s_price[8]
								+ $dinner_s_price[9]
								+ $bedrooms_price
								+ $bedrooms_bridal_price
								+ $bedrooms_zbeds;
								echo '&pound;' . number_format($sum, 2);
							?>
						</span>
					</li>
				</ul>
			<?php } ?>
			<?php if ( !isset( $_GET["role"] ) ) { ?>
	            <div class='gform_page_footer'>
	            	 <input type='button' id='back' class='button' onclick="location.href = 'https://weddings.hodsockpriory.com/'" value='Get a new quote' />
    	             <input type='button' id='planner_email' class='button planner_email' value='Email these estimates to me' />
        	    </div>
    	    <?php } ?>
    	    <h3>Want to see more? </h3>
	 	    <div id="extra-buttons">
				<a target="_blank" href="https://www.hodsockpriory.com/weddings/venue-hire/">Visit us</a>
				<a target="_blank" href="https://www.hodsockpriory.com/weddings/menus-winelist/">Our Menus</a>
				<a target="_blank" href="https://www.hodsockpriory.com/wp-content/uploads/2018/11/Wedding_Brochure_.pdf">Wedding Brochure</a>
		    </div>
			<div class="terms">
				<h4><?php the_field('planner_terms_title', 'options' ); ?></h4>
				<?php the_field('planner_terms_body', 'options' ); ?>
			</div>
		</div>
    </div>

</section>

<!-- Google Code for Contact Us Form Conversion Page -->
<script type="text/javascript">
/* <![CDATA[ */
var google_conversion_id = 958225579;
var google_conversion_language = "en";
var google_conversion_format = "3";
var google_conversion_color = "ffffff";
var google_conversion_label = "lq_-CNuMplcQq7n1yAM";
var google_remarketing_only = false;
/* ]]> */
</script>
<script type="text/javascript" src="//www.googleadservices.com/pagead/conversion.js">
</script>
<noscript>
<div style="display:inline;">
<img height="1" width="1" style="border-style:none;" alt="" src="//www.googleadservices.com/pagead/conversion/958225579/?label=lq_-CNuMplcQq7n1yAM&amp;guid=ON&amp;script=0"/>
</div>
</noscript>

<?php get_footer(); ?>