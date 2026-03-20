<?php get_header(); ?>
<div class="main-content-wrap km-estimated-total">
  <div class="main-content" id="main-content">
		<section class="content index-content">
				<div class="global-container clearfix">

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
					// echo '<pre>';
					// var_dump($values);
					// echo '</pre>'; 
				?>
				

				<?php

					// DATES

					// variable for final price output
					$plan_price = array();

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
							$ceremony_cost[$choice] = intval($plan_option[0]['price']);
						} elseif ( $values[8][value] == 2 ) {
							$ceremony_cost[$choice] = intval($plan_option[1]['price']);
						} elseif ( $values[8][value] == 3 ) {
							$ceremony_cost[$choice] = intval($plan_option[2]['price']);
						}

					} // end loop

				?>



				<span id="entry_id" class="visuallyhidden"><?php echo $_GET['entry']; ?></span>

					<div class="booking-form">

					<?php if ( !isset( $_GET["role"] ) ) { ?>
									<div class='gform_page_footer top' style="margin:0;border-top:0;">
											<input type='button' id='planner_email' class='button planner_email' value='Email these estimates to me' />
									</div>
					<?php } ?>

					<h3 class="title estimated-total-title">Your Estimated Total</h3>

					<?php for ($formCount=1; $formCount<=2; $formCount++) { ?>

						
						<?php 
							$date = $values[$formCount][value];
							$dt = new DateTime($date);
							$year = $dt->format('Y');
						?>

						<p class="direction estimate-direction">Estimate for <?php echo date('l, d F o', strtotime($values[$formCount][value])) ?></p>

						<ul class="estimated-total clearfix">

							<!-- Plan -->

							<li class="clearfix">
								<span class="item">Exclusive Venue Hire</span>
								<span class="detail"></span>
								<span class="price">&pound;<?php echo number_format( intval($plan_price[$formCount]), 2); ?></span>
							</li>

							<!-- Ceremony Type -->

							<li class="clearfix">
								<?php
									$rows = get_field('planner_plan_repeater', 'options' );
									$find_row = $rows[ $values[8][value]-1 ];
								?>
								<span class="item">Ceremony Type</span>
								<span class="detail"><?php echo $find_row['planner_plan_item']; ?></span>
								<span class="price">&pound;<?php echo number_format( intval($ceremony_cost[$formCount]), 2); ?></span>
							</li>

							<!-- Daytime (number of guests) -->

							<?php $daytime_guests = $values[9][value]; ?>
							<li class="clearfix">
								<span class="item">Number of daytime guests</span>
								<span class="detail"><?php echo $daytime_guests; ?></span>
								<span class="price"></span>
							</li>

							<!-- Number of children -->

							<?php $chindren = $values[56][value]; ?>
							<li class="clearfix">
								<span class="item">Number of children</span>
								<span class="detail"><?php echo $chindren; ?></span>
								<span class="price"></span>
							</li>

							<!-- Dressing Room -->
												
							<?php 
							$dressing_room_price = 0;
							$dressing_room_repeater = get_field('dressing_rooms_options', 'option');

								if ($values['80.1']['value'] == 'Dressing Room' && !empty($dressing_room_repeater)) {
								  if ($year == '2025') {
								      $dressing_room_price = $dressing_room_repeater[0]['price_2025'];
								  } elseif ($year == '2026') {
								      $dressing_room_price = $dressing_room_repeater[0]['price_2026'];
								  } elseif ($year == '2027') {
								      $dressing_room_price = $dressing_room_repeater[0]['price_2027'];
								  }
								?>
							  <li class="clearfix">
							      <span class="item">Dressing Room</span>
							      <span class="detail">Yes</span>
							      <span class="price">&pound;<?php echo number_format($dressing_room_price, 2); ?></span>
							  </li>
							<?php } ?>

							<?php 
							$lounge_room_price = 0;

								if ($values['80.2']['value'] == 'The Lounge' && !empty($dressing_room_repeater)) {
								  if ($year == '2025') {
								      $lounge_room_price = $dressing_room_repeater[1]['price_2025'];
								  } elseif ($year == '2026') {
								      $lounge_room_price = $dressing_room_repeater[1]['price_2026'];
								  } elseif ($year == '2027') {
								      $lounge_room_price = $dressing_room_repeater[1]['price_2027'];
								  }
								?>
							  <li class="clearfix">
							      <span class="item">The Lounge</span>
							      <span class="detail">Yes</span>
							      <span class="price">&pound;<?php echo number_format($lounge_room_price, 2); ?></span>
							  </li>
							<?php } ?>

							<!-- Pre Wedding Breakfast -->

							<?php 
								$prewed_brunch_price = 0;
								$prewed_classic_price = 0;
								$prewed_baps_price = 0;
							?>

							<?php if ( !empty($values[58]['value']) || !empty($values[59]['value']) || !empty($values[60]['value']) ): ?>
								<li class="clearfix has-subitems">
							      <span class="item">Pre Wedding Breakfast</span>
							      <span class="detail"></span>
							      <span class="price"></span>

							      <ul class="subitems">
							      		<?php if ( !empty($values[58]['value']) ): ?>
							      			<?php 
							      				$prewed_brunch_price = $values[58]['value'] * 21.50;
							      			?>
							      			<li>
							      				<span class="item">Breakfast & Brunch Light</span>
											      <span class="detail"><?php echo $values[58]['value']; ?>x</span>
											      <span class="price">&pound;<?php echo number_format($prewed_brunch_price, 2); ?></span>
							      			</li>
							      		<?php endif; ?>
							      		<?php if ( !empty($values[59]['value']) ): ?>
							      			<?php 
							      				$prewed_classic_price = $values[59]['value'] * 21.50;
							      			?>
							      			<li>
							      				<span class="item">Classic</span>
											      <span class="detail"><?php echo $values[59]['value']; ?>x</span>
											      <span class="price">&pound;<?php echo number_format($prewed_classic_price, 2); ?></span>
							      			</li>
							      		<?php endif; ?>
							      		<?php if ( !empty($values[60]['value']) ): ?>
							      			<?php 
							      				$prewed_baps_price = $values[60]['value'] * 17.50;
							      			?>
							      			<li>
							      				<span class="item">Baps</span>
											      <span class="detail"><?php echo $values[60]['value']; ?>x</span>
											      <span class="price">&pound;<?php echo number_format($prewed_baps_price, 2); ?></span>
							      			</li>
							      		<?php endif; ?>
							      </ul>

							  </li>
							<?php endif; ?>

							<!-- Canapés -->

							<?php 
							$canape_price = 0;
							if ( $values[7][value] != NULL ) { ?>
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

							<?php 
							$breakfast_price = 0;
							if ( $values[10][value] != NULL ) { ?>
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

							<?php if ( $values[66][value] != NULL ) { ?>
								<li class="clearfix">
									<?php
										$rows = get_field('planner_relaxed_dining_repeater', 'options' );
										$find_row = $rows[ $values[66][value] ];
										$breakfast_price = $find_row['planner_relaxed_dining_price'] * $daytime_guests;
									?>
									<span class="item"><?php the_field('planner_breakfast_keyword', 'options'); ?></span>
									<span class="detail"><?php echo $values[9][value] . ' &times; ' . $find_row['planner_relaxed_dining_title']; ?></span>
									<span class="price">&pound;<?php echo number_format($breakfast_price, 2); ?></span>
								</li>
							<?php } ?>

							<?php if ( $values['67.1']['value'] == 'Select' ) { ?>
								<li class="clearfix">
									<?php
										// $rows = get_field('planner_relaxed_dining_repeater', 'options' );
										// $find_row = $rows[ $values[66][value] ];
										$breakfast_price = 66 * $daytime_guests;
									?>
									<span class="item"><?php the_field('planner_breakfast_keyword', 'options'); ?></span>
									<span class="detail"><?php echo $values[9][value] . ' &times; Asian Catering' ?></span>
									<span class="price">&pound;<?php echo number_format($breakfast_price, 2); ?></span>
								</li>
							<?php } ?>

							<!-- Childrens 2 Course -->
						
							<?php  
							$child_course_price = 0;

							if ( $values[64][value] != NULL ) { 
								if ( $values[64][value] == '0' ) {
									$child_course_title = 'Childrens 2 Course';
									$child_course_price = 28 * $values[56][value]; 
								} elseif ( $values[64][value] == '1' ) {
									$child_course_title = 'Childrens 3 Course';
									$child_course_price = 34 * $values[56][value];
								}
								
							?>
								<li class="clearfix">
									<span class="item"><?php echo $child_course_title; ?></span>
									<span class="detail"><?php echo $values[56][value]; ?>x</span>
									<span class="price">&pound;<?php echo number_format($child_course_price, 2); ?></span>
								</li>

							<?php } ?>

							<!-- Crew Food -->
							<?php 
								$crew_hot_main_course_price = 0;
								$crew_sandwiches_price = 0;
							?>

							<?php if ( !empty($values[62]['value']) || !empty($values[63]['value']) ): ?>
								<li class="clearfix has-subitems">
							      <span class="item">Crew Food</span>
							      <span class="detail"></span>
							      <span class="price"></span>

							      <ul class="subitems">
							      		<?php if ( !empty($values[62]['value']) ): ?>
							      			<?php 
							      				$crew_hot_main_course_price = $values[62]['value'] * 27.95;
							      			?>
							      			<li>
							      				<span class="item">Hot main course and soft drinks</span>
											      <span class="detail"><?php echo $values[62]['value']; ?>x</span>
											      <span class="price">&pound;<?php echo number_format($crew_hot_main_course_price, 2); ?></span>
							      			</li>
							      		<?php endif; ?>
							      		<?php if ( !empty($values[63]['value']) ): ?>
							      			<?php 
							      				$crew_sandwiches_price = $values[63]['value'] * 23.95;
							      			?>
							      			<li>
							      				<span class="item">Sandwiches and soft drinks</span>
											      <span class="detail"><?php echo $values[63]['value']; ?>x</span>
											      <span class="price">&pound;<?php echo number_format($crew_sandwiches_price, 2); ?></span>
							      			</li>
							      		<?php endif; ?>
							      </ul>

							  </li>
							<?php endif; ?>

							<!-- Drinks -->
							<?php 

							$non_alco_guests = 0;
							$drinks_price = 0;
							$non_alco_drinks_price = 0;

							if ($values[69][value] != NULL) { 
								$non_alco_guests = $values[69][value];
								$alco_guests = $daytime_guests - $non_alco_guests;
							} else {
								$alco_guests = $daytime_guests;
							} ?>

							<?php if ( $values[11][value] != NULL ) { ?>
								<li class="clearfix">
									<?php
										$rows = get_field('planner_drinks_repeater', 'options');
										$find_row = $rows[ $values[11][value] ];
										$drinks_price = $find_row['planner_drinks_price'] * $alco_guests;
									?>
									<span class="item"><?php the_field('planner_drinks_keyword', 'options'); ?></span>
									<span class="detail"><?php echo $alco_guests . ' &times; ' . $find_row['planner_drinks_name']; ?></span>
									<span class="price">&pound;<?php echo number_format($drinks_price, 2); ?></span>
								</li>
							<?php } ?>

							<?php if ( $values[68][value] != NULL ) { ?>
								<li class="clearfix">
									<?php
										$rows = get_field('planner_non_alco_drinks_repeater', 'options');
										$find_row = $rows[ $values[68][value] ];
										$non_alco_drinks_price = $find_row['planner_drinks_price'] * $non_alco_guests;
									?>
									<span class="item">Non-Alcoholic Drinks</span>
									<span class="detail"><?php echo $non_alco_guests . ' &times; ' . $find_row['planner_drinks_name']; ?></span>
									<span class="price">&pound;<?php echo number_format($non_alco_drinks_price, 2); ?></span>
								</li>
							<?php } ?>

							<!-- Children Soft Drinks -->

							<?php 
							$children_soft_drinks_price = 0;

							if ( $values['70.1'][value] != NULL ) { 
								$children_soft_drinks_price = $chindren * 15;
							?>
							  <li class="clearfix">
							      <span class="item">Children Soft Drinks</span>
							      <span class="detail"><?php echo $chindren; ?>x</span>
							      <span class="price">&pound;<?php echo number_format($children_soft_drinks_price, 2); ?></span>
							  </li>
							<?php } ?>

							<!-- Gin Selection -->

							<?php 
							$ginselection_price = 0;
							if ( $values[47][value] != NULL ) { ?>
								<li class="clearfix">
									<?php
										$rows = get_field('planner_ginselection_repeater', 'options');
										$find_row = $rows[ $values[47][value] ];
										$ginselection_price = $find_row['planner_ginselection_price'];
									?>
									<span class="item"><?php the_field('planner_ginselection_keyword', 'options'); ?></span>
									<span class="detail"><?php echo $find_row['planner_ginselection_name']; ?></span>
									<span class="price">&pound;<?php echo number_format($ginselection_price, 2); ?></span>
								</li>
							<?php } ?>

							<!-- CHAMPAGNE TOWER -->

							<?php 
							$champagne_tower_price = 0;

							if ( $values[65][value] != NULL ) { ?>
								<li class="clearfix">
									<?php
										$rows = get_field('planner_champagnetower_repeater', 'options');
										$find_row = $rows[ $values[65][value] ];
										$champagne_tower_price = $find_row['planner_champagnetower_price'];
									?>
									<span class="item">Champagne Tower</span>
									<span class="detail"><?php echo $find_row['planner_champagnetower_name']; ?></span>
									<span class="price">&pound;<?php echo number_format($champagne_tower_price, 2); ?></span>
								</li>
							<?php } ?>

							<!-- Evening (number of guests) -->

							<?php 
							$additional_guests = 0;

							if ($values[38][value] != NULL) { 
								$additional_guests = $values[38][value];
								$total_guests = $daytime_guests + $additional_guests; ?>

								<li class="clearfix">
									<span class="item">Number of additional guests attending the evening party</span>
									<span class="detail"><?php echo $additional_guests; ?></span>
									<span class="price"></span>
								</li>
							<?php } else {
								$total_guests = $daytime_guests;
							} ?>

							<!-- Dinner (Evening Meal) -->

							<?php
							$evening_menus = $values[14]['choices'];
							$selected_evening_menu = $values[14]['value'];
							$selected_index = null;
							$evening_menu_price = 0;

							foreach ($evening_menus as $index => $choice) {
							    if ($choice['value'] === $selected_evening_menu) {
							        $selected_index = $index;
							        break;
							    }
							}

							if (!is_null($selected_index)) {
							    // Check if index is between 0 and 4 (inclusive)
							    if ($selected_index >= 0 && $selected_index <= 4) { ?>
							        <li class="clearfix">
												<?php
													$rows = get_field('planner_dinner_repeater', 'options' );
													$find_row = $rows[ $selected_index ];
													$evening_menu_price = $find_row['planner_dinner_price'] * $total_guests * 0.75;
												?>
												<span class="item">Substantial Evening Menu</span>
												<span class="detail"><?php echo $find_row['planner_dinner_name']; ?></span>
												<span class="price">
													<?php echo '&pound;' . number_format($evening_menu_price, 2); ?>
												</span>
											</li>
							    <?php } else { ?>
							        <li class="clearfix">
												<?php
													$rows = get_field('planner_dinner_s_repeater', 'options' );
													$find_row = $rows[ ($selected_index - 5) ];
													$evening_menu_price = $find_row['planner_dinner_s_price'] * $total_guests;
												?>
												<span class="item">Simple Evening Menu</span>
												<span class="detail"><?php echo $find_row['title']; ?></span>
												<span class="price">
													<?php echo '&pound;' . number_format($evening_menu_price, 2); ?>
												</span>
											</li>
							    <?php }
							} ?>

							<!-- Something Extra -->
							<?php 
							$crepe_station_price = 0;
							$ice_cream_trike_price = 0;
							$cheese_tower_price = 0;
							$extra_repeater = get_field('planner_something_extra_repeater', 'option');

								if ( $values['75.1'][value] != NULL && !empty($extra_repeater)) {
								  $crepe_station_price = $extra_repeater[0]['planner_relaxed_dining_price'];
								?>
								  <li class="clearfix">
								      <span class="item"><?php echo $extra_repeater[0]['planner_relaxed_dining_title']; ?> </span>
								      <span class="detail">Yes</span>
								      <span class="price">&pound;<?php echo number_format($crepe_station_price, 2); ?></span>
								  </li>
							<?php }; 

								if ( $values['75.2'][value] != NULL && !empty($extra_repeater)) {
								  $ice_cream_trike_price = $extra_repeater[1]['planner_relaxed_dining_price'];
								?>
								  <li class="clearfix">
								      <span class="item"><?php echo $extra_repeater[1]['planner_relaxed_dining_title']; ?> </span>
								      <span class="detail">Yes</span>
								      <span class="price">&pound;<?php echo number_format($ice_cream_trike_price, 2); ?></span>
								  </li>
							<?php }; 

								if ( $values['75.3'][value] != NULL && !empty($extra_repeater)) {
								  $cheese_tower_price = $extra_repeater[2]['planner_relaxed_dining_price'];
								?>
								  <li class="clearfix">
								      <span class="item"><?php echo $extra_repeater[2]['planner_relaxed_dining_title']; ?> </span>
								      <span class="detail">Yes</span>
								      <span class="price">&pound;<?php echo number_format($cheese_tower_price, 2); ?></span>
								  </li>
							<?php } ?>

							<!-- Extended Bar Hours -->

							<?php 
							$bar_price = 0;
							if ( $values[12 . '.' . 1][value] == Extend ) { ?>
								<li class="clearfix">
									<?php $bar_price = get_field('planner_bar_price', 'options'); ?>
									<span class="item">Extended Bar Hours</span>
									<span class="detail">Yes</span>
									<span class="price">&pound;<?php echo number_format($bar_price, 2); ?></span>
								</li>
							<?php } ?>

							<!-- After Hours -->

							<!-- Charcuterie -->
							<?php  
							$charcuterie_price = 0;
							if ( $values[49][value] != NULL ) { 
								$charcuterie_price = get_field('planner_charcuterie_price', 'options') * $values[49][value]; 
							?>
								<li class="clearfix">
									<span class="item">Charcuterie (After Hours)</span>
									<span class="detail"><?php echo $values[49][value]; ?></span>
									<span class="price">&pound;<?php echo number_format($charcuterie_price, 2); ?></span>
								</li>
							<?php } ?>

							<!-- Cheeseboard -->
							<?php  
							$cheeseboard_price = 0;
							if ( $values[50][value] != NULL ) { 
								$cheeseboard_price = get_field('planner_cheeseboard_price', 'options') * $values[50][value]; 
							?>
								<li class="clearfix">
									<span class="item">Cheeseboard (After Hours)</span>
									<span class="detail"><?php echo $values[50][value]; ?></span>
									<span class="price">&pound;<?php echo number_format($cheeseboard_price, 2); ?></span>
								</li>
							<?php } ?>
							

							<!-- Bedrooms -->

							<?php 
							$honeymoon_suite_price = 0;
							$silver_suite_price = 0;
							$double_bedroom_price = 0;
							$twin_room_price = 0;
							$bedrooms_repeater = get_field('planner_bedrooms_repeater', 'option');

								if ( $values['27.1'][value] != NULL && !empty($bedrooms_repeater)) {
								  if ($year == '2025') {
								      $honeymoon_suite_price = $bedrooms_repeater[0]['planner_bedrooms_price'];
								  } elseif ($year == '2026') {
								      $honeymoon_suite_price = $bedrooms_repeater[0]['planner_bedrooms_price_2026'];
								  } elseif ($year == '2027') {
								      $honeymoon_suite_price = $bedrooms_repeater[0]['planner_bedrooms_price_2027'];
								  }
								?>
								  <li class="clearfix">
								      <span class="item"><?php echo $bedrooms_repeater[0]['planner_bedrooms_name']; ?> </span>
								      <span class="detail">Yes</span>
								      <span class="price">&pound;<?php echo number_format($honeymoon_suite_price, 2); ?></span>
								  </li>
							<?php }; 

							if ( $values['27.2'][value] != NULL && !empty($bedrooms_repeater)) {
								  if ($year == '2025') {
								      $silver_suite_price = $bedrooms_repeater[1]['planner_bedrooms_price'];
								  } elseif ($year == '2026') {
								      $silver_suite_price = $bedrooms_repeater[1]['planner_bedrooms_price_2026'];
								  } elseif ($year == '2027') {
								      $silver_suite_price = $bedrooms_repeater[1]['planner_bedrooms_price_2027'];
								  }
								?>
								  <li class="clearfix">
								      <span class="item"><?php echo $bedrooms_repeater[1]['planner_bedrooms_name']; ?> </span>
								      <span class="detail">Yes</span>
								      <span class="price">&pound;<?php echo number_format($silver_suite_price, 2); ?></span>
								  </li>
							<?php };

							if ( $values['27.3'][value] != NULL && !empty($bedrooms_repeater)) {
								  if ($year == '2025') {
								      $double_bedroom_price = $bedrooms_repeater[2]['planner_bedrooms_price'] * $values[77][value];
								  } elseif ($year == '2026') {
								      $double_bedroom_price = $bedrooms_repeater[2]['planner_bedrooms_price_2026'] * $values[77][value];
								  } elseif ($year == '2027') {
								      $double_bedroom_price = $bedrooms_repeater[2]['planner_bedrooms_price_2027'] * $values[77][value];
								  }
								?>
								  <li class="clearfix">
								      <span class="item"><?php echo $bedrooms_repeater[2]['planner_bedrooms_name']; ?> </span>
								      <span class="detail"><?php echo $values[77][value]; ?>x</span>
								      <span class="price">&pound;<?php echo number_format($double_bedroom_price, 2); ?></span>
								  </li>
							<?php } 

							if ( $values['27.4'][value] != NULL && !empty($bedrooms_repeater)) {
								  if ($year == '2025') {
								      $twin_room_price = $bedrooms_repeater[3]['planner_bedrooms_price'];
								  } elseif ($year == '2026') {
								      $twin_room_price = $bedrooms_repeater[3]['planner_bedrooms_price_2026'];
								  } elseif ($year == '2027') {
								      $twin_room_price = $bedrooms_repeater[3]['planner_bedrooms_price_2027'];
								  }
								?>
								  <li class="clearfix">
								      <span class="item"><?php echo $bedrooms_repeater[3]['planner_bedrooms_name']; ?> </span>
								      <span class="detail">Yes</span>
								      <span class="price">&pound;<?php echo number_format($twin_room_price, 2); ?></span>
								  </li>
							<?php } ?>

							<!-- Bedrooms zbeds -->
							<?php 
							$bedrooms_zbeds_price = 0;
							if ( $values[78][value] != NULL ) { 
									if ($year == '2025') {
								      $bedrooms_zbeds_price = 38 * $values[78][value];
								  } elseif ($year == '2026') {
								      $bedrooms_zbeds_price = 40 * $values[78][value];
								  } elseif ($year == '2027') {
								      $bedrooms_zbeds_price = 42 * $values[78][value];
								  }
								?>
								<li class="clearfix">
									<span class="item">Number of Z Beds</span>
									<span class="detail"><?php echo $values[78][value]; ?></span>
									<span class="price">&pound;<?php echo number_format($bedrooms_zbeds_price, 2); ?></span>
								</li>
							<?php } ?>

							<!-- Extra Breakfasts -->
							<?php 
							$extra_breakfasts_price = 0;
							if ( $values[79][value] != NULL ) { 
									if ($year == '2025') {
								      $extra_breakfasts_price = 20 * $values[79][value];
								  } elseif ($year == '2026') {
								      $extra_breakfasts_price = 22 * $values[79][value];
								  } elseif ($year == '2027') {
								      $extra_breakfasts_price = 24 * $values[79][value];
								  }
								?>
								<li class="clearfix">
									<span class="item">Extra Breakfasts</span>
									<span class="detail"><?php echo $values[79][value]; ?>x</span>
									<span class="price">&pound;<?php echo number_format($extra_breakfasts_price, 2); ?></span>
								</li>
							<?php } ?>

							<!-- Sum Total -->

							<li class="sum-total clearfix">
								<span class="item">Estimated Total</span>
								<span class="detail"></span>
								<span class="price">
									<?php
										$sum =
											$plan_price[$formCount]
										+ $ceremony_cost[$formCount]
										+ $dressing_room_price
										+ $lounge_room_price
										+ $prewed_brunch_price
										+ $prewed_classic_price
								    + $prewed_baps_price
										+ $canape_price
										+ $breakfast_price
										+ $child_course_price
										+ $crew_hot_main_course_price
										+ $crew_sandwiches_price
										+ $drinks_price
										+ $non_alco_drinks_price
										+ $children_soft_drinks_price
										+ $ginselection_price
										+ $champagne_tower_price
										+ $evening_menu_price
										+ $crepe_station_price
										+ $ice_cream_trike_price
										+ $cheese_tower_price
										+ $bar_price 
										+ $charcuterie_price
										+ $cheeseboard_price
										+ $honeymoon_suite_price
										+ $silver_suite_price
										+ $double_bedroom_price
										+ $twin_room_price
										+ $bedrooms_zbeds_price
										+ $extra_breakfasts_price;
										echo '&pound;' . number_format($sum, 2);
									?>
								</span>
							</li>
						</ul>
					<?php } ?>
					<?php if ( !isset( $_GET["role"] ) ) { ?>
									<div class='gform_page_footer'>
											<input type='button' id='planner_email' class='button planner_email' value='Email these estimates to me' />
									</div>
							<?php } ?>
					<div class="terms">
						<h4><?php the_field('planner_terms_title', 'options' ); ?></h4>
						<?php the_field('planner_terms_body', 'options' ); ?>
					</div>
				</div>

				</div>
		</section>
	</div>
</div>

<style>
	.km-estimated-total ul.estimated-total li .detail br {
		display: none;
	}

	ul.subitems {
		width: 100%;
    padding-left: 20px;
	}

	ul.subitems li {
		background: inherit !important;
		border-bottom: 1px solid lightgrey;
	}

	ul.subitems li:last-child {
		border: none;
	}

	.km-estimated-total ul.estimated-total ul.subitems li .item {
		width: 30%;
	}

	.km-estimated-total ul.estimated-total ul.subitems li .detail {
		width: 50%;
	}

	.km-estimated-total ul.estimated-total li.has-subitems {
		padding-right: 0;
	}
</style>

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