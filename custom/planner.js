
jQuery(document).ready(function($){

	var $selected_year = 2025;

	$('#input_1_1').on('change', function() {
	      var dateValue = $(this).val();
	      if (dateValue) {
	          var parts = dateValue.split('/');
	          var $selected_year = parts[2];
	          console.log("Selected year:", $selected_year);
	      }

	    if ($selected_year == '2025') {
            $('.gchoice_1_57_0 .dressing-1-price-placeholder').text( $('.dressing-1-price .dressing-price-2025').text() );
            $('.gchoice_1_57_1 .dressing-2-price-placeholder').text( $('.dressing-2-price .dressing-price-2025').text() );
        } else if ($selected_year == '2026') {
        	$('.gchoice_1_57_0 .dressing-1-price-placeholder').text( $('.dressing-1-price .dressing-price-2026').text() );
            $('.gchoice_1_57_1 .dressing-2-price-placeholder').text( $('.dressing-2-price .dressing-price-2026').text() );
        } else if ($selected_year == '2027') {
        	$('.gchoice_1_57_0 .dressing-1-price-placeholder').text( $('.dressing-1-price .dressing-price-2027').text() );
            $('.gchoice_1_57_1 .dressing-2-price-placeholder').text( $('.dressing-2-price .dressing-price-2027').text() );
        }
	});

	function planner() {

		$('.booking-form').show();

		$('.booking-form input[type=radio] + label, .booking-form input[type=checkbox] + label').html('Select');
		$('.booking-form input[type=radio] + label#label_1_29_0').html('1');
		$('.booking-form input[type=radio] + label#label_1_29_1').html('2');
		$('.booking-form input[type=radio] + label#label_1_29_2').html('3');
		
		// plan
		$('.plan-first-choice').before( $('.plan-title') ).before( $('.plan-direction') );
		$('.gchoice_1_8_0').prepend( $('.plan-1-item') ).children('.plan-1-item').after('<hr>');
		$('.gchoice_1_8_1').prepend( $('.plan-2-item') ).children('.plan-2-item').after('<hr>');
		$('.gchoice_1_8_2').prepend( $('.plan-3-item') ).children('.plan-3-item').after('<hr>');

		var planHeights = Math.max($('.gchoice_1_8_0 p').height(), $('.gchoice_1_8_1 p').height(), $('.gchoice_1_8_2 p').height());
		$('.gchoice_1_8_0 p, .gchoice_1_8_1 p, .gchoice_1_8_2 p').height(planHeights);

		// daytime (number of guests)

		$('.daytime-guests').before( $('.daytime-title') ).before( $('.daytime-direction') );
		$('#field_1_9 label').text( $('.daytime-label').text() );

		// ceremony type
		$('.ceremony-type').before( $('.ceremony-type-title') ).before( '<br>' );

		//dressing
		$('.dressing-rooms').before( $('.dressingrooms-title') );

        // $('.gchoice_1_57_0').prepend( $('.dressing-1-description') ).children('.dressing-1-description').after('<hr>');
        // $('.gchoice_1_57_0').prepend( $('.dressing-1-price-placeholder') );
		// $('.gchoice_1_57_0').prepend( $('.dressing-1-name') ).children('.dressing-1-name').after('<hr>');
		// $('.gchoice_1_57_0 .dressing-1-price-placeholder').text( $('.dressing-1-price .dressing-price-2025').text() );

		// $('.gchoice_1_57_1').prepend( $('.dressing-2-description') ).children('.dressing-2-description').after('<hr>');
        // $('.gchoice_1_57_1').prepend( $('.dressing-2-price-placeholder') );
		// $('.gchoice_1_57_1').prepend( $('.dressing-2-name') ).children('.dressing-2-name').after('<hr>');
		// $('.gchoice_1_57_1 .dressing-2-price-placeholder').text( $('.dressing-2-price .dressing-price-2025').text() );

		$('.gchoice_1_80_1').prepend( $('.dressing-1-description') ).children('.dressing-1-description').after('<hr>');
        $('.gchoice_1_80_1').prepend( $('.dressing-1-price-placeholder') );
		$('.gchoice_1_80_1').prepend( $('.dressing-1-name') ).children('.dressing-1-name').after('<hr>');
		$('.gchoice_1_80_1 .dressing-1-price-placeholder').text( $('.dressing-1-price .dressing-price-2025').text() );

		$('.gchoice_1_80_2').prepend( $('.dressing-2-description') ).children('.dressing-2-description').after('<hr>');
        $('.gchoice_1_80_2').prepend( $('.dressing-2-price-placeholder') );
		$('.gchoice_1_80_2').prepend( $('.dressing-2-name') ).children('.dressing-2-name').after('<hr>');
		$('.gchoice_1_80_2 .dressing-2-price-placeholder').text( $('.dressing-2-price .dressing-price-2025').text() );
		
		// PRE WEDDING BREAKFAST
		// $('.canapes').before( $('.canapes-title') ).prepend( $('.canapes-direction') );

		// canapes

		$('.canapes').before( $('.canapes-title') ).prepend( $('.canapes-direction') );

		$('.gchoice_1_7_0').prepend( $('.canapes-1-description') ).children('.canapes-1-description').after('<hr>');
		$('.gchoice_1_7_0').prepend( $('.canapes-1-fancy') ).children('.canapes-1-fancy').after('<hr>');
		$('.gchoice_1_7_0').prepend( $('.canapes-1-price') );
		$('.gchoice_1_7_0').prepend( $('.canapes-1-name') ).children('.canapes-1-name').after('<hr>');

		$('.gchoice_1_7_1').prepend( $('.canapes-2-description') ).children('.canapes-2-description').after('<hr>');;
		$('.gchoice_1_7_1').prepend( $('.canapes-2-fancy') ).children('.canapes-2-fancy').after('<hr>');
		$('.gchoice_1_7_1').prepend( $('.canapes-2-price') );
		$('.gchoice_1_7_1').prepend( $('.canapes-2-name') ).children('.canapes-2-name').after('<hr>');

		$('.gchoice_1_7_2').prepend( $('.canapes-3-description') ).children('.canapes-3-description').after('<hr>');;
		$('.gchoice_1_7_2').prepend( $('.canapes-3-fancy') ).children('.canapes-3-fancy').after('<hr>');
		$('.gchoice_1_7_2').prepend( $('.canapes-3-price') );
		$('.gchoice_1_7_2').prepend( $('.canapes-3-name') ).children('.canapes-3-name').after('<hr>');

		$('.gchoice_1_7_3').prepend( $('.canapes-4-description') ).children('.canapes-4-description').after('<hr>');;
		$('.gchoice_1_7_3').prepend( $('.canapes-4-fancy') ).children('.canapes-4-fancy').after('<hr>');
		$('.gchoice_1_7_3').prepend( $('.canapes-4-price') );
		$('.gchoice_1_7_3').prepend( $('.canapes-4-name') ).children('.canapes-4-name').after('<hr>');

		// var canapesHeights = Math.max($('.gchoice_1_7_0 h4').height(), $('.gchoice_1_7_1 h4').height(), $('.gchoice_1_7_2 h4').height(), $('.gchoice_1_7_3 h4').height());
		// $('.gchoice_1_7_0 h4, .gchoice_1_7_1 h4, .gchoice_1_7_2 h4, .gchoice_1_7_3 h4').height(canapesHeights);

		// breakfast

		$('.breakfast').before( $('.breakfast-packages-title') ).before( $('.breakfast-title') ).before( $('.breakfast-direction') );

		$('.gchoice_1_10_0').prepend( $('.breakfast-menu-link-1') ).children('a').after('<hr>');
		$('.gchoice_1_10_0').prepend( $('.breakfast-1-description') );
		$('.gchoice_1_10_0').prepend( $('.breakfast-1-price') ).children('.breakfast-1-price').after('<hr>');
		$('.gchoice_1_10_0').prepend( $('.breakfast-1-name') ).children('.breakfast-1-name').after('<hr>');
		$('.gchoice_1_10_0').prepend( '<h5 style="text-transform: none;">Traditional Three Course Wedding Breakfast</h5>' ).children('h5').after('<hr>');

		$('.gchoice_1_10_1').prepend( $('.breakfast-menu-link-2') ).children('a').after('<hr>');
		$('.gchoice_1_10_1').prepend( $('.breakfast-2-description') );
		$('.gchoice_1_10_1').prepend( $('.breakfast-2-price') ).children('.breakfast-2-price').after('<hr>');
		$('.gchoice_1_10_1').prepend( $('.breakfast-2-name') ).children('.breakfast-2-name').after('<hr>');
		$('.gchoice_1_10_1').prepend( '<h5 style="text-transform: none;">Traditional Three Course Wedding Breakfast</h5>' ).children('h5').after('<hr>');

		$('.gchoice_1_10_2').prepend( $('.breakfast-menu-link-3') ).children('a').after('<hr>');
		$('.gchoice_1_10_2').prepend( $('.breakfast-3-description') );
		$('.gchoice_1_10_2').prepend( $('.breakfast-3-price') ).children('.breakfast-3-price').after('<hr>');
		$('.gchoice_1_10_2').prepend( $('.breakfast-3-name') ).children('.breakfast-3-name').after('<hr>');
		$('.gchoice_1_10_2').prepend( '<h5 style="text-transform: none;">Traditional Three Course Wedding Breakfast</h5>' ).children('h5').after('<hr>');

		$('.gchoice_1_10_3').prepend( $('.breakfast-menu-link-4') ).children('a').after('<hr>');
		$('.gchoice_1_10_3').prepend( $('.breakfast-4-description') );
		$('.gchoice_1_10_3').prepend( $('.breakfast-4-price') ).children('.breakfast-4-price').after('<hr>');
		$('.gchoice_1_10_3').prepend( $('.breakfast-4-name') ).children('.breakfast-4-name').after('<hr>');
		$('.gchoice_1_10_3').prepend( '<h5 style="text-transform: none;">Traditional Three Course Wedding Breakfast</h5>' ).children('h5').after('<hr>');

		// $('.breakfast').after( $('.breakfast-information') );

		var breakfastHeights = Math.max($('.gchoice_1_10_0 span.description').height(), $('.gchoice_1_10_1 span.description').height(), $('.gchoice_1_10_2 span.description').height(), $('.gchoice_1_10_3 span.description').height());
		$('.gchoice_1_10_0 span.description, .gchoice_1_10_1 span.description, .gchoice_1_10_2 span.description, .gchoice_1_10_3 span.description').height(breakfastHeights);

		// crew food
		$('#field_1_62').before( $('.crew-title') ).before( $('.crew-direction') );
		
		// relaxed dining options

		$('.relaxed-dining').before( $('.relaxed-dining-title') );

		$('.gchoice_1_66_0').prepend( $('.relaxed-dining-1-fancy') ).children('.relaxed-dining-1-fancy').after('<hr>');
		$('.gchoice_1_66_0').prepend( $('.relaxed-dining-1-price') );
		$('.gchoice_1_66_0').prepend( $('.relaxed-dining-1-name') ).children('.relaxed-dining-1-name').after('<hr>');

		$('.gchoice_1_66_1').prepend( $('.relaxed-dining-2-fancy') ).children('.relaxed-dining-2-fancy').after('<hr>');
		$('.gchoice_1_66_1').prepend( $('.relaxed-dining-2-price') );
		$('.gchoice_1_66_1').prepend( $('.relaxed-dining-2-name') ).children('.relaxed-dining-2-name').after('<hr>');

		$('.gchoice_1_66_2').prepend( $('.relaxed-dining-3-fancy') ).children('.relaxed-dining-3-fancy').after('<hr>');
		$('.gchoice_1_66_2').prepend( $('.relaxed-dining-3-price') );
		$('.gchoice_1_66_2').prepend( $('.relaxed-dining-3-name') ).children('.relaxed-dining-3-name').after('<hr>');

		// Asian catering
		$('.asian-catering').before( $('.asian-catering-title') ).before( $('.asian-catering-direction') );

		$('.gchoice_1_67_1').prepend( $('.asian-catering-price') ).children('.asian-catering-price').after('<hr>');
		$('.gchoice_1_67_1').prepend( $('.asian-catering-name') ).children('.asian-catering-name').after('<hr>');

		//chidren menu
		$('.gchoice_1_64_0').prepend( $('.children-2-course-popup-link') ).children('a').after('<hr>');
		$('.gchoice_1_64_0').prepend('<p class="children-menu-price">£28.00 per head</p>');
		$('.gchoice_1_64_0').prepend("<h4>Childrens 2 Course</h4>").children('h4').after('<hr>');

		$('.gchoice_1_64_1').prepend( $('.children-3-course-popup-link') ).children('a').after('<hr>');
		$('.gchoice_1_64_1').prepend('<p class="children-menu-price">£34.00 per head</p>');
		$('.gchoice_1_64_1').prepend("<h4>Childrens 3 Course</h4>").children('h4').after('<hr>');


		// drinks

		$('.drinks:not(.ginselection)').before( $('.drinks-title') ).before( $('.drinks-direction') );
		
		$('.gchoice_1_11_0').prepend( $('.drinks-1-description') ).children('.drinks-1-description').after('<hr>');
		$('.gchoice_1_11_0').prepend( $('.drinks-1-price') ).children('.drinks-1-price').after('<hr>');
		$('.gchoice_1_11_0').prepend( $('.drinks-1-name') ).children('.drinks-1-name').after('<hr>');
		
		$('.gchoice_1_11_1').prepend( $('.drinks-2-description') ).children('.drinks-2-description').after('<hr>');
		$('.gchoice_1_11_1').prepend( $('.drinks-2-price') ).children('.drinks-2-price').after('<hr>');
		$('.gchoice_1_11_1').prepend( $('.drinks-2-name') ).children('.drinks-2-name').after('<hr>');
		
		$('.gchoice_1_11_2').prepend( $('.drinks-3-description') ).children('.drinks-3-description').after('<hr>');
		$('.gchoice_1_11_2').prepend( $('.drinks-3-price') ).children('.drinks-3-price').after('<hr>');
		$('.gchoice_1_11_2').prepend( $('.drinks-3-name') ).children('.drinks-3-name').after('<hr>');
		
		$('.gchoice_1_11_3').prepend( $('.drinks-4-description') ).children('.drinks-4-description').after('<hr>');
		$('.gchoice_1_11_3').prepend( $('.drinks-4-price') ).children('.drinks-4-price').after('<hr>');
		$('.gchoice_1_11_3').prepend( $('.drinks-4-name') ).children('.drinks-4-name').after('<hr>');
		
		var drinksHeights = Math.max($('.gchoice_1_11_0 span.description').height(), $('.gchoice_1_11_1 span.description').height(), $('.gchoice_1_11_2 span.description').height(), $('.gchoice_1_11_3 span.description').height());
		$('.gchoice_1_11_0 span.description, .gchoice_1_11_1 span.description, .gchoice_1_11_2 span.description, .gchoice_1_11_3 span.description').height(drinksHeights);
		
		// non-alco drinks
		$('.non-alcoholic-drinks').before( $('.non-alco-drinks-title') );
		
		$('.gchoice_1_68_0').prepend( $('.non-alco-drinks-1-description') ).children('.non-alco-drinks-1-description').after('<hr>');
		$('.gchoice_1_68_0').prepend( $('.non-alco-drinks-1-price') ).children('.non-alco-drinks-1-price').after('<hr>');
		$('.gchoice_1_68_0').prepend( $('.non-alco-drinks-1-name') ).children('.non-alco-drinks-1-name').after('<hr>');
		
		$('.gchoice_1_68_1').prepend( $('.non-alco-drinks-2-description') ).children('.non-alco-drinks-2-description').after('<hr>');
		$('.gchoice_1_68_1').prepend( $('.non-alco-drinks-2-price') ).children('.non-alco-drinks-2-price').after('<hr>');
		$('.gchoice_1_68_1').prepend( $('.non-alco-drinks-2-name') ).children('.non-alco-drinks-2-name').after('<hr>');
		
		$('.gchoice_1_68_2').prepend( $('.non-alco-drinks-3-description') ).children('.non-alco-drinks-3-description').after('<hr>');
		$('.gchoice_1_68_2').prepend( $('.non-alco-drinks-3-price') ).children('.non-alco-drinks-3-price').after('<hr>');
		$('.gchoice_1_68_2').prepend( $('.non-alco-drinks-3-name') ).children('.non-alco-drinks-3-name').after('<hr>');
		
		var drinksHeights2 = Math.max($('.gchoice_1_68_0 span.description').height(), $('.gchoice_1_68_1 span.description').height(), $('.gchoice_1_68_2 span.description').height());
		$('.gchoice_1_68_0 span.description, .gchoice_1_68_1 span.description, .gchoice_1_68_2 span.description').height(drinksHeights2);

		// children soft drinks
		$('.gchoice_1_70_1').prepend( $('.chidren-soft-description') ).children('.chidren-soft-description').after('<hr>');
		$('.gchoice_1_70_1').prepend( $('.chidren-soft-price') ).children('.chidren-soft-price').after('<hr>');
		$('.gchoice_1_70_1').prepend( $('.chidren-soft-name') ).children('.chidren-soft-name').after('<hr>');


		// Extended bar
		$('.gchoice_1_12_1').prepend( $('.bar-description') ).children('.bar-description').after('<hr>');
		$('.gchoice_1_12_1').prepend( $('.bar-price') ).children('.bar-price').after('<hr>');
		$('.gchoice_1_12_1').prepend( $('.bar-name') ).children('.bar-name').after('<hr>');
		
		// Gin bar

		$('.gchoice_1_46_1').prepend( $('.ginbar-description') ).children('.ginbar-description').after('<hr>');
		$('.gchoice_1_46_1').prepend( $('.ginbar-price') ).children('.ginbar-price').after('<hr>');
		$('.gchoice_1_46_1').prepend( $('.ginbar-name') ).children('.ginbar-name').after('<hr>');
		
		// Gin Selection
		$('.ginselection').before( $('.ginselection-title') ).before( $('.ginselection-direction') );
		
		$('.gchoice_1_47_0').prepend( $('.ginselection-1-description') ).children('.ginselection-1-description').after('<hr>');
		$('.gchoice_1_47_0').prepend( $('.ginselection-1-price') ).children('.ginselection-1-price').after('<hr>');
		$('.gchoice_1_47_0').prepend( $('.ginselection-1-name') ).children('.ginselection-1-name').after('<hr>');
		
		$('.gchoice_1_47_1').prepend( $('.ginselection-2-description') ).children('.ginselection-2-description').after('<hr>');
		$('.gchoice_1_47_1').prepend( $('.ginselection-2-price') ).children('.ginselection-2-price').after('<hr>');
		$('.gchoice_1_47_1').prepend( $('.ginselection-2-name') ).children('.ginselection-2-name').after('<hr>');
		
		$('.gchoice_1_47_2').prepend( $('.ginselection-3-description') ).children('.ginselection-3-description').after('<hr>');
		$('.gchoice_1_47_2').prepend( $('.ginselection-3-price') ).children('.ginselection-3-price').after('<hr>');
		$('.gchoice_1_47_2').prepend( $('.ginselection-3-name') ).children('.ginselection-3-name').after('<hr>');

		$('.gchoice_1_47_3').prepend( $('.ginselection-4-description') ).children('.ginselection-4-description').after('<hr>');
		$('.gchoice_1_47_3').prepend( $('.ginselection-4-price') ).children('.ginselection-4-price').after('<hr>');
		$('.gchoice_1_47_3').prepend( $('.ginselection-4-name') ).children('.ginselection-4-name').after('<hr>');

		// Champagne Tower
		$('.champagne-tower').before( $('.champagnetower-title') );

		$('.gchoice_1_65_0').prepend( $('.champagnetower-1-price') ).children('.champagnetower-1-price').after('<hr>');
		$('.gchoice_1_65_0').prepend( $('.champagnetower-1-name') ).children('.champagnetower-1-name').after('<hr>');
		
		$('.gchoice_1_65_1').prepend( $('.champagnetower-2-price') ).children('.champagnetower-2-price').after('<hr>');
		$('.gchoice_1_65_1').prepend( $('.champagnetower-2-name') ).children('.champagnetower-2-name').after('<hr>');
		
		$('.gchoice_1_65_2').prepend( $('.champagnetower-3-price') ).children('.champagnetower-3-price').after('<hr>');
		$('.gchoice_1_65_2').prepend( $('.champagnetower-3-name') ).children('.champagnetower-3-name').after('<hr>');


		// After hours
	
		$('#gfield_description_1_49').prepend( $('.charcuterie-description') ).children('.charcuterie-description');
		$('#gfield_description_1_50').prepend( $('.cheeseboard-description') ).children('.cheeseboard-description');
	
		
		// evening (number of guests)

		$('.evening-guests').before( $('.evening-title') ).before( $('.evening-direction') );
		$('#field_1_13 label').text( $('.evening-label').text() );
		$('#field_1_38 label').text( $('.evening-label-2').text() );

		// dinner (evening meal)
		$('#field_1_14').prepend('<h3 class="title" style="color: #606060; margin-bottom: 20px;">SUBSTANTIAL EVENING MENUS</h3>');

		$('.dinner').before( $('.dinner-title') ).before( $('.dinner-direction') );
		$('.dinner-title').after( $('.evening-label-3') );

		// $('.gchoice_1_14_4').before( $('.dinner-title') );

		$('.gchoice_1_14_4').after( $('.dinner-s-direction') );

		$('.gchoice_1_14_5').before('<h3 class="title simple-dinner-title">SIMPLE EVENING FOOD</h3>');

		$('.gchoice_1_14_0').prepend( $('.dinner-1-description') ).children('.dinner-1-description').after('<hr>');
		$('.gchoice_1_14_0').prepend( $('.dinner-1-fancy') ).children('.dinner-1-fancy').after('<hr>');
		$('.gchoice_1_14_0').prepend( $('.dinner-1-price') ).children('.dinner-1-price');
		$('.gchoice_1_14_0').prepend( $('.dinner-1-name') ).children('.dinner-1-name').after('<hr>');

		$('.gchoice_1_14_1').prepend( $('.dinner-2-description') ).children('.dinner-2-description').after('<hr>');
		$('.gchoice_1_14_1').prepend( $('.dinner-2-fancy') ).children('.dinner-2-fancy').after('<hr>');
		$('.gchoice_1_14_1').prepend( $('.dinner-2-price') ).children('.dinner-2-price');
		$('.gchoice_1_14_1').prepend( $('.dinner-2-name') ).children('.dinner-2-name').after('<hr>');

		$('.gchoice_1_14_2').prepend( $('.dinner-3-description') ).children('.dinner-3-description').after('<hr>');
		$('.gchoice_1_14_2').prepend( $('.dinner-3-fancy') ).children('.dinner-3-fancy').after('<hr>');
		$('.gchoice_1_14_2').prepend( $('.dinner-3-price') ).children('.dinner-3-price');
		$('.gchoice_1_14_2').prepend( $('.dinner-3-name') ).children('.dinner-3-name').after('<hr>');

		$('.gchoice_1_14_3').prepend( $('.dinner-4-description') ).children('.dinner-4-description').after('<hr>');
		$('.gchoice_1_14_3').prepend( $('.dinner-4-fancy') ).children('.dinner-4-fancy').after('<hr>');
		$('.gchoice_1_14_3').prepend( $('.dinner-4-price') ).children('.dinner-4-price');
		$('.gchoice_1_14_3').prepend( $('.dinner-4-name') ).children('.dinner-4-name').after('<hr>');

		$('.gchoice_1_14_4').prepend( $('.dinner-5-description') ).children('.dinner-5-description').after('<hr>');
		$('.gchoice_1_14_4').prepend( $('.dinner-5-fancy') ).children('.dinner-5-fancy').after('<hr>');
		$('.gchoice_1_14_4').prepend( $('.dinner-5-price') ).children('.dinner-5-price');
		$('.gchoice_1_14_4').prepend( $('.dinner-5-name') ).children('.dinner-5-name').after('<hr>');

		$('.gchoice_1_14_5').prepend( $('.dinner-s-1-price') ).children('.dinner-s-1-price').after('<hr>');
		$('.gchoice_1_14_5').prepend( $('.dinner-s-1-description') ).children('.dinner-s-1-description');
		$('.gchoice_1_14_5').prepend( $('.dinner-s-1-name') ).children('.dinner-s-1-name').after('<hr>');

		$('.gchoice_1_14_6').prepend( $('.dinner-s-2-price') ).children('.dinner-s-2-price').after('<hr>');
		$('.gchoice_1_14_6').prepend( $('.dinner-s-2-description') ).children('.dinner-s-2-description');
		$('.gchoice_1_14_6').prepend( $('.dinner-s-2-name') ).children('.dinner-s-2-name').after('<hr>');

		$('.gchoice_1_14_7').prepend( $('.dinner-s-3-price') ).children('.dinner-s-3-price').after('<hr>');
		$('.gchoice_1_14_7').prepend( $('.dinner-s-3-description') ).children('.dinner-s-3-description');
		$('.gchoice_1_14_7').prepend( $('.dinner-s-3-name') ).children('.dinner-s-3-name').after('<hr>');

		$('.gchoice_1_14_8').prepend( $('.dinner-s-4-price') ).children('.dinner-s-4-price').after('<hr>');
		$('.gchoice_1_14_8').prepend( $('.dinner-s-4-description') ).children('.dinner-s-4-description');
		$('.gchoice_1_14_8').prepend( $('.dinner-s-4-name') ).children('.dinner-s-4-name').after('<hr>');

		$('.gchoice_1_14_9').prepend( $('.dinner-s-5-price') ).children('.dinner-s-5-price').after('<hr>');
		$('.gchoice_1_14_9').prepend( $('.dinner-s-5-description') ).children('.dinner-s-5-description');
		$('.gchoice_1_14_9').prepend( $('.dinner-s-5-name') ).children('.dinner-s-5-name').after('<hr>');

		$('.gchoice_1_14_10').prepend( $('.dinner-s-6-price') ).children('.dinner-s-6-price').after('<hr>');
		$('.gchoice_1_14_10').prepend( $('.dinner-s-6-description') ).children('.dinner-s-6-description');
		$('.gchoice_1_14_10').prepend( $('.dinner-s-6-name') ).children('.dinner-s-6-name').after('<hr>');

		$('.gchoice_1_14_11').prepend( $('.dinner-s-7-price') ).children('.dinner-s-7-price').after('<hr>');
		$('.gchoice_1_14_11').prepend( $('.dinner-s-7-description') ).children('.dinner-s-7-description');
		$('.gchoice_1_14_11').prepend( $('.dinner-s-7-name') ).children('.dinner-s-7-name').after('<hr>');


		var breakfastHeights = Math.max($('.gchoice_1_14_0 span.description').height(), $('.gchoice_1_14_1 span.description').height(), $('.gchoice_1_14_2 span.description').height(), $('.gchoice_1_14_3 span.description').height());
		$('.gchoice_1_14_0 span.description, .gchoice_1_14_1 span.description, .gchoice_1_14_2 span.description, .gchoice_1_14_3 span.description').height(breakfastHeights);

		// dinner simple (evening meal)

		

		

		// $('.gchoice_1_73_0').prepend( $('.dinner-s-1-price') ).children('.dinner-s-1-price').after('<hr>');
		// $('.gchoice_1_73_0').prepend( $('.dinner-s-1-description') ).children('.dinner-s-1-description');
		// $('.gchoice_1_73_0').prepend( $('.dinner-s-1-name') ).children('.dinner-s-1-name').after('<hr>');

		// $('.gchoice_1_73_1').prepend( $('.dinner-s-2-price') ).children('.dinner-s-2-price').after('<hr>');
		// $('.gchoice_1_73_1').prepend( $('.dinner-s-2-description') ).children('.dinner-s-2-description');
		// $('.gchoice_1_73_1').prepend( $('.dinner-s-2-name') ).children('.dinner-s-2-name').after('<hr>');

		// $('.gchoice_1_73_2').prepend( $('.dinner-s-3-price') ).children('.dinner-s-3-price').after('<hr>');
		// $('.gchoice_1_73_2').prepend( $('.dinner-s-3-description') ).children('.dinner-s-3-description');
		// $('.gchoice_1_73_2').prepend( $('.dinner-s-3-name') ).children('.dinner-s-3-name').after('<hr>');

		// $('.gchoice_1_73_3').prepend( $('.dinner-s-4-price') ).children('.dinner-s-4-price').after('<hr>');
		// $('.gchoice_1_73_3').prepend( $('.dinner-s-4-description') ).children('.dinner-s-4-description');
		// $('.gchoice_1_73_3').prepend( $('.dinner-s-4-name') ).children('.dinner-s-4-name').after('<hr>');

		// $('.gchoice_1_73_4').prepend( $('.dinner-s-5-price') ).children('.dinner-s-5-price').after('<hr>');
		// $('.gchoice_1_73_4').prepend( $('.dinner-s-5-description') ).children('.dinner-s-5-description');
		// $('.gchoice_1_73_4').prepend( $('.dinner-s-5-name') ).children('.dinner-s-5-name').after('<hr>');

		// $('.gchoice_1_73_5').prepend( $('.dinner-s-6-price') ).children('.dinner-s-6-price').after('<hr>');
		// $('.gchoice_1_73_5').prepend( $('.dinner-s-6-description') ).children('.dinner-s-6-description');
		// $('.gchoice_1_73_5').prepend( $('.dinner-s-6-name') ).children('.dinner-s-6-name').after('<hr>');

		// $('.gchoice_1_73_6').prepend( $('.dinner-s-7-price') ).children('.dinner-s-7-price').after('<hr>');
		// $('.gchoice_1_73_6').prepend( $('.dinner-s-7-description') ).children('.dinner-s-7-description');
		// $('.gchoice_1_73_6').prepend( $('.dinner-s-7-name') ).children('.dinner-s-7-name').after('<hr>');

		var dinnerSHeights = Math.max($('#field_1_16 p.description').height(), $('#field_1_17 p.description').height(), $('#field_1_18 p.description').height(), $('#field_1_19 p.description').height(), $('#field_1_20 p.description').height(), $('#field_1_21 p.description').height(), $('#field_1_22 p.description').height(), $('#field_1_23 p.description').height(), $('#field_1_24 p.description').height(), $('#field_1_25 p.description').height() );
		$('#field_1_16 p.description, .field_1_17 p.description, #field_1_18 p.description, #field_1_19 p.description, #field_1_20 p.description, #field_1_21 p.description, #field_1_22 p.description, #field_1_23 p.description, #field_1_24 p.description, #field_1_25 p.description').height(dinnerSHeights);

		// something extra
		$('#field_1_75').prepend( $('.something-extra-title') );		

		$('.gchoice_1_75_1').prepend( $('.something-extra-1-price') ).children('.something-extra-1-price').after('<hr>');
		$('.gchoice_1_75_1').prepend( $('.something-extra-1-description') ).children('.something-extra-1-description');
		$('.gchoice_1_75_1').prepend( $('.something-extra-1-name') ).children('.something-extra-1-name').after('<hr>');

		$('.gchoice_1_75_2').prepend( $('.something-extra-2-price') ).children('.something-extra-2-price').after('<hr>');
		$('.gchoice_1_75_2').prepend( $('.something-extra-2-description') ).children('.something-extra-2-description');
		$('.gchoice_1_75_2').prepend( $('.something-extra-2-name') ).children('.something-extra-2-name').after('<hr>');

		$('.gchoice_1_75_3').prepend( $('.something-extra-3-price') ).children('.something-extra-3-price').after('<hr>');
		$('.gchoice_1_75_3').prepend( $('.something-extra-3-description') ).children('.something-extra-3-description');
		$('.gchoice_1_75_3').prepend( $('.something-extra-3-name') ).children('.something-extra-3-name').after('<hr>');


		// bedrooms

		$('.bedrooms-main').before( $('.bedrooms-title') ).before( $('.bedrooms-information') ).before( $('.bedrooms-direction') );

		$('.gchoice_1_27_1').prepend( $('.bedrooms-1-description') ).children('.bedrooms-1-description').after('<hr>');
		$('.gchoice_1_27_1').prepend( $('.bedrooms-1-price') ).children('.bedrooms-1-price').after('<hr>');
		$('.gchoice_1_27_1').prepend( $('.bedrooms-1-name') ).children('.bedrooms-1-name').after('<hr>');

		$('.gchoice_1_27_2').prepend( $('.bedrooms-2-description') ).children('.bedrooms-2-description').after('<hr>');
		$('.gchoice_1_27_2').prepend( $('.bedrooms-2-price') ).children('.bedrooms-2-price').after('<hr>');
		$('.gchoice_1_27_2').prepend( $('.bedrooms-2-name') ).children('.bedrooms-2-name').after('<hr>');

		$('.gchoice_1_27_3').prepend( $('.bedrooms-3-description') ).children('.bedrooms-3-description').after('<hr>');
		$('.gchoice_1_27_3').prepend( $('.bedrooms-3-price') ).children('.bedrooms-3-price').after('<hr>');
		$('.gchoice_1_27_3').prepend( $('.bedrooms-3-name') ).children('.bedrooms-3-name').after('<hr>');

		$('.gchoice_1_27_4').prepend( $('.bedrooms-4-description') ).children('.bedrooms-4-description').after('<hr>');
		$('.gchoice_1_27_4').prepend( $('.bedrooms-4-price') ).children('.bedrooms-4-price').after('<hr>');
		$('.gchoice_1_27_4').prepend( $('.bedrooms-4-name') ).children('.bedrooms-4-name').after('<hr>');
		
		$('.gchoice_1_27_5').prepend( $('.bedrooms-5-description') ).children('.bedrooms-5-description').after('<hr>');
		$('.gchoice_1_27_5').prepend( $('.bedrooms-5-price') ).children('.bedrooms-5-price').after('<hr>');
		$('.gchoice_1_27_5').prepend( $('.bedrooms-5-name') ).children('.bedrooms-5-name').after('<hr>');

		$('.gchoice_1_27_6').prepend( $('.bedrooms-6-description') ).children('.bedrooms-6-description').after('<hr>');
		$('.gchoice_1_27_6').prepend( $('.bedrooms-6-price') ).children('.bedrooms-6-price').after('<hr>');
		$('.gchoice_1_27_6').prepend( $('.bedrooms-6-name') ).children('.bedrooms-6-name').after('<hr>');

		$('.gchoice_1_27_7').prepend( $('.bedrooms-7-description') ).children('.bedrooms-7-description').after('<hr>');
		$('.gchoice_1_27_7').prepend( $('.bedrooms-7-price') ).children('.bedrooms-7-price').after('<hr>');
		$('.gchoice_1_27_7').prepend( $('.bedrooms-7-name') ).children('.bedrooms-7-name').after('<hr>');

		$('.gchoice_1_27_8').prepend( $('.bedrooms-8-description') ).children('.bedrooms-8-description').after('<hr>');
		$('.gchoice_1_27_8').prepend( $('.bedrooms-8-price') ).children('.bedrooms-8-price').after('<hr>');
		$('.gchoice_1_27_8').prepend( $('.bedrooms-8-name') ).children('.bedrooms-8-name').after('<hr>');

		$('.gchoice_1_27_9').prepend( $('.bedrooms-9-description') ).children('.bedrooms-9-description').after('<hr>');
		$('.gchoice_1_27_9').prepend( $('.bedrooms-9-price') ).children('.bedrooms-9-price').after('<hr>');
		$('.gchoice_1_27_9').prepend( $('.bedrooms-9-name') ).children('.bedrooms-9-name').after('<hr>');

		$('.gchoice_1_27_11').prepend( $('.bedrooms-10-description') ).children('.bedrooms-10-description').after('<hr>');
		$('.gchoice_1_27_11').prepend( $('.bedrooms-10-price') ).children('.bedrooms-10-price').after('<hr>');
		$('.gchoice_1_27_11').prepend( $('.bedrooms-10-name') ).children('.bedrooms-10-name').after('<hr>');

		$('.gchoice_1_27_12').prepend( $('.bedrooms-11-description') ).children('.bedrooms-11-description').after('<hr>');
		$('.gchoice_1_27_12').prepend( $('.bedrooms-11-price') ).children('.bedrooms-11-price').after('<hr>');
		$('.gchoice_1_27_12').prepend( $('.bedrooms-11-name') ).children('.bedrooms-11-name').after('<hr>');

		$('.gchoice_1_27_13').prepend( $('.bedrooms-12-description') ).children('.bedrooms-12-description').after('<hr>');
		$('.gchoice_1_27_13').prepend( $('.bedrooms-12-price') ).children('.bedrooms-12-price').after('<hr>');
		$('.gchoice_1_27_13').prepend( $('.bedrooms-12-name') ).children('.bedrooms-12-name').after('<hr>');

		var bedroomsHeights = Math.max($('.gchoice_1_27_1 h4').height(), $('.gchoice_1_27_2 h4').height(), $('.gchoice_1_27_3 h4').height(), $('.gchoice_1_27_4 h4').height(), $('.gchoice_1_27_5 h4').height(), $('.gchoice_1_27_6 h4').height(), $('.gchoice_1_27_7 h4').height(), $('.gchoice_1_27_8 h4').height(), $('.gchoice_1_27_9 h4').height(), $('.gchoice_1_27_11 h4').height(), $('.gchoice_1_27_12 h4').height(), $('#field_1_28 h4').height(), $('#field_1_29 h4').height() );
		$('.gchoice_1_27_1 h4, .gchoice_1_27_2 h4, .gchoice_1_27_3 h4, .gchoice_1_27_4 h4, .gchoice_1_27_5 h4, .gchoice_1_27_6 h4, .gchoice_1_27_7 h4, .gchoice_1_27_8 h4, .gchoice_1_27_9 h4, .gchoice_1_27_11 h4, .gchoice_1_27_12 h4, #field_1_28 h4, #field_1_29 h4').height(bedroomsHeights);

		// personal

		$('.personal').before( $('.personal-title') ).before( $('.personal-direction') );
		
		var radioChecked;
		$('input[type=radio] + label').bind('mousedown', function() {
			radioChecked = $(this).attr('checked');
		});
	}
	$(planner);

	// Set min/max calandar dates

	if ( $('body').hasClass('page-id-17') ) {
	    var min_year = (new Date).getFullYear();
	    var min_month = (new Date).getMonth();

	    var max_year = parseInt( $('.calandar_limit .year').text() );
	    var max_month = parseInt( $('.calandar_limit .month').text() );
	    var max_day = parseInt( $('.calandar_limit .day').text() );

	    $( "#input_1_1, #input_1_2" ).datepicker({
	        minDate: new Date(min_year, min_month, 1),
	        maxDate: new Date(max_year, max_month-1, max_day),
	        dateFormat: 'dd/mm/yy'
	    });
	}

	// disable unavailable datepicker dates

	function calandarUnavail() {
		$( 'td' ).each(function() {
			var cellDay = $(this).children('a').text();
			if (parseFloat(cellDay)<10) {
				var cellDay = '0' + $(this).children('a').text();
			}
			var cellMonth = parseFloat( $(this).attr('data-month') ) + 1;
			if (parseFloat(cellMonth)<10) {
				var cellMonth = '0' + (parseFloat( $(this).attr('data-month') ) + 1);
			}
			var cellYear = $(this).attr('data-year');
			$(this).children('a').addClass( 'cell-date-' + cellYear + cellMonth + cellDay );
		});
		$( 'p.unavailable-date' ).each(function() {
			var unavailDate = $(this).text();
			var buildClass = '.cell-date-' + unavailDate;
			$(buildClass).css({
				'background':'url(http://www.prestwold-hall.com/wp-content/themes/prestwold/img/datepicker-unavail.png) center center no-repeat #fff',
				'color':'#666',
				'cursor':'default',
				'border':0,
				'margin':0,
				'-moz-box-shadow':'none',
				'-webkit-box-shadow':'none',
				'box-shadow':'none'
			});
		});
	}

	$('#input_1_1, #input_1_2').click(function(){
		calandarUnavail();
	});

	$('body').livequery(
				'.ui-datepicker-calendar', // the selector to match against
				function(elem) {
					console.log("Livequery");
					calandarUnavail();
				}
			);

	// dynamically change evening guests number

	$('#input_1_9').keyup(function(){
		var daytimeGuests = parseFloat( $(this).val() );
		$('#input_1_13').val(daytimeGuests);
	})

	// dynamically change evening meal prices
	$('#input_1_38, #input_1_13').keyup(function(){

		var daytimeGuests = parseFloat( $('#input_1_13').val() );
		var additionalGuests = parseFloat( $('#input_1_38').val() );

		var guestsTotal = (daytimeGuests/2) + additionalGuests;

		var dinner1 = $('.gchoice_1_14_0 p span.price').text() * guestsTotal;
		var dinner2 = $('.gchoice_1_14_1 p span.price').text() * guestsTotal;
		var dinner3 = $('.gchoice_1_14_2 p span.price').text() * guestsTotal;
		var dinner4 = $('.gchoice_1_14_3 p span.price').text() * guestsTotal;

		var numberRegex = /^[+-]?\d+(\.\d+)?([eE][+-]?\d+)?$/;

		if ( numberRegex.test(daytimeGuests) && numberRegex.test(additionalGuests) ) {
			$('.gchoice_1_14_0 p span.calculated-price').html( dinner1.toFixed(2) );
			$('.gchoice_1_14_1 p span.calculated-price').html( dinner2.toFixed(2) );
			$('.gchoice_1_14_2 p span.calculated-price').html( dinner3.toFixed(2) );
			$('.gchoice_1_14_3 p span.calculated-price').html( dinner4.toFixed(2) );
		} else {
			$('.gchoice_1_14_0 p span.calculated-price').html( '&mdash;' );
			$('.gchoice_1_14_1 p span.calculated-price').html( '&mdash;' );
			$('.gchoice_1_14_2 p span.calculated-price').html( '&mdash;' );
			$('.gchoice_1_14_3 p span.calculated-price').html( '&mdash;' );
		}
	});

	//breakfast options self-excluding
	
	    // Select all the inputs from the three different sections.
	    var $breackfastOptions = $('#field_1_10 input[type="radio"], #field_1_66 input[type="radio"], #field_1_67 input[type="checkbox"]');

	    // Add a change event listener to all of them.
	    $breackfastOptions.on('change', function() {
	        // If the changed element is checked...
	        if ($(this).is(':checked')) {
	            // Uncheck all other options.
	            $breackfastOptions.not(this).prop('checked', false);
	        }
	    });

	// radio uncheck
	    $(function() {
		  let lastChecked = null;

		  $("input[name='input_7'], input[name='input_64'], input[name='input_68'], input[name='input_47'], input[name='input_65']").on("click", function(e) {
		    const $this = $(this);

		    if ($this[0] === lastChecked) {
		      $this.prop("checked", false);
		      lastChecked = null;
		    } else {
		      lastChecked = $this[0];
		    }
		  });
		});
	

	// Email customer

	$('.planner_email').click(function(){
		$.ajax({
			url: '/wp-admin/admin-ajax.php',
			data: {
				'action' : 'planner_email',
				'entry_id' : $("#entry_id").html()
			},
			success:function(data) {
				// This outputs the result of the ajax request
				$('.planner_email').attr('value', 'Email sent');
				console.log(data);
			},
            error: function(data) {
				$('.planner_email').attr('value', 'There was a problem');
				console.log(data);
            }
		});
	});

 	// fixing nth-child selectivize nonsense

	if (jQuery.support.leadingWhitespace == false){

	 	$('.gfield_radio input, .gfield_checkbox input').css({
	 		"clip":"auto",
	 		"height":"auto",
	 		"margin":"0 auto",
	 		"position":"static",
	 		"width":"auto"
	 	});

	 	$('.gfield_radio input, .gfield_checkbox input').parent().css({'text-align':'center'});

	 	$('.gfield_radio label, .gfield_checkbox label').css({
	 		"background":"none",
	 		"border":"0",
	 		"color":"#666",
	 		"margin":"0 auto 20px"
	 	});
	 	$('#label_8_0, #label_8_1, #label_8_2').css({'color':'#fff'});
	 	$('#choice_29_0').css({'display':'block', 'margin':'0 22px 0 auto'});
	 	$('#choice_29_1').css({'display':'block', 'margin':'0 auto 0 22px'});

	 	$('.gchoice_1_8_2').css({'margin':'20px 0'});
	 	$('.gchoice_1_7_2').css({'margin':'0 0 20px'});
	 	$('.gchoice_1_10_3').css({'margin':'0'});
	 	$('.gchoice_1_11_3').css({'margin':'0'});
	 	$('.gchoice_1_14_3').css({'margin':'0'});
	 	$('#field_1_18, #field_1_21, #field_1_24').css({'margin':'0 0 20px'});
	 	$('.gchoice_1_27_4').css({'margin':'0'});
	 	$( "#field_1_29" ).after( "<div style=clear:both;></div>" );

	 	$('section.content .gallery-item:nth-of-type(3n)').css({'margin':'0 0 2%'});

	 	$('ul.personal').hide();

	 	$('#input_1_30').css({'border':'1px solid #ccc'});
	 	$('#input_1_32').css({'border':'1px solid #ccc'});
	 	$('#input_1_31').css({'border':'1px solid #ccc'});

	}

});


/* KM Custom popup
==================*/
function km_wp_modals(){
	const parent = document.getElementById('km-wp-popups');

	if(!parent) return;
	const closeBtns = Array.from(parent.getElementsByClassName('km-popup-close'));
	const modals = Array.from(parent.getElementsByClassName('km-wp-popup'));
	const triggers = Array.from(document.querySelectorAll('a[data-fancybox-group]'));

	triggers.forEach(trigger => {
		trigger.addEventListener('click', revealModal, false);
	});


	closeBtns.forEach(closeBtn => {
		closeBtn.addEventListener('click',() => {
			triggers.forEach(trigger => {
				trigger.classList.remove('active');
			});

			modals.forEach(trigger => {
				trigger.classList.remove('active');
			});

     parent.classList.remove('active');
		 document.documentElement.classList.remove('wedding-planner-popup-active');
		},false);
	});


 function revealModal(e){
   e.preventDefault();
	 document.documentElement.classList.add('wedding-planner-popup-active');
	 parent.classList.toggle('active');
	 const btn = e.currentTarget;
	 const target = btn.href.split('#')[1];
	 const modal = document.getElementById(target);
	 btn.classList.toggle('active');
	 modal.classList.toggle('active');
 }
}
window.addEventListener('DOMContentLoaded', (event) => {
	km_wp_modals();
});
