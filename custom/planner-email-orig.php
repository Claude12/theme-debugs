<?php

function planner_email() {

	// get data from gravity forms
    $lead_id = $_REQUEST['entry_id'];
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

			echo "<pre>";
			var_dump($values);
			echo "</pre>";


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
					$plan_price[$choice] = intval($plan_price[$choice]) + intval($plan_option[0]['price']);
				} elseif ( $values[8][value] == 2 ) {
					$plan_price[$choice] = intval($plan_price[$choice]) + intval($plan_option[1]['price']);
				} elseif ( $values[8][value] == 3 ) {
					$plan_price[$choice] = intval($plan_price[$choice]) + intval($plan_option[2]['price']);
				}

	} // end loop


    // Dear ' . $values[30][value] . '


	$to = $values[32][value];

	$subject = 'Your Exclusive Venue Hire at Prestwold Hall';

 	$message = '<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
				<html>
				<head>
					<meta content="text/html; charset=utf-8" http-equiv="Content-Type" />
					<title>Email Template - Geometric</title>
					<style type="text/css">
						table br {display: none !important;}
						p {margin: 0;}
					</style>
				</head>
				<body marginheight="0" topmargin="0" marginwidth="0" style="margin: 0px; background-color: #FFFFFF;" bgcolor="#FFFFFF" leftmargin="0">
			        <table cellspacing="0" border="0" cellpadding="0" width="100%" bgcolor="#FFFFFF">
			        	<tr>
			        		<td>
			        			<h1 style="font-size: 24px; font-family: Arial, sans-serif; color: #4b0126; margin: 0 0 30px; text-align:center;">
			        				Your Wedding Day Estimate with Prestwold Hall
			        			</h1>
			        		</td>
			        	</tr>
			        	<tr>
			        		<td>
			        			<p style="font-size: 14px; font-family: Arial, sans-serif; color: #4b0126; margin: 0; text-align:center;">
			        				We do hope this estimate is of interest – please do not hesitate to contact us for further information or advice.
			        			</p>
			        		</td>
			        	</tr>';

	for ($formCount=1; $formCount<=2; $formCount++) {

		$message .= '<tr>
						<td>
		        			<h2 style="font-size: 18px; font-family: Arial, sans-serif; color: #4b0126; margin: 20px 0; text-align:center;">
								Estimate for ' . date('l, d F o', strtotime($values[$formCount][value])) . '
		        			</h2>
		        		</td>
		        	</tr>
		        	<tr>
		        		<td>
		        			<table cellspacing="0" border="0" cellpadding="0" width="100%" bgcolor="#F6E8ED">';

		// Plan

							$rows = get_field('planner_plan_repeater', 'options' );
							$find_row = $rows[ $values[8][value]-1 ];

		$message .= '<tr>
                        <td style="border-bottom: 1px solid #ccc;" valign="middle" width="25%" height="50px">
                            <p style="font-size: 14px; font-family: Arial, sans-serif; color: #4b0126; margin: 0 0 0 20px; line-height: 50px;">
                                Exclusive Venue Hire
                            </p>
                        </td>
                        <td style="border-bottom: 1px solid #ccc;" valign="middle" width="60%" height="50px">
                            <p style="font-size: 14px; font-family: Arial, sans-serif; color: #4b0126; line-height: 50px;">
                            	' . $find_row['planner_plan_item'] . '
                        	</p>
                        </td>
                        <td style="border-bottom: 1px solid #ccc;" valign="middle" width="15%" height="50px">
                            <p style="font-size: 14px; font-family: Arial, sans-serif; color: #4b0126; margin: 0 20px 0 0; line-height: 50px;text-align: right;">
                            	&pound;' . number_format( intval($plan_price[$formCount]), 2) . '
                            </p>
                        </td>
                    </tr>';

        // Daytime (number of guests)

        $daytime_guests = $values[9][value];

        // Canapés

		if ( $values[7][value] != NULL ) {
			$rows = get_field('planner_canapes_repeater', 'options' );
			$find_row = $rows[ $values[7][value] ];
			$canape_price = $find_row['planner_canapes_price'] * $daytime_guests;
	        $message .= '<tr>
	                        <td style="border-bottom: 1px solid #ccc;" valign="middle" width="25%">
                            <p style="font-size: 14px; font-family: Arial, sans-serif; color: #4b0126; margin: 0 0 0 20px; line-height: 50px;">
	                                ' . get_field('planner_canapes_keyword', 'options') . '
	                            </p>
	                        </td>
	                        <td style="border-bottom: 1px solid #ccc;" valign="middle" width="60%">
	                            <p style="font-size: 14px; font-family: Arial, sans-serif; color: #4b0126; line-height: 50px;">
	                            	'. $values[9][value] . ' &times; ' . $find_row['planner_canapes_name'] . '
	                        	</p>
	                        </td>
	                        <td style="border-bottom: 1px solid #ccc;" valign="middle" width="15%" height="50px">
	                            <p style="font-size: 14px; font-family: Arial, sans-serif; color: #4b0126; margin: 0 20px 0 0; line-height: 50px;text-align: right;">
	                            	&pound; ' . number_format($canape_price, 2) . '
	                            </p>
	                        </td>
	                    </tr>';
		}

		// Breakfast

		if ( $values[10][value] != NULL ) {
			$rows = get_field('planner_breakfast_repeater', 'options' );
			$find_row = $rows[ $values[10][value] ];
			$breakfast_price = $find_row['planner_breakfast_price'] * $daytime_guests;
            $message .= '<tr>
			                <td style="border-bottom: 1px solid #ccc;" valign="middle" width="25%">
	                            <p style="font-size: 14px; font-family: Arial, sans-serif; color: #4b0126; margin: 0 0 0 20px; line-height: 50px;">
			                        ' . get_field('planner_breakfast_keyword', 'options') . '
			                    </p>
			                </td>
			                <td style="border-bottom: 1px solid #ccc;" valign="middle" width="60%">
	                            <p style="font-size: 14px; font-family: Arial, sans-serif; color: #4b0126; line-height: 50px;">
			                    	' . $values[9][value] . ' &times; ' . $find_row['planner_breakfast_name'] . '
			                	</p>
			                </td>
			                <td style="border-bottom: 1px solid #ccc;" valign="middle" width="15%" height="50px">
	                            <p style="font-size: 14px; font-family: Arial, sans-serif; color: #4b0126; margin: 0 20px 0 0; line-height: 50px;text-align: right;">
			                    	&pound; ' . number_format($breakfast_price, 2) . '
			                    </p>
			                </td>
			            </tr>';
		}

		if ( $values[41][value] != NULL ) {
			$child_2_price = 21 * $values[41][value];
            $message .= '<tr>
			                <td style="border-bottom: 1px solid #ccc;" valign="middle" width="25%">
	                            <p style="font-size: 14px; font-family: Georgia, serif; color: #4b0126; margin: 0 0 0 20px; line-height: 50px;">
			                        Childrens 2 Course
			                    </p>
			                </td>
			                <td style="border-bottom: 1px solid #ccc;" valign="middle" width="60%">
	                            <p style="font-size: 14px; font-family: Georgia, serif; color: #4b0126; line-height: 50px;">
			                    	' . $values[41][value] . '
			                	</p>
			                </td>
			                <td style="border-bottom: 1px solid #ccc;" valign="middle" width="15%" height="50px">
	                            <p style="font-size: 14px; font-family: Georgia, serif; color: #4b0126; margin: 0 20px 0 0; line-height: 50px;text-align: right;">
			                    	&pound; ' . number_format($child_2_price, 2) . '
			                    </p>
			                </td>
			            </tr>';
		}
		if ( $values[42][value] != NULL ) {
			$child_2_price = 26.25 * $values[42][value];
            $message .= '<tr>
			                <td style="border-bottom: 1px solid #ccc;" valign="middle" width="25%">
	                            <p style="font-size: 14px; font-family: Georgia, serif; color: #4b0126; margin: 0 0 0 20px; line-height: 50px;">
			                        Childrens 2 Course
			                    </p>
			                </td>
			                <td style="border-bottom: 1px solid #ccc;" valign="middle" width="60%">
	                            <p style="font-size: 14px; font-family: Georgia, serif; color: #4b0126; line-height: 50px;">
			                    	' . $values[42][value] . '
			                	</p>
			                </td>
			                <td style="border-bottom: 1px solid #ccc;" valign="middle" width="15%" height="50px">
	                            <p style="font-size: 14px; font-family: Georgia, serif; color: #4b0126; margin: 0 20px 0 0; line-height: 50px;text-align: right;">
			                    	&pound; ' . number_format($child_3_price, 2) . '
			                    </p>
			                </td>
			            </tr>';
		}

		// Drinks

		if ( $values[11][value] != NULL ) {
			$rows = get_field('planner_drinks_repeater', 'options');
			$find_row = $rows[ $values[11][value] ];
			$drinks_price = $find_row['planner_drinks_price'] * $daytime_guests;
            $message .= '<tr>
			                <td style="border-bottom: 1px solid #ccc;" valign="middle" width="25%">
	                            <p style="font-size: 14px; font-family: Arial, sans-serif; color: #4b0126; margin: 0 0 0 20px; line-height: 50px;">
			                        ' . get_field('planner_drinks_keyword', 'options') . '
			                    </p>
			                </td>
			                <td style="border-bottom: 1px solid #ccc;" valign="middle" width="60%">
	                            <p style="font-size: 14px; font-family: Arial, sans-serif; color: #4b0126; line-height: 50px;">
			                    	' . $values[9][value] . ' &times; ' . $find_row['planner_drinks_name'] . '
			                	</p>
			                </td>
			                <td style="border-bottom: 1px solid #ccc;" valign="middle" width="15%" height="50px">
	                            <p style="font-size: 14px; font-family: Arial, sans-serif; color: #4b0126; margin: 0 20px 0 0; line-height: 50px;text-align: right;">
			                    	&pound; ' . number_format($drinks_price, 2) . '
			                    </p>
			                </td>
			            </tr>';
		}

		// Extended Bar Hours

		if ( $values[12 . '.' . 1][value] == Extend ) {
			$bar_price = get_field('planner_bar_price', 'options');
            $message .= '<tr>
			                <td style="border-bottom: 1px solid #ccc;" valign="middle" width="25%">
	                            <p style="font-size: 14px; font-family: Arial, sans-serif; color: #4b0126; margin: 0 0 0 20px; line-height: 50px;">
			                        Extended Bar Hours
			                    </p>
			                </td>
			                <td style="border-bottom: 1px solid #ccc;" valign="middle" width="60%">
	                            <p style="font-size: 14px; font-family: Arial, sans-serif; color: #4b0126; line-height: 50px;">
			                    	&mdash;
			                	</p>
			                </td>
			                <td style="border-bottom: 1px solid #ccc;" valign="middle" width="15%" height="50px">
	                            <p style="font-size: 14px; font-family: Arial, sans-serif; color: #4b0126; margin: 0 20px 0 0; line-height: 50px;text-align: right;">
			                    	&pound;' . number_format($bar_price, 2) . '
			                    </p>
			                </td>
			            </tr>';
		}

		// Evening (number of guests) -->

		$evening_guests = $values[13][value];

		// Dinner (Evening Meal)

			if ( $values[16][value] != NULL || $values[17][value] != NULL || $values[18][value] != NULL || $values[19][value] != NULL || $values[20][value] != NULL || $values[24][value] != NULL || $values[23][value] != NULL || $values[22][value] != NULL || $values[21][value] != NULL ) {
			$dinner_s_price = array();
			for ($i = 1; $i <= 9; $i++) {
				if ( $values[$i+15][value] != NULL ) {
					$rows = get_field('planner_dinner_s_repeater', 'options' );
					$dinner_s_price[$i] = $rows[$i-1]['planner_dinner_s_price'] * $values[$i+15][value];
                    $message .= '<tr>
			                        <td style="border-bottom: 1px solid #ccc;" valign="middle" width="25%">
			                            <p style="font-size: 14px; font-family: Arial, sans-serif; color: #4b0126; margin: 0 0 0 20px; line-height: 50px;">
			                                Simple Evening Menu
			                            </p>
			                        </td>
			                        <td style="border-bottom: 1px solid #ccc;" valign="middle" width="60%">
			                            <p style="font-size: 14px; font-family: Arial, sans-serif; color: #4b0126; line-height: 50px;">
			                            	' . $values[$i+15][value] . ' &times; ' . $rows[$i-1]['planner_dinner_s_description'] . '
			                        	</p>
			                        </td>
			                        <td style="border-bottom: 1px solid #ccc;" valign="middle" width="15%" height="50px">
			                            <p style="font-size: 14px; font-family: Arial, sans-serif; color: #4b0126; margin: 0 20px 0 0; line-height: 50px;text-align: right;">
			                            	&pound;' . number_format($dinner_s_price[$i], 2) . '
			                            </p>
			                        </td>
			                    </tr>';
				}
			}
		} elseif ( $values[14][value] != NULL ) {
			$rows = get_field('planner_dinner_repeater', 'options' );
			$find_row = $rows[ $values[14][value] ];
			$dinner_price = $find_row['planner_dinner_price'];
            $message .= '<tr>
			                <td style="border-bottom: 1px solid #ccc;" valign="middle" width="25%">
	                            <p style="font-size: 14px; font-family: Arial, sans-serif; color: #4b0126; margin: 0 0 0 20px; line-height: 50px;">
			                        ' . get_field('planner_dinner_keyword', 'options') .'
			                    </p>
			                </td>
			                <td style="border-bottom: 1px solid #ccc;" valign="middle" width="60%">
	                            <p style="font-size: 14px; font-family: Arial, sans-serif; color: #4b0126; line-height: 50px;">
			                    	' . $find_row['planner_dinner_name'] .'
			                	</p>
			                </td>
			                <td style="border-bottom: 1px solid #ccc;" valign="middle" width="15%" height="50px">
	                            <p style="font-size: 14px; font-family: Arial, sans-serif; color: #4b0126; margin: 0 20px 0 0; line-height: 50px;text-align: right;">';
			$guestsTotal = $values[38][value] + ($values[13][value]/2);
			$dinnerSum = $dinner_price * $guestsTotal;
			$message .= '&pound;' . number_format($dinnerSum, 2) . '
			                    </p>
			                </td>
			            </tr>';
		}

		// Bedrooms

		$bedrooms_price = 0;
for ($i = 1; $i <= 11; $i++) {
							if ($values[27 . "." . $i][value] != NULL ) {
								$part = $i-1;
								
								if ( $part == 6 ) {
									$part = 9;
								}

				$rows = get_field('planner_bedrooms_repeater', 'options' );
				$bedrooms_price += $rows[$part]['planner_bedrooms_price'];
                $message .= '<tr>
			                    <td style="border-bottom: 1px solid #ccc;" valign="middle" width="25%">
		                            <p style="font-size: 14px; font-family: Arial, sans-serif; color: #4b0126; margin: 0 0 0 20px; line-height: 50px;">
			                            ' . $rows[$part]['planner_bedrooms_name'] . '
			                        </p>
			                    </td>
			                    <td style="border-bottom: 1px solid #ccc;" valign="middle" width="60%">
		                            <p style="font-size: 14px; font-family: Arial, sans-serif; color: #4b0126; line-height: 50px;">
			                        	&mdash;
			                    	</p>
			                    </td>
			                    <td style="border-bottom: 1px solid #ccc;" valign="middle" width="15%" height="50px">
		                            <p style="font-size: 14px; font-family: Arial, sans-serif; color: #4b0126; margin: 0 20px 0 0; line-height: 50px;text-align: right;">
										&pound;' . number_format($rows[$part]['planner_bedrooms_price'], 2) . '
			                        </p>
			                    </td>
			                </tr>';
			}
		}

		// Bedrooms zbeds

		if ( $values[29][value] != NULL ) { 
								$rows = get_field('planner_bedrooms_repeater', 'options' );
								$bedrooms_zbeds = $rows[6]['planner_bedrooms_price'] * $values[29][value];
			if ($values[29][value]>1){
				$plural = 's';
			}
            $message .= '<tr>
			                <td style="border-bottom: 1px solid #ccc;" valign="middle" width="25%" height="75px">
	                            <p style="font-size: 14px; font-family: Arial, sans-serif; color: #4b0126; margin: 0 0 0 20px; line-height: 75px;">
			                        ' . $rows[6]['planner_bedrooms_name'] . '
			                    </p>
			                </td>
			                <td style="border-bottom: 1px solid #ccc;" valign="middle" width="60%" height="75px">
			                    <p style="font-size: 14px; font-family: Arial, sans-serif; color: #4b0126; margin: 0; line-height: 75px;">
			                    	' . $values[29][value] . ' bed' . $plural . '
			                	</p>
			                </td>
			                <td style="border-bottom: 1px solid #ccc;" valign="middle" width="15%" height="75px">
			                    <p style="font-size: 14px; font-family: Arial, sans-serif; color: #4b0126; margin: 0 20px 0 0;text-align: right; line-height: 75px;">
									&pound;' . number_format($bedrooms_zbeds, 2) . '
			                    </p>
			                </td>
			            </tr>';
		}

		// Sum Total

		$sum =
		  $plan_price[$formCount]
		+ $canape_price
		+ $breakfast_price
		+ $child_2_price
		+ $child_3_price
		+ $drinks_price
		+ $bar_price
		+ $dinnerSum
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
    	$message .= '<tr>
			            <td valign="middle" width="25%" height="70px">
			                <p style="font-size: 16px; font-family: Arial, sans-serif; color: #4b0126; margin: 0 0 0 20px;">
			                    Estimated Total
			                </p>
			            </td>
			            <td valign="middle" width="75%" colspan="2" height="70px">
			                <p style="font-size: 22px; font-family: Arial, sans-serif; color: #4b0126; margin: 0 20px 0 0;text-align: right;">
								&pound;' . number_format($sum, 2) . '
			                </p>
			            </td>
        			</tr>
				</table>';
	}

	// Contact Details

    $message .= '<tr>
	        		<td>
	        			<p style="font-size: 14px; font-family: Arial, sans-serif; color: #4b0126; margin: 30px 0 0; text-align:center;">
	        				For more details call us: <span style="font-weight:bold;">01509 880236</span>
	        			</p>
	        		</td>
	        	</tr>
	        	<tr>
	        		<td>
	        			<p style="font-size: 14px; font-family: Arial, sans-serif; color: #4b0126; margin: 10px 0 0; text-align:center;">
	        				<a style="color: #FF007F;text-decoration: none;" href="http://www.prestwold-hall.com/">Visit our website to learn more about Prestwold Hall</a>
	        			</p>
	        		</td>
	        	</tr>';

	$message .= '</td>
				</tr>
				</table>

				</body>
				</html>';

	$headers = array( 'Content-type: text/html', 'From: Prestwold Hall <enquiries@prestwold-hall.com>' );

	wp_mail( $to, $subject, $message, $headers );

}
add_action('wp_ajax_planner_email','planner_email');
add_action('wp_ajax_nopriv_planner_email','planner_email');
