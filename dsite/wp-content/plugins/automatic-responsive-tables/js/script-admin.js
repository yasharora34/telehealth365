jQuery(document).ready(function($) {

	//---- Because I love feedback
	if( $('#rockwellgrowth-loves-feedback').length > 0 ) {
		$('#rockwellgrowth-loves-feedback input[type="submit"]').click(function(event) {
			event.preventDefault();

		    url = "http://plugins.rockwellgrowth.com/feedback/grabber.php?regarding=responsive-tables&feedback_text=" + $('#rockwellgrowth-loves-feedback textarea').val();
		    leftPosition = (window.screen.width / 2) - 135;
		    topPosition = (window.screen.height / 2) - 87;

		    window.open(url, "Window2", "status=no,height=75,width=250,resizable=no,left=" + leftPosition + ",top=" + topPosition + ",screenX=" + leftPosition + ",screenY=" + topPosition + ",toolbar=no,menubar=no,scrollbars=no,location=no,directories=no");
		});
	}

	//---- Hide / show the classes field based on the state of the checkbox
	if( $('input[name="responsive_tables_opt[activate_all]"]').is(':checked') ) {
		$('.settings_page_responsive-tables form table.form-table tr:nth-child(2)').css('display', 'none');
	}
	//---- Hide / show the classes field when the checkbox is clicked
	$('input[name="responsive_tables_opt[activate_all]"]').click(function () {
		if( $(this).is(':checked') ) {
			$('.settings_page_responsive-tables form table.form-table tr:nth-child(2)').fadeOut(350);
		} else {
			$('.settings_page_responsive-tables form table.form-table tr:nth-child(2)').fadeIn(350);
		}
	});


	//---- Hide / show the style fields based on the state of the checkbox
	if( !$('input[name="responsive_tables_opt[default_styling]"]').is(':checked') ) {
		$('input[name="responsive_tables_opt[default_styling]"]').parent().parent().nextAll().css('display', 'none');
		$('.art-table').css('display','none');
	}
	//---- Hide / show the style fields when the checkbox is clicked
	$('input[name="responsive_tables_opt[default_styling]"]').click(function () {
		if( $(this).is(':checked') ) {
			$('input[name="responsive_tables_opt[default_styling]"]').parent().parent().nextAll().fadeIn(350);
			$('.art-table').fadeIn(350);
		} else {
			$('input[name="responsive_tables_opt[default_styling]"]').parent().parent().nextAll().fadeOut(350);
			$('.art-table').fadeOut(350);
		}
	});


	//---- Style Editor
    $( '.cpa-color-picker' ).wpColorPicker();
    optionArray = {};
    optionArray.table_border_color = ['.art-table, .art-table .art-tbody .art-tr:not(:last-child), .art-table .art-tbody .art-tr .art-td .art-td-last','border-color'];
    optionArray.cell_border_color = ['.art-table .art-tbody .art-tr .art-td:not(:last-child)','border-color'];
    optionArray.odd_row_color = ['.art-table .art-tbody .art-tr:nth-child(2n+1)','background'];
    optionArray.even_row_color = ['.art-table .art-tbody .art-tr:nth-child(2n)','background'];
    $('.wp-picker-container').on('mousemove', function () {
    	colorOptions = optionArray[$('.wp-color-picker', this).attr('id')];
    	console.log(colorOptions);
    	$(colorOptions[0]).css(colorOptions[1], $('.wp-color-picker', this).val());
    });

    $('#header_font').on('change mouseup keyup', function () {
    	var fontSize = $(this).val();
    	if( $(this).val() == '') {
    		fontSize = 16;
    	}
    	$('.art-table .art-tbody .art-tr .art-td .art-td-first').css('font-size', fontSize+'px');
    });

    $('#value_font').on('change mouseup keyup', function () {
    	var fontSize = $(this).val();
    	if( fontSize == '' ) {
    		fontSize = 16;
    	}
    	$('.art-table .art-tbody .art-tr .art-td .art-td-last').css('font-size', fontSize+'px');
    });


    $('#header_value_divider').click(function () {
    	if( $(this).is(':checked') ) {
    		$('.art-table .art-tbody .art-tr .art-td .art-td-last').css('border-width', '1px');
    	} else {
    		$('.art-table .art-tbody .art-tr .art-td .art-td-last').css('border-width', '0');
    	}
    })

});