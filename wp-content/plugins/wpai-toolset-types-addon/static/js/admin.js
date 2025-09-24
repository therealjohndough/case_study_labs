/**
 * plugin admin area javascript
 */
(function($){$(function () {
	
	if ( ! $('body.wpallimport-plugin').length) return; // do not execute any code if we are not on plugin page

	var pmti_get_toolset_group = function(ths){

		var request = {
			action:'get_toolset',
			security: wp_all_import_security,
			toolset: ths.attr('rel')
	    };

	    if (typeof import_id != "undefined") request.id = import_id;

        var $ths = ths.parents('.pmti_options:first');

	    $ths.find('.wpcs_groups').prepend('<div class="pmti_preloader"></div>');

	    $('.pmti_toolset_group').attr('disabled', 'disabled');

		$.ajax({
			type: 'GET',
			url: ajaxurl,
			data: request,
			success: function(response) {
				$('.pmti_toolset_group').removeAttr('disabled');
				$ths.find('.pmti_preloader').remove();
				$ths.find('.wpcs_groups').prepend(response.html);
				pmti_init($ths.find('.wpcs_signle_group:first'));
				// swither show/hide logic
				$ths.find('.wpcs_groups').find('input.switcher').change();
			},
			error: function(jqXHR, textStatus){
				$('.pmti_toolset_group').removeAttr('disabled');
				$ths.find('.pmti_preloader').remove();
				alert('Something went wrong. ' + textStatus );
			},
			dataType: "json"
		});
	}

	var pmti_reset_wpcs_groups = function(){
		
		$('.pmti_options').find('.wpcs_signle_group').remove();

		$('.pmti_options:visible').find('.pmti_toolset_group:checked').each(function(){
			pmti_get_toolset_group($(this));
		});
	}

	pmti_reset_wpcs_groups();

	$('.pmxi_plugin').find('.nav-tab').click(function(){
		pmti_reset_wpcs_groups();
	});

	$('.pmti_toolset_group').on('change', function(){
		let wpcs = $(this).attr('rel');
		if ($(this).is(':checked')) {
			// if requested Toolset group doesn't exists
			if ( ! $(this).parents('.pmti_options:first').find('.wpcs_signle_group[rel=' + wpcs + ']').length){
				pmti_get_toolset_group($(this));
			}	
		} else {
			if (confirm("Confirm removal?")) {
				$(this).parents('.pmti_options:first').find('.wpcs_signle_group[rel=' + wpcs + ']').remove();
			} else {
				$(this).attr('checked','checked');
			}
		}
	});

    function pmti_init(ths){

		ths.find('input.datetimepicker').datetimepicker({
			dateFormat: 'd M yy',
			timeFormat: 'hh:mm TT',
			ampm: true
		});

		ths.find('input.datepicker').datepicker({
			dateFormat: 'd M yy',
			ampm: true
		});
		
		ths.find('.sortable').each(function(){
			if ( ! $(this).parents('tr.row-clone').length ){
				$(this).pmxi_nestedSortable({
			        handle: 'div',
			        items: 'li.dragging',
			        toleranceElement: '> div',
			        update: function () {	        
				       $(this).parents('td:first').find('.hierarhy-output').val(window.JSON.stringify($(this).pmxi_nestedSortable('toArray', {startDepthCount: 0})));
				       if ($(this).parents('td:first').find('input:first').val() == '') $(this).parents('td:first').find('.hierarhy-output').val('');
				    }
			    });		    
			} 
		});

		ths.find('.wpcs-repeater').find('.add-row-end').on('click', function(){
			let $parent = $(this).parents('.wpcs-repeater:first');
			pmti_repeater_clone($parent);
		});

		ths.find('input.switcher').on("change", function (e) {
			if ($(this).is(':radio:checked')) {
				$(this).parents('form').find('input.switcher:radio[name="' + $(this).attr('name') + '"]').not(this).change();
			}
			let $targets = $('.switcher-target-' + $(this).attr('id'));
			let is_show = $(this).is(':checked'); if ($(this).is('.switcher-reversed')) is_show = ! is_show;
			if (is_show) {
				$targets.slideDown();
			} else {
				$targets.slideUp().find('.clear-on-switch').add($targets.filter('.clear-on-switch')).val('');
			}
		}).change();
	}

	$(document).on('change', '.wpcs-variable_repeater_mode', function() {
		// if variable mode
		if ($(this).is(':checked') && ($(this).val() == 'yes' || $(this).val() == 'csv')){
			var $parent = $(this).parents('.wpcs-repeater:first');
			$parent.find('tbody:first').children('.row:not(:first)').remove();
			if ( ! $parent.find('tbody:first').children('.row').length) pmti_repeater_clone($parent);
		}
	});

	let pmti_repeater_clone = function($parent){

		let $clone = $parent.find('tbody:first').children('.row-clone:first').clone();
		let $number = parseInt($parent.find('tbody:first').children().length);

		$clone.removeClass('row-clone').addClass('row').find('td.order').html($number);
		$clone.find('.switcher').each(function(){
			$(this).attr({'id':$(this).attr('id').replace('ROWNUMBER', $number)});
		});
		$clone.find('.chooser_label').each(function(){
			$(this).attr({'for':$(this).attr('for').replace('ROWNUMBER', $number)});
		});
		$clone.find('div[class^=switcher-target]').each(function(){
			$(this).attr({'class':$(this).attr('class').replace('ROWNUMBER', $number)});
		});
		$clone.find('input, select, textarea').each(function(){
			let name = $(this).attr('name');
			if (name != undefined) $(this).attr({'name':$(this).attr('name').replace('ROWNUMBER', $number)});
		});

		$parent.find('.wpcs-input-table:first').find('tbody:first').append($clone);

		$parent.find('tr.row').find('.sortable').each(function(){
			if ( ! $(this).hasClass('ui-sortable') && ! $(this).parents('tr.row-clone').length ){
				$(this).pmxi_nestedSortable({
					handle: 'div',
					items: 'li.dragging',
					toleranceElement: '> div',
					update: function () {
						$(this).parents('td:first').find('.hierarhy-output').val(window.JSON.stringify($(this).pmxi_nestedSortable('toArray', {startDepthCount: 0})));
						if ($(this).parents('td:first').find('input:first').val() == '') $(this).parents('td:first').find('.hierarhy-output').val('');
					}
				});
			}
		});

		pmti_init($parent);
	};

	$(document).on('click', '.delete_row', function(){
		let $parent = $(this).parents('.wpcs-repeater:first');
		$parent.find('tbody:first').children('.row:last').remove();
	});

});})(jQuery);
