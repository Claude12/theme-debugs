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

		$date = $values[$formCount][value];
		$dt = new DateTime($date);
		$year = $dt->format('Y');

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

		$message .= '<tr>
                        <td style="border-bottom: 1px solid #ccc;" valign="middle" width="25%" height="50px">
                            <p style="font-size: 14px; font-family: Arial, sans-serif; color: #4b0126; margin: 0 0 0 20px; line-height: 50px;">
                                Exclusive Venue Hire
                            </p>
                        </td>
                        <td style="border-bottom: 1px solid #ccc;" valign="middle" width="60%" height="50px">
                            
                        </td>
                        <td style="border-bottom: 1px solid #ccc;" valign="middle" width="15%" height="50px">
                            <p style="font-size: 14px; font-family: Arial, sans-serif; color: #4b0126; margin: 0 20px 0 0; line-height: 50px;text-align: right;">
                            	&pound;' . number_format( intval($plan_price[$formCount]), 2) . '
                            </p>
                        </td>
                    </tr>';

        // Cerenomy type
        $rows = get_field('planner_plan_repeater', 'options' );
		$find_row = $rows[ $values[8][value]-1 ];

		$message .= '<tr>
                        <td style="border-bottom: 1px solid #ccc;" valign="middle" width="25%" height="50px">
                            <p style="font-size: 14px; font-family: Arial, sans-serif; color: #4b0126; margin: 0 0 0 20px; line-height: 50px;">
                                Ceremony Type
                            </p>
                        </td>
                        <td style="border-bottom: 1px solid #ccc;" valign="middle" width="60%" height="50px">
                            ' . $find_row['planner_plan_item'] . '
                        </td>
                        <td style="border-bottom: 1px solid #ccc;" valign="middle" width="15%" height="50px">
                            <p style="font-size: 14px; font-family: Arial, sans-serif; color: #4b0126; margin: 0 20px 0 0; line-height: 50px;text-align: right;">
                            	&pound;' . number_format( intval($ceremony_cost[$formCount]), 2) . '
                            </p>
                        </td>
                    </tr>';

        // Daytime (number of guests)

        $daytime_guests = $values[9][value];

        $message .= '<tr>
                        <td style="border-bottom: 1px solid #ccc;" valign="middle" width="25%" height="50px">
                            <p style="font-size: 14px; font-family: Arial, sans-serif; color: #4b0126; margin: 0 0 0 20px; line-height: 50px;">
                                Number of daytime guests
                            </p>
                        </td>
                        <td style="border-bottom: 1px solid #ccc;" valign="middle" width="60%" height="50px">
                            ' . $daytime_guests . '
                        </td>
                        <td style="border-bottom: 1px solid #ccc;" valign="middle" width="15%" height="50px">
                            
                        </td>
                    </tr>';

        // Number of children

        $chindren = $values[56][value];

        $message .= '<tr>
                        <td style="border-bottom: 1px solid #ccc;" valign="middle" width="25%" height="50px">
                            <p style="font-size: 14px; font-family: Arial, sans-serif; color: #4b0126; margin: 0 0 0 20px; line-height: 50px;">
                                Number of children
                            </p>
                        </td>
                        <td style="border-bottom: 1px solid #ccc;" valign="middle" width="60%" height="50px">
                            ' . $chindren . '
                        </td>
                        <td style="border-bottom: 1px solid #ccc;" valign="middle" width="15%" height="50px">
                            
                        </td>
                    </tr>';

        // Dressing Room

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

			$message .= '<tr>
                        <td style="border-bottom: 1px solid #ccc;" valign="middle" width="25%" height="50px">
                            <p style="font-size: 14px; font-family: Arial, sans-serif; color: #4b0126; margin: 0 0 0 20px; line-height: 50px;">
                                Dressing Room
                            </p>
                        </td>
                        <td style="border-bottom: 1px solid #ccc;" valign="middle" width="60%">
                            <p style="font-size: 14px; font-family: Arial, sans-serif; color: #4b0126; line-height: 50px;">
		                    	Yes
		                	</p>
		                </td>
                        <td style="border-bottom: 1px solid #ccc;" valign="middle" width="15%" height="50px">
                            <p style="font-size: 14px; font-family: Arial, sans-serif; color: #4b0126; margin: 0 20px 0 0; line-height: 50px;text-align: right;">
	                            	&pound; ' . number_format($dressing_room_price, 2) . '
	                        </p>
                        </td>
                    </tr>';
		}

		// The Lounge

		$lounge_room_price = 0;

		if ($values['80.2']['value'] == 'The Lounge' && !empty($dressing_room_repeater)) {
			if ($year == '2025') {
			      $lounge_room_price = $dressing_room_repeater[1]['price_2025'];
			} elseif ($year == '2026') {
			      $lounge_room_price = $dressing_room_repeater[1]['price_2026'];
			} elseif ($year == '2027') {
			      $lounge_room_price = $dressing_room_repeater[1]['price_2027'];
			}

			$message .= '<tr>
                        <td style="border-bottom: 1px solid #ccc;" valign="middle" width="25%" height="50px">
                            <p style="font-size: 14px; font-family: Arial, sans-serif; color: #4b0126; margin: 0 0 0 20px; line-height: 50px;">
                                The Lounge
                            </p>
                        </td>
                        <td style="border-bottom: 1px solid #ccc;" valign="middle" width="60%">
                            <p style="font-size: 14px; font-family: Arial, sans-serif; color: #4b0126; line-height: 50px;">
		                    	Yes
		                	</p>
		                </td>
                        <td style="border-bottom: 1px solid #ccc;" valign="middle" width="15%" height="50px">
                            <p style="font-size: 14px; font-family: Arial, sans-serif; color: #4b0126; margin: 0 20px 0 0; line-height: 50px;text-align: right;">
	                            	&pound; ' . number_format($lounge_room_price, 2) . '
	                        </p>
                        </td>
                    </tr>';
		}

		// Pre Wedding Breakfast

		$prewed_brunch_price = 0;
		$prewed_classic_price = 0;
		$prewed_baps_price = 0;

		if ( !empty($values[58]['value']) || !empty($values[59]['value']) || !empty($values[60]['value']) ) {
			$message .= '<tr>
			                <td style="border-bottom: 1px solid #ccc;" valign="middle" width="25%">
	                            <p style="font-size: 14px; font-family: Arial, sans-serif; color: #4b0126; margin: 0 0 0 20px; line-height: 50px;">
			                        Pre Wedding Breakfast
			                    </p>
			                </td>
			                <td style="border-bottom: 1px solid #ccc;" valign="middle" width="60%">
	                            <p style="font-size: 14px; font-family: Arial, sans-serif; color: #4b0126; line-height: 50px;">
			                    	
			                	</p>
			                </td>
			                <td style="border-bottom: 1px solid #ccc;" valign="middle" width="15%" height="50px">
	                            <p style="font-size: 14px; font-family: Arial, sans-serif; color: #4b0126; margin: 0 20px 0 0; line-height: 50px;text-align: right;">
			                    	
			                    </p>
			                </td>
			            </tr>';

			if ( !empty($values[58]['value']) ) {
				$prewed_brunch_price = $values[58]['value'] * 21.50;

				$message .= '<tr>
			                <td style="border-bottom: 1px solid #ccc;" valign="middle" width="25%">
	                            <p style="font-size: 14px; font-family: Arial, sans-serif; color: #4b0126; margin: 0 0 0 40px; line-height: 50px;">
			                        Breakfast & Brunch Light
			                    </p>
			                </td>
			                <td style="border-bottom: 1px solid #ccc;" valign="middle" width="60%">
	                            <p style="font-size: 14px; font-family: Arial, sans-serif; color: #4b0126; line-height: 50px;">
			                    	' . $values[58]['value'] . 'x
			                	</p>
			                </td>
			                <td style="border-bottom: 1px solid #ccc;" valign="middle" width="15%" height="50px">
	                            <p style="font-size: 14px; font-family: Arial, sans-serif; color: #4b0126; margin: 0 20px 0 0; line-height: 50px;text-align: right;">
			                    	&pound; ' . number_format($prewed_brunch_price, 2) . '
			                    </p>
			                </td>
			            </tr>';
			}

			if ( !empty($values[59]['value']) ) {
				$prewed_classic_price = $values[59]['value'] * 21.50;

				$message .= '<tr>
			                <td style="border-bottom: 1px solid #ccc;" valign="middle" width="25%">
	                            <p style="font-size: 14px; font-family: Arial, sans-serif; color: #4b0126; margin: 0 0 0 40px; line-height: 50px;">
			                        Classic
			                    </p>
			                </td>
			                <td style="border-bottom: 1px solid #ccc;" valign="middle" width="60%">
	                            <p style="font-size: 14px; font-family: Arial, sans-serif; color: #4b0126; line-height: 50px;">
			                    	' . $values[59]['value'] . 'x
			                	</p>
			                </td>
			                <td style="border-bottom: 1px solid #ccc;" valign="middle" width="15%" height="50px">
	                            <p style="font-size: 14px; font-family: Arial, sans-serif; color: #4b0126; margin: 0 20px 0 0; line-height: 50px;text-align: right;">
			                    	&pound; ' . number_format($prewed_classic_price, 2) . '
			                    </p>
			                </td>
			            </tr>';
			}

			if ( !empty($values[60]['value']) ) {
				$prewed_baps_price = $values[60]['value'] * 17.50;

				$message .= '<tr>
			                <td style="border-bottom: 1px solid #ccc;" valign="middle" width="25%">
	                            <p style="font-size: 14px; font-family: Arial, sans-serif; color: #4b0126; margin: 0 0 0 40px; line-height: 50px;">
			                        Baps
			                    </p>
			                </td>
			                <td style="border-bottom: 1px solid #ccc;" valign="middle" width="60%">
	                            <p style="font-size: 14px; font-family: Arial, sans-serif; color: #4b0126; line-height: 50px;">
			                    	' . $values[60]['value'] . 'x
			                	</p>
			                </td>
			                <td style="border-bottom: 1px solid #ccc;" valign="middle" width="15%" height="50px">
	                            <p style="font-size: 14px; font-family: Arial, sans-serif; color: #4b0126; margin: 0 20px 0 0; line-height: 50px;text-align: right;">
			                    	&pound; ' . number_format($prewed_baps_price, 2) . '
			                    </p>
			                </td>
			            </tr>';
			}
		}

        // Canapés
        $canape_price = 0;

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

		$breakfast_price = 0;

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

		if ( $values[66][value] != NULL ) {
			$rows = get_field('planner_relaxed_dining_repeater', 'options' );
			$find_row = $rows[ $values[66][value] ];
			$breakfast_price = $find_row['planner_relaxed_dining_price'] * $daytime_guests;

			$message .= '<tr>
			                <td style="border-bottom: 1px solid #ccc;" valign="middle" width="25%">
	                            <p style="font-size: 14px; font-family: Arial, sans-serif; color: #4b0126; margin: 0 0 0 20px; line-height: 50px;">
			                        ' . get_field('planner_breakfast_keyword', 'options') . '
			                    </p>
			                </td>
			                <td style="border-bottom: 1px solid #ccc;" valign="middle" width="60%">
	                            <p style="font-size: 14px; font-family: Arial, sans-serif; color: #4b0126; line-height: 50px;">
			                    	' . $values[9][value] . ' &times; ' . $find_row['planner_relaxed_dining_title'] . '
			                	</p>
			                </td>
			                <td style="border-bottom: 1px solid #ccc;" valign="middle" width="15%" height="50px">
	                            <p style="font-size: 14px; font-family: Arial, sans-serif; color: #4b0126; margin: 0 20px 0 0; line-height: 50px;text-align: right;">
			                    	&pound; ' . number_format($breakfast_price, 2) . '
			                    </p>
			                </td>
			            </tr>';
		}

		if ( $values['67.1']['value'] == 'Select' ) {
			$breakfast_price = 66 * $daytime_guests;

			$message .= '<tr>
			                <td style="border-bottom: 1px solid #ccc;" valign="middle" width="25%">
	                            <p style="font-size: 14px; font-family: Arial, sans-serif; color: #4b0126; margin: 0 0 0 20px; line-height: 50px;">
			                        ' . get_field('planner_breakfast_keyword', 'options') . '
			                    </p>
			                </td>
			                <td style="border-bottom: 1px solid #ccc;" valign="middle" width="60%">
	                            <p style="font-size: 14px; font-family: Arial, sans-serif; color: #4b0126; line-height: 50px;">
			                    	' . $values[9][value] . ' &times; Asian Catering
			                	</p>
			                </td>
			                <td style="border-bottom: 1px solid #ccc;" valign="middle" width="15%" height="50px">
	                            <p style="font-size: 14px; font-family: Arial, sans-serif; color: #4b0126; margin: 0 20px 0 0; line-height: 50px;text-align: right;">
			                    	&pound; ' . number_format($breakfast_price, 2) . '
			                    </p>
			                </td>
			            </tr>';
		}

		// Childrens Course

		$child_course_price = 0;

		if ( $values[64][value] != NULL ) {
			if ( $values[64][value] == '0' ) {
				$child_course_title = 'Childrens 2 Course';
				$child_course_price = 28 * $values[56][value]; 
			} elseif ( $values[64][value] == '1' ) {
				$child_course_title = 'Childrens 3 Course';
				$child_course_price = 34 * $values[56][value];
			}

			$message .= '<tr>
			                <td style="border-bottom: 1px solid #ccc;" valign="middle" width="25%">
	                            <p style="font-size: 14px; font-family: Arial, sans-serif; color: #4b0126; margin: 0 0 0 20px; line-height: 50px;">
			                        ' . $child_course_title . '
			                    </p>
			                </td>
			                <td style="border-bottom: 1px solid #ccc;" valign="middle" width="60%">
	                            <p style="font-size: 14px; font-family: Arial, sans-serif; color: #4b0126; line-height: 50px;">
			                    	' . $values[56][value] . 'x
			                	</p>
			                </td>
			                <td style="border-bottom: 1px solid #ccc;" valign="middle" width="15%" height="50px">
	                            <p style="font-size: 14px; font-family: Arial, sans-serif; color: #4b0126; margin: 0 20px 0 0; line-height: 50px;text-align: right;">
			                    	&pound; ' . number_format($child_course_price, 2) . '
			                    </p>
			                </td>
			            </tr>';
		}

		// Crew Food

		$crew_hot_main_course_price = 0;
		$crew_sandwiches_price = 0;

		if ( !empty($values[62]['value']) || !empty($values[63]['value']) ) {
			$message .= '<tr>
			                <td style="border-bottom: 1px solid #ccc;" valign="middle" width="25%">
	                            <p style="font-size: 14px; font-family: Arial, sans-serif; color: #4b0126; margin: 0 0 0 20px; line-height: 50px;">
			                        Crew Food
			                    </p>
			                </td>
			                <td style="border-bottom: 1px solid #ccc;" valign="middle" width="60%">
	                            <p style="font-size: 14px; font-family: Arial, sans-serif; color: #4b0126; line-height: 50px;">
			                    	
			                	</p>
			                </td>
			                <td style="border-bottom: 1px solid #ccc;" valign="middle" width="15%" height="50px">
	                            <p style="font-size: 14px; font-family: Arial, sans-serif; color: #4b0126; margin: 0 20px 0 0; line-height: 50px;text-align: right;">
			                    	
			                    </p>
			                </td>
			            </tr>';

			if ( !empty($values[62]['value']) ) {

				$crew_hot_main_course_price = $values[62]['value'] * 27.95;

				$message .= '<tr>
			                <td style="border-bottom: 1px solid #ccc;" valign="middle" width="25%">
	                            <p style="font-size: 14px; font-family: Arial, sans-serif; color: #4b0126; margin: 0 0 0 40px; line-height: 50px;">
			                        Hot main course and soft drinks
			                    </p>
			                </td>
			                <td style="border-bottom: 1px solid #ccc;" valign="middle" width="60%">
	                            <p style="font-size: 14px; font-family: Arial, sans-serif; color: #4b0126; line-height: 50px;">
			                    	' . $values[62]['value'] . 'x
			                	</p>
			                </td>
			                <td style="border-bottom: 1px solid #ccc;" valign="middle" width="15%" height="50px">
	                            <p style="font-size: 14px; font-family: Arial, sans-serif; color: #4b0126; margin: 0 20px 0 0; line-height: 50px;text-align: right;">
			                    	&pound; ' . number_format($crew_hot_main_course_price, 2) . '
			                    </p>
			                </td>
			            </tr>';
			}

			if ( !empty($values[63]['value']) ) {

				$crew_sandwiches_price = $values[63]['value'] * 23.95;

				$message .= '<tr>
			                <td style="border-bottom: 1px solid #ccc;" valign="middle" width="25%">
	                            <p style="font-size: 14px; font-family: Arial, sans-serif; color: #4b0126; margin: 0 0 0 40px; line-height: 50px;">
			                        Sandwiches and soft drinks
			                    </p>
			                </td>
			                <td style="border-bottom: 1px solid #ccc;" valign="middle" width="60%">
	                            <p style="font-size: 14px; font-family: Arial, sans-serif; color: #4b0126; line-height: 50px;">
			                    	' . $values[63]['value'] . 'x
			                	</p>
			                </td>
			                <td style="border-bottom: 1px solid #ccc;" valign="middle" width="15%" height="50px">
	                            <p style="font-size: 14px; font-family: Arial, sans-serif; color: #4b0126; margin: 0 20px 0 0; line-height: 50px;text-align: right;">
			                    	&pound; ' . number_format($crew_sandwiches_price, 2) . '
			                    </p>
			                </td>
			            </tr>';
			}
		}

		// Drinks

		$non_alco_guests = 0;
		$drinks_price = 0;
		$non_alco_drinks_price = 0;

		if ($values[69][value] != NULL) { 
			$non_alco_guests = $values[69][value];
			$alco_guests = $daytime_guests - $non_alco_guests;
		} else {
			$alco_guests = $daytime_guests;
		}

		if ( $values[11][value] != NULL ) {
			$rows = get_field('planner_drinks_repeater', 'options');
			$find_row = $rows[ $values[11][value] ];
			$drinks_price = $find_row['planner_drinks_price'] * $alco_guests;

			$message .= '<tr>
			                <td style="border-bottom: 1px solid #ccc;" valign="middle" width="25%">
	                            <p style="font-size: 14px; font-family: Arial, sans-serif; color: #4b0126; margin: 0 0 0 20px; line-height: 50px;">
			                        ' . get_field('planner_drinks_keyword', 'options') . '
			                    </p>
			                </td>
			                <td style="border-bottom: 1px solid #ccc;" valign="middle" width="60%">
	                            <p style="font-size: 14px; font-family: Arial, sans-serif; color: #4b0126; line-height: 50px;">
			                    	' . $alco_guests . ' &times; ' . $find_row['planner_drinks_name'] . '
			                	</p>
			                </td>
			                <td style="border-bottom: 1px solid #ccc;" valign="middle" width="15%" height="50px">
	                            <p style="font-size: 14px; font-family: Arial, sans-serif; color: #4b0126; margin: 0 20px 0 0; line-height: 50px;text-align: right;">
			                    	&pound; ' . number_format($drinks_price, 2) . '
			                    </p>
			                </td>
			            </tr>';
		}

		if ( $values[68][value] != NULL ) {
			$rows = get_field('planner_non_alco_drinks_repeater', 'options');
			$find_row = $rows[ $values[68][value] ];
			$non_alco_drinks_price = $find_row['planner_drinks_price'] * $non_alco_guests;

			$message .= '<tr>
			                <td style="border-bottom: 1px solid #ccc;" valign="middle" width="25%">
	                            <p style="font-size: 14px; font-family: Arial, sans-serif; color: #4b0126; margin: 0 0 0 20px; line-height: 50px;">
			                        Non-Alcoholic Drinks
			                    </p>
			                </td>
			                <td style="border-bottom: 1px solid #ccc;" valign="middle" width="60%">
	                            <p style="font-size: 14px; font-family: Arial, sans-serif; color: #4b0126; line-height: 50px;">
			                    	' . $non_alco_guests . ' &times; ' . $find_row['planner_drinks_name'] . '
			                	</p>
			                </td>
			                <td style="border-bottom: 1px solid #ccc;" valign="middle" width="15%" height="50px">
	                            <p style="font-size: 14px; font-family: Arial, sans-serif; color: #4b0126; margin: 0 20px 0 0; line-height: 50px;text-align: right;">
			                    	&pound; ' . number_format($non_alco_drinks_price, 2) . '
			                    </p>
			                </td>
			            </tr>';
		}

		// Children Soft Drinks

		$children_soft_drinks_price = 0;

		if ( $values['70.1'][value] != NULL ) {
			$children_soft_drinks_price = $chindren * 15;

			$message .= '<tr>
			                <td style="border-bottom: 1px solid #ccc;" valign="middle" width="25%">
	                            <p style="font-size: 14px; font-family: Arial, sans-serif; color: #4b0126; margin: 0 0 0 20px; line-height: 50px;">
			                        Children Soft Drinks
			                    </p>
			                </td>
			                <td style="border-bottom: 1px solid #ccc;" valign="middle" width="60%">
	                            <p style="font-size: 14px; font-family: Arial, sans-serif; color: #4b0126; line-height: 50px;">
			                    	' . $chindren . 'x
			                	</p>
			                </td>
			                <td style="border-bottom: 1px solid #ccc;" valign="middle" width="15%" height="50px">
	                            <p style="font-size: 14px; font-family: Arial, sans-serif; color: #4b0126; margin: 0 20px 0 0; line-height: 50px;text-align: right;">
			                    	&pound;' . number_format($children_soft_drinks_price, 2) . '
			                    </p>
			                </td>
			            </tr>';
		}
		
        // Gin Selection

        $ginselection_price = 0;

        if ( $values[47][value] != NULL ) {
        	$rows = get_field('planner_ginselection_repeater', 'options');
			$find_row = $rows[ $values[47][value] ];
			$ginselection_price = $find_row['planner_ginselection_price'];

			$message .= '<tr>
							<td style="border-bottom: 1px solid #ccc;" valign="middle" width="25%">
											<p style="font-size: 14px; font-family: Arial, sans-serif; color: #4b0126; margin: 0 0 0 20px; line-height: 50px;">
											' . get_field('planner_ginselection_keyword', 'options') . '
									</p>
							</td>
							<td style="border-bottom: 1px solid #ccc;" valign="middle" width="60%">
											<p style="font-size: 14px; font-family: Arial, sans-serif; color: #4b0126; line-height: 50px;">
										' . $find_row['planner_ginselection_name'] . '
								</p>
							</td>
							<td style="border-bottom: 1px solid #ccc;" valign="middle" width="15%" height="50px">
											<p style="font-size: 14px; font-family: Arial, sans-serif; color: #4b0126; margin: 0 20px 0 0; line-height: 50px;text-align: right;">
										&pound; ' . number_format($ginselection_price, 2) . '
									</p>
							</td>
						</tr>';
        }

        // CHAMPAGNE TOWER

        $champagne_tower_price = 0;

        if ( $values[65][value] != NULL ) {
        	$rows = get_field('planner_champagnetower_repeater', 'options');
			$find_row = $rows[ $values[65][value] ];
			$champagne_tower_price = $find_row['planner_champagnetower_price'];

			$message .= '<tr>
							<td style="border-bottom: 1px solid #ccc;" valign="middle" width="25%">
									<p style="font-size: 14px; font-family: Arial, sans-serif; color: #4b0126; margin: 0 0 0 20px; line-height: 50px;">
										Champagne Tower
									</p>
							</td>
							<td style="border-bottom: 1px solid #ccc;" valign="middle" width="60%">
								<p style="font-size: 14px; font-family: Arial, sans-serif; color: #4b0126; line-height: 50px;">
										' . $find_row['planner_champagnetower_name'] . '
								</p>
							</td>
							<td style="border-bottom: 1px solid #ccc;" valign="middle" width="15%" height="50px">
								<p style="font-size: 14px; font-family: Arial, sans-serif; color: #4b0126; margin: 0 20px 0 0; line-height: 50px;text-align: right;">
									&pound; ' . number_format($champagne_tower_price, 2) . '
								</p>
							</td>
						</tr>';
        }

        // Evening (number of guests)

        $additional_guests = 0;

        if ($values[38][value] != NULL) {
        	$additional_guests = $values[38][value];
			$total_guests = $daytime_guests + $additional_guests;

			$message .= '<tr>
							<td style="border-bottom: 1px solid #ccc;" valign="middle" width="25%">
								<p style="font-size: 14px; font-family: Arial, sans-serif; color: #4b0126; margin: 0 0 0 20px; line-height: 1.2;">
									Number of additional guests attending the evening party
								</p>
							</td>
							<td style="border-bottom: 1px solid #ccc;" valign="middle" width="60%">
								<p style="font-size: 14px; font-family: Arial, sans-serif; color: #4b0126; line-height: 50px;">
										' . $additional_guests . '
								</p>
							</td>
							<td style="border-bottom: 1px solid #ccc;" valign="middle" width="15%" height="50px">
								<p style="font-size: 14px; font-family: Arial, sans-serif; color: #4b0126; margin: 0 20px 0 0; line-height: 50px;text-align: right;">
									
								</p>
							</td>
						</tr>';
        }

		
        // Dinner (Evening Meal)

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
			if ($selected_index >= 0 && $selected_index <= 4) {
				$rows = get_field('planner_dinner_repeater', 'options' );
				$find_row = $rows[ $selected_index ];
				$evening_menu_price = $find_row['planner_dinner_price'] * $total_guests * 0.75;

				$message .= '<tr>
							<td style="border-bottom: 1px solid #ccc;" valign="middle" width="25%">
								<p style="font-size: 14px; font-family: Arial, sans-serif; color: #4b0126; margin: 0 0 0 20px; line-height: 50px;">
									Substantial Evening Menu
								</p>
							</td>
							<td style="border-bottom: 1px solid #ccc;" valign="middle" width="60%">
								<p style="font-size: 14px; font-family: Arial, sans-serif; color: #4b0126; line-height: 50px;">
										' . $find_row['planner_dinner_name'] . '
								</p>
							</td>
							<td style="border-bottom: 1px solid #ccc;" valign="middle" width="15%" height="50px">
								<p style="font-size: 14px; font-family: Arial, sans-serif; color: #4b0126; margin: 0 20px 0 0; line-height: 50px;text-align: right;">
									&pound; ' . number_format($evening_menu_price, 2) . '
								</p>
							</td>
						</tr>';

			} else {
				$rows = get_field('planner_dinner_s_repeater', 'options' );
				$find_row = $rows[ ($selected_index - 5) ];
				$evening_menu_price = $find_row['planner_dinner_s_price'] * $total_guests;

				$message .= '<tr>
							<td style="border-bottom: 1px solid #ccc;" valign="middle" width="25%">
								<p style="font-size: 14px; font-family: Arial, sans-serif; color: #4b0126; margin: 0 0 0 20px; line-height: 50px;">
									Simple Evening Menu
								</p>
							</td>
							<td style="border-bottom: 1px solid #ccc;" valign="middle" width="60%">
								<p style="font-size: 14px; font-family: Arial, sans-serif; color: #4b0126; line-height: 50px;">
										' . $find_row['title'] . '
								</p>
							</td>
							<td style="border-bottom: 1px solid #ccc;" valign="middle" width="15%" height="50px">
								<p style="font-size: 14px; font-family: Arial, sans-serif; color: #4b0126; margin: 0 20px 0 0; line-height: 50px;text-align: right;">
									&pound; ' . number_format($evening_menu_price, 2) . '
								</p>
							</td>
						</tr>';
			} 
		}

		// Something Extra

		$crepe_station_price = 0;
		$ice_cream_trike_price = 0;
		$cheese_tower_price = 0;
		$extra_repeater = get_field('planner_something_extra_repeater', 'option');

		if ( $values['75.1'][value] != NULL && !empty($extra_repeater)) {
			$crepe_station_price = $extra_repeater[0]['planner_relaxed_dining_price'];

			$message .= '<tr>
							<td style="border-bottom: 1px solid #ccc;" valign="middle" width="25%">
								<p style="font-size: 14px; font-family: Arial, sans-serif; color: #4b0126; margin: 0 0 0 20px; line-height: 50px;">
									' . $extra_repeater[0]['planner_relaxed_dining_title'] . '
								</p>
							</td>
							<td style="border-bottom: 1px solid #ccc;" valign="middle" width="60%">
								<p style="font-size: 14px; font-family: Arial, sans-serif; color: #4b0126; line-height: 50px;">
										Yes
								</p>
							</td>
							<td style="border-bottom: 1px solid #ccc;" valign="middle" width="15%" height="50px">
								<p style="font-size: 14px; font-family: Arial, sans-serif; color: #4b0126; margin: 0 20px 0 0; line-height: 50px;text-align: right;">
									&pound; ' . number_format($crepe_station_price, 2) . '
								</p>
							</td>
						</tr>';
		}

		if ( $values['75.2'][value] != NULL && !empty($extra_repeater)) {
			$ice_cream_trike_price = $extra_repeater[1]['planner_relaxed_dining_price'];

			$message .= '<tr>
							<td style="border-bottom: 1px solid #ccc;" valign="middle" width="25%">
								<p style="font-size: 14px; font-family: Arial, sans-serif; color: #4b0126; margin: 0 0 0 20px; line-height: 50px;">
									' . $extra_repeater[1]['planner_relaxed_dining_title'] . '
								</p>
							</td>
							<td style="border-bottom: 1px solid #ccc;" valign="middle" width="60%">
								<p style="font-size: 14px; font-family: Arial, sans-serif; color: #4b0126; line-height: 50px;">
										Yes
								</p>
							</td>
							<td style="border-bottom: 1px solid #ccc;" valign="middle" width="15%" height="50px">
								<p style="font-size: 14px; font-family: Arial, sans-serif; color: #4b0126; margin: 0 20px 0 0; line-height: 50px;text-align: right;">
									&pound; ' . number_format($ice_cream_trike_price, 2) . '
								</p>
							</td>
						</tr>';
		}

		if ( $values['75.3'][value] != NULL && !empty($extra_repeater)) {
			$cheese_tower_price = $extra_repeater[2]['planner_relaxed_dining_price'];

			$message .= '<tr>
							<td style="border-bottom: 1px solid #ccc;" valign="middle" width="25%">
								<p style="font-size: 14px; font-family: Arial, sans-serif; color: #4b0126; margin: 0 0 0 20px; line-height: 50px;">
									' . $extra_repeater[2]['planner_relaxed_dining_title'] . '
								</p>
							</td>
							<td style="border-bottom: 1px solid #ccc;" valign="middle" width="60%">
								<p style="font-size: 14px; font-family: Arial, sans-serif; color: #4b0126; line-height: 50px;">
										Yes
								</p>
							</td>
							<td style="border-bottom: 1px solid #ccc;" valign="middle" width="15%" height="50px">
								<p style="font-size: 14px; font-family: Arial, sans-serif; color: #4b0126; margin: 0 20px 0 0; line-height: 50px;text-align: right;">
									&pound; ' . number_format($cheese_tower_price, 2) . '
								</p>
							</td>
						</tr>';
		}

		// Extended Bar Hours
		$bar_price = 0;

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
			                    	Yes
			                	</p>
			                </td>
			                <td style="border-bottom: 1px solid #ccc;" valign="middle" width="15%" height="50px">
	                            <p style="font-size: 14px; font-family: Arial, sans-serif; color: #4b0126; margin: 0 20px 0 0; line-height: 50px;text-align: right;">
			                    	&pound;' . number_format($bar_price, 2) . '
			                    </p>
			                </td>
			            </tr>';
		}


		// After Hours
		$charcuterie_price = 0;

		if ( $values[49][value] != NULL ) {
			$charcuterie_price = get_field('planner_charcuterie_price', 'options') * $values[49][value];

            $message .= '<tr>
			                <td style="border-bottom: 1px solid #ccc;" valign="middle" width="25%">
	                            <p style="font-size: 14px; font-family: Arial, sans-serif; color: #4b0126; margin: 0 0 0 20px; line-height: 50px;">
			                        Charcuterie (After Hours)
			                    </p>
			                </td>
			                <td style="border-bottom: 1px solid #ccc;" valign="middle" width="60%">
	                            <p style="font-size: 14px; font-family: Arial, sans-serif; color: #4b0126; line-height: 50px;">
			                    	' . $values[49][value] . '
			                	</p>
			                </td>
			                <td style="border-bottom: 1px solid #ccc;" valign="middle" width="15%" height="50px">
	                            <p style="font-size: 14px; font-family: Arial, sans-serif; color: #4b0126; margin: 0 20px 0 0; line-height: 50px;text-align: right;">
			                    	&pound; ' . number_format($charcuterie_price, 2) . '
			                    </p>
			                </td>
			            </tr>';
		}

		$cheeseboard_price = 0;
		
		if ( $values[50][value] != NULL ) {
			$cheeseboard_price = get_field('planner_cheeseboard_price', 'options') * $values[50][value];

            $message .= '<tr>
			                <td style="border-bottom: 1px solid #ccc;" valign="middle" width="25%">
	                            <p style="font-size: 14px; font-family: Arial, sans-serif; color: #4b0126; margin: 0 0 0 20px; line-height: 50px;">
			                        Cheeseboard (After Hours)
			                    </p>
			                </td>
			                <td style="border-bottom: 1px solid #ccc;" valign="middle" width="60%">
	                            <p style="font-size: 14px; font-family: Arial, sans-serif; color: #4b0126; line-height: 50px;">
			                    	' . $values[50][value] . '
			                	</p>
			                </td>
			                <td style="border-bottom: 1px solid #ccc;" valign="middle" width="15%" height="50px">
	                            <p style="font-size: 14px; font-family: Arial, sans-serif; color: #4b0126; margin: 0 20px 0 0; line-height: 50px;text-align: right;">
			                    	&pound; ' . number_format($cheeseboard_price, 2) . '
			                    </p>
			                </td>
			            </tr>';
		}

		// Bedrooms

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

			$message .= '<tr>
			                <td style="border-bottom: 1px solid #ccc;" valign="middle" width="25%">
	                            <p style="font-size: 14px; font-family: Arial, sans-serif; color: #4b0126; margin: 0 0 0 20px; line-height: 50px;">
			                        '. $bedrooms_repeater[0]['planner_bedrooms_name'] .'
			                    </p>
			                </td>
			                <td style="border-bottom: 1px solid #ccc;" valign="middle" width="60%">
	                            <p style="font-size: 14px; font-family: Arial, sans-serif; color: #4b0126; line-height: 50px;">
			                    	Yes
			                	</p>
			                </td>
			                <td style="border-bottom: 1px solid #ccc;" valign="middle" width="15%" height="50px">
	                            <p style="font-size: 14px; font-family: Arial, sans-serif; color: #4b0126; margin: 0 20px 0 0; line-height: 50px;text-align: right;">
			                    	&pound; ' . number_format($honeymoon_suite_price, 2) . '
			                    </p>
			                </td>
			            </tr>';
		}

		if ( $values['27.2'][value] != NULL && !empty($bedrooms_repeater)) {
			if ($year == '2025') {
			      $silver_suite_price = $bedrooms_repeater[1]['planner_bedrooms_price'];
			  } elseif ($year == '2026') {
			      $silver_suite_price = $bedrooms_repeater[1]['planner_bedrooms_price_2026'];
			  } elseif ($year == '2027') {
			      $silver_suite_price = $bedrooms_repeater[1]['planner_bedrooms_price_2027'];
			}

			$message .= '<tr>
			                <td style="border-bottom: 1px solid #ccc;" valign="middle" width="25%">
	                            <p style="font-size: 14px; font-family: Arial, sans-serif; color: #4b0126; margin: 0 0 0 20px; line-height: 50px;">
			                        '. $bedrooms_repeater[1]['planner_bedrooms_name'] .'
			                    </p>
			                </td>
			                <td style="border-bottom: 1px solid #ccc;" valign="middle" width="60%">
	                            <p style="font-size: 14px; font-family: Arial, sans-serif; color: #4b0126; line-height: 50px;">
			                    	Yes
			                	</p>
			                </td>
			                <td style="border-bottom: 1px solid #ccc;" valign="middle" width="15%" height="50px">
	                            <p style="font-size: 14px; font-family: Arial, sans-serif; color: #4b0126; margin: 0 20px 0 0; line-height: 50px;text-align: right;">
			                    	&pound; ' . number_format($silver_suite_price, 2) . '
			                    </p>
			                </td>
			            </tr>';
		}

		if ( $values['27.3'][value] != NULL && !empty($bedrooms_repeater)) {
			if ($year == '2025') {
			      $double_bedroom_price = $bedrooms_repeater[2]['planner_bedrooms_price'] * $values[77][value];
			  } elseif ($year == '2026') {
			      $double_bedroom_price = $bedrooms_repeater[2]['planner_bedrooms_price_2026'] * $values[77][value];
			  } elseif ($year == '2027') {
			      $double_bedroom_price = $bedrooms_repeater[2]['planner_bedrooms_price_2027'] * $values[77][value];
			}

			$message .= '<tr>
			                <td style="border-bottom: 1px solid #ccc;" valign="middle" width="25%">
	                            <p style="font-size: 14px; font-family: Arial, sans-serif; color: #4b0126; margin: 0 0 0 20px; line-height: 50px;">
			                        '. $bedrooms_repeater[2]['planner_bedrooms_name'] .'
			                    </p>
			                </td>
			                <td style="border-bottom: 1px solid #ccc;" valign="middle" width="60%">
	                            <p style="font-size: 14px; font-family: Arial, sans-serif; color: #4b0126; line-height: 50px;">
			                    	'. $values[77][value] .'x
			                	</p>
			                </td>
			                <td style="border-bottom: 1px solid #ccc;" valign="middle" width="15%" height="50px">
	                            <p style="font-size: 14px; font-family: Arial, sans-serif; color: #4b0126; margin: 0 20px 0 0; line-height: 50px;text-align: right;">
			                    	&pound; ' . number_format($double_bedroom_price, 2) . '
			                    </p>
			                </td>
			            </tr>';
		}

		if ( $values['27.4'][value] != NULL && !empty($bedrooms_repeater)) {
			if ($year == '2025') {
			      $twin_room_price = $bedrooms_repeater[3]['planner_bedrooms_price'];
			  } elseif ($year == '2026') {
			      $twin_room_price = $bedrooms_repeater[3]['planner_bedrooms_price_2026'];
			  } elseif ($year == '2027') {
			      $twin_room_price = $bedrooms_repeater[3]['planner_bedrooms_price_2027'];
			}

			$message .= '<tr>
			                <td style="border-bottom: 1px solid #ccc;" valign="middle" width="25%">
	                            <p style="font-size: 14px; font-family: Arial, sans-serif; color: #4b0126; margin: 0 0 0 20px; line-height: 50px;">
			                        '. $bedrooms_repeater[3]['planner_bedrooms_name'] .'
			                    </p>
			                </td>
			                <td style="border-bottom: 1px solid #ccc;" valign="middle" width="60%">
	                            <p style="font-size: 14px; font-family: Arial, sans-serif; color: #4b0126; line-height: 50px;">
			                    	Yes
			                	</p>
			                </td>
			                <td style="border-bottom: 1px solid #ccc;" valign="middle" width="15%" height="50px">
	                            <p style="font-size: 14px; font-family: Arial, sans-serif; color: #4b0126; margin: 0 20px 0 0; line-height: 50px;text-align: right;">
			                    	&pound; ' . number_format($twin_room_price, 2) . '
			                    </p>
			                </td>
			            </tr>';
		}

		// Bedrooms zbeds

		$bedrooms_zbeds_price = 0;

		if ( $values[78][value] != NULL ) {
			if ($year == '2025') {
			      $bedrooms_zbeds_price = 38 * $values[78][value];
			  } elseif ($year == '2026') {
			      $bedrooms_zbeds_price = 40 * $values[78][value];
			  } elseif ($year == '2027') {
			      $bedrooms_zbeds_price = 42 * $values[78][value];
			}

			$message .= '<tr>
			                <td style="border-bottom: 1px solid #ccc;" valign="middle" width="25%">
	                            <p style="font-size: 14px; font-family: Arial, sans-serif; color: #4b0126; margin: 0 0 0 20px; line-height: 50px;">
			                        Number of Z Beds
			                    </p>
			                </td>
			                <td style="border-bottom: 1px solid #ccc;" valign="middle" width="60%">
	                            <p style="font-size: 14px; font-family: Arial, sans-serif; color: #4b0126; line-height: 50px;">
			                    	'. $values[78][value] .'
			                	</p>
			                </td>
			                <td style="border-bottom: 1px solid #ccc;" valign="middle" width="15%" height="50px">
	                            <p style="font-size: 14px; font-family: Arial, sans-serif; color: #4b0126; margin: 0 20px 0 0; line-height: 50px;text-align: right;">
			                    	&pound; ' . number_format($bedrooms_zbeds_price, 2) . '
			                    </p>
			                </td>
			            </tr>';
		}

		// Extra Breakfasts

		$extra_breakfasts_price = 0;

		if ( $values[79][value] != NULL ) {
			if ($year == '2025') {
			      $extra_breakfasts_price = 20 * $values[79][value];
			  } elseif ($year == '2026') {
			      $extra_breakfasts_price = 22 * $values[79][value];
			  } elseif ($year == '2027') {
			      $extra_breakfasts_price = 24 * $values[79][value];
			}

			$message .= '<tr>
			                <td style="border-bottom: 1px solid #ccc;" valign="middle" width="25%">
	                            <p style="font-size: 14px; font-family: Arial, sans-serif; color: #4b0126; margin: 0 0 0 20px; line-height: 50px;">
			                        Extra Breakfasts
			                    </p>
			                </td>
			                <td style="border-bottom: 1px solid #ccc;" valign="middle" width="60%">
	                            <p style="font-size: 14px; font-family: Arial, sans-serif; color: #4b0126; line-height: 50px;">
			                    	'. $values[79][value] .'x
			                	</p>
			                </td>
			                <td style="border-bottom: 1px solid #ccc;" valign="middle" width="15%" height="50px">
	                            <p style="font-size: 14px; font-family: Arial, sans-serif; color: #4b0126; margin: 0 20px 0 0; line-height: 50px;text-align: right;">
			                    	&pound; ' . number_format($extra_breakfasts_price, 2) . '
			                    </p>
			                </td>
			            </tr>';
		}

		// Sum Total

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

