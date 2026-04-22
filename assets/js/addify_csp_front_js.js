jQuery(document).ready(function ($) {
		
		//for simple product only
		if(!$('.single_variation').length && !$('.product-type-grouped').length){

			//if table is shown then handle the price change
			if($('.dynamic_price_display').length){
				var className = '';

				var initialPrice = $('.entry-summary .price ins .woocommerce-Price-amount bdi').text();
				
				if(initialPrice == ''){
					className = '.entry-summary .price .woocommerce-Price-amount bdi';
					initialPrice = $(className).text();
				}
				else{
					className = '.entry-summary .price ins .woocommerce-Price-amount bdi';
				}

				setTimeout(function(){
					$('.qty').trigger('change');
				},10);

				$('.qty').on('input change', function(){
					var newValue = parseInt($(this).val()); 
					
					$('table.dynamic_price_display tbody tr').each(function() {
						var minQuantity = parseInt($(this).find('td:nth-child(1)').text());
						var maxQuantity = isNaN(parseInt($(this).find('td:nth-child(2)').text()))?0:parseInt($(this).find('td:nth-child(2)').text());
						var priceText = $(this).find('td:nth-child(3)').text(); 
						var actualPrice = $(this).find('td:nth-child(5)').text(); 
						var replace_price = $(this).find('td:first').data('replace');		
						
						if ((newValue >= minQuantity && newValue <= maxQuantity) || (newValue >= minQuantity && 0 == maxQuantity)) {
							
							$(className).html('<span class="woocommerce-Price-currencySymbol">' + priceText + '</span>' );
							if('yes' == replace_price){
								$('.entry-summary .price del').hide();
								$('.b2b-role-based-custom-price').remove();
							}
							else{
								$('.entry-summary .price del').hide();
								$('.b2b-role-based-custom-price').remove();
								var delElement = '<del class="b2b-role-based-custom-price"><span class="woocommerce-Price-amount amount">' + actualPrice + '</span></del>';
								$('.entry-summary .price').prepend(delElement);
							}
							return false; 
						}
					});

					if (!$('table.dynamic_price_display tbody tr').is(function() {
						return (newValue >= parseInt($(this).find('td:nth-child(1)').text()) && newValue <= parseInt($(this).find('td:nth-child(2)').text()) || (newValue >= parseInt($(this).find('td:nth-child(1)').text()) && (0 == parseInt($(this).find('td:nth-child(2)').text()) || isNaN(parseInt($(this).find('td:nth-child(2)').text())))));
					})) {
						$('.afb2b_role_list_box').each(function(){
								$(this).removeClass('afb2b_role_selected_list')
							})
							$('[name=offer]').each(function(){
								$(this).prop('checked', false);
							})
							$('.afb2b_role_inner_small_box').each(function(){
								$(this).removeClass('afb2b_role_selected_card');
							})
						$('.entry-summary .price del').show();
						$(className).text(initialPrice);
						$('.b2b-role-based-custom-price').remove();
					}
				});
			}
		}

		//for variation product
		$('.variations select').on('change', function() {
			setTimeout(function(){
				//if table is shown then handle the price change
				if($('.dynamic_price_display').length){
				
					var variation_id = $('.variation_id').val();

					$('.afb2b_role_radio_div').append('<input type="radio" name="offer" />');

				
					if(variation_id != '' && variation_id != '0' ){
		
						$.ajax({
							url: afb2b_role_php_vars.admin_url,
							type: 'POST',
							data: {
								action: 'afb2b_role_get_variation_price',
								nonce :  afb2b_role_php_vars.nonce,
								variation_id:variation_id
							},
							success: function (response) {
								// var original_price_formatted = response.data.price;	
								var original_price_formatted = response.data.formatted_price;													
												
								//changing price on quantity change for variable price
								if($('.single_variation').length){
									var className = '';
								
									var initialPrice = $('.single_variation .price ins .woocommerce-Price-amount bdi').text();
									
									if(initialPrice == ''){
										className = '.single_variation .price .woocommerce-Price-amount bdi';
									}
									else{
										className = '.single_variation .price ins .woocommerce-Price-amount bdi';
									}
		
									initialPrice = original_price_formatted;
									
		
									function handleQuantityChange(newValue, className) {
		
										$('table.dynamic_price_display tbody tr').each(function() {
											var minQuantity = parseInt($(this).find('td:nth-child(1)').text());
											var maxQuantity = isNaN(parseInt($(this).find('td:nth-child(2)').text()))?0:parseInt($(this).find('td:nth-child(2)').text());
											var priceText = $(this).find('td:nth-child(3)').text(); 
											var actualPrice = $(this).find('td:nth-child(5)').text(); 
											var replace_price = $(this).find('td:first').data('replace');
		
											
											if ((newValue >= minQuantity && newValue <= maxQuantity) || (newValue >= minQuantity && 0 == maxQuantity)) {
												$(className).html('<span class="woocommerce-Price-currencySymbol">' + priceText + '</span>');

												$(className).html('<span class="woocommerce-Price-currencySymbol">' + priceText + '</span>' );
												if('yes' == replace_price){
													$('.entry-summary .single_variation_wrap .price del').hide();
													$('.b2b-role-based-custom-price').remove();
												}
												else{
														$('.entry-summary .single_variation_wrap .price del').hide();
														$('.b2b-role-based-custom-price').remove();
														var delElement = '<del class="b2b-role-based-custom-price"><span class="woocommerce-Price-amount amount">' + actualPrice + '</span></del>';
														$('.entry-summary .single_variation_wrap .price').prepend(delElement);
												}
		
												return false; 
											}
										});
		
										if (!$('table.dynamic_price_display tbody tr').is(function() {
											return (newValue >= parseInt($(this).find('td:nth-child(1)').text()) && newValue <= parseInt($(this).find('td:nth-child(2)').text()) || (newValue >= parseInt($(this).find('td:nth-child(1)').text()) && (0 == parseInt($(this).find('td:nth-child(2)').text()) || isNaN(parseInt($(this).find('td:nth-child(2)').text())))));
										})) {
											$('.afb2b_role_list_box').each(function(){
												$(this).removeClass('afb2b_role_selected_list')
											})
											$('[name=offer]').each(function(){
												$(this).prop('checked', false);
											})
											$('.afb2b_role_inner_small_box').each(function(){
												$(this).removeClass('afb2b_role_selected_card');
											})
											// $('.entry-summary .price del').show();
											// $(className).text(initialPrice);
											// $('.b2b-role-based-custom-price').remove();
											let initial = parseFloat(initialPrice.replace(/[^0-9.]/g, '')) || 0;
											let originalText = $('.entry-summary .price del').text().replace(/[^0-9.]/g, '');
											let original = parseFloat(originalText) || 0;

											if (!isNaN(initial) && !isNaN(original) && original > 0) {
												if (initial <= original) {
													$('.entry-summary .price del').hide();
												} else {
													$('.entry-summary .price del').show();
												}

												$(className).text(initialPrice);
												$('.b2b-role-based-custom-price').remove();
											}
											else{
												$('.entry-summary .price del').show();
												$(className).text(initialPrice);
												$('.b2b-role-based-custom-price').remove();
											}

										}
									}
		
									$(document).ready(function() {
										var initialValue = parseInt($('.qty').val()); 
										handleQuantityChange(initialValue, className); 
									});
		
									$(document).on('input change','.qty', function() { 
										var newValue = parseInt($(this).val()); 
										handleQuantityChange(newValue, className); 
									});
								}
							}
						});
					}
				}
			}, 10);
			
		});


		setTimeout(function(){
			$('.variations select').trigger('change');
		},10);

		//card click logic
		$(document).on('click' ,'.afb2b_role_inner_small_box',function(){
			var min_qty = $(this).data('min-qty');
			if(min_qty>0){
				$('.qty').val(min_qty).trigger('change');
				$('.afb2b_role_inner_small_box').each(function(){
					$(this).removeClass('afb2b_role_selected_card');
				})
				$(this).addClass('afb2b_role_selected_card')
			}
		})


		$('.afb2b_role_radio_div').append('<input type="radio" name="offer" />');
	
		$(document).on('click','.afb2b_role_list_box',function(){
				var min_qty = $(this).data('min-qty');
		
				$(this).find('input[type="radio"]').prop('checked', true);
				$('.qty').val(min_qty).trigger('change');
		
				$('.afb2b_role_list_box').each(function(){
					$(this).removeClass('afb2b_role_selected_list');
				})
				$(this).addClass('afb2b_role_selected_list')
		
		})
	

});







