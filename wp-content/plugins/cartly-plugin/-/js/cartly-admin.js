(function($) {

$(document).ready(function(){
	
	// Add Product Option
	$(document).on('click', '.add-option', function(event) {
		event.preventDefault();
		$('.option-label-row').show();
		$row = $(this).parents('tr').removeClass('add-option-row');
		$row.after('<tr class="add-option-row"><td class="blank"></td><td colspan="5">'+$(this).parents('td').detach().html()+'</td></tr>');
		BuildOptionRow($row, '', '', '', '', $('.option-row').length + 1);
	});
	
	// Delete Product Option
	$(document).on('click', '.delete-option', function(event) {
		event.preventDefault();
		$row = $(this).parents('tr');
		
		if ($('.option-row').length <= 1) {
			$('.add-option-row td:first').append($('.options-label').detach());	
			$('.option-label-row').remove();
		} else if ($row.index('.option-row') == 0) {
			$('.option-row:eq(1) td:first').append($('.options-label').detach());
		}
		
		if ($('tr[data-option-id]').length == 1) {
			$labelRow = $('.option-label:first').parents('tr');
			$labelRow.fadeTo(600, 0, function() {
				$labelRow.slideUp(function() {
					$labelRow.remove();
				});
			});
		}
		
		$row.fadeTo(600, 0, function() {
			$row.slideUp(function() {
				if ($row.data('optionId') != undefined) {
					$row.remove();
					return;
				} else {
					$row.remove();	
				}				
			});
		});
		
		$.post(cartlyAjaxUrl, {
    		action : 'delete-product-option',
    		post_id : $row.parents('table').data('postId'),
    		option_id : $row.data('optionId')
    	})
    	.done(function(data) {
    		var response = $.parseJSON(data);
    		if (response.status == 'SUCCESS') {
    		}
    		else {
    		}
    	})
    	.fail(function() {
    		// Handle Error
    	});
		
	});
	
	// Order Change
	$(document).on('click', '.change-order', function(event) {
		event.preventDefault();
		$controls = $('.order-controls.template').clone().removeClass('template');
		$('select[name=status]', $controls).val($(this).parents('tr').data('statusId'));
		$(this).parent('div').hide().after($controls.show());
	});
	
	// Order Actions
	$(document).on('click', '.order-controls a', function(event) {
		event.preventDefault();
		$anchor = $(this);
		$oldStatusId = $anchor.parents('tr').data('statusId');
		$newStatusId = $anchor.siblings('select[name=status]').val();
		$oldStatusValue = parseInt($('.status-table td[data-status-id="'+$oldStatusId+'"]').html()) - 1;
		$newStatusValue = parseInt($('.status-table td[data-status-id="'+$newStatusId+'"]').html()) + 1;
		$anchor.parents('tr').data('statusId', $newStatusId);
		if ($(this).is('.save')) {
			$.post(cartlyAjaxUrl, {
	    		action : 'update-order-status',
	    		order_id : $anchor.parents('tr').data('id'),
	    		status_id : $newStatusId
	    	})
	    	.done(function(data) {
	    		var response = $.parseJSON(data);
	    		if (response.status == 'SUCCESS') {	    			
					$('.status-table td[data-status-id="'+$oldStatusId+'"]').html($oldStatusValue);
					$('.status-table td[data-status-id="'+$newStatusId+'"]').html($newStatusValue);
	    		}
	    		else {
	    		}
	    		$controls = $anchor.parents('.order-controls');
	    		$statusMeta = $anchor.parents('tr').find('.status-meta');
	    		$('span', $statusMeta).html($('select[name=status] option:selected', $controls).text());
	    		$controls.hide().remove();
	    		$statusMeta.show();
	    	})
	    	.fail(function() {
	    		// Handle Error
	    	});
		} else if ($(this).is('.cancel')) {
			$(this).parents('.order-controls').siblings('.status-meta').show();
			$(this).parents('.order-controls').remove();
		}
	});
	
	// Get Product Option Set
	$(document).on('click', '.get-product-option-set', function(event) {
		event.preventDefault();
		$('.option-label-row').show();
		$setId = $(this).data('optionSetId');
		$.post(cartlyAjaxUrl, {
    		action : 'get-product-option-set',
    		option_set_id : $setId
    	})
    	.done(function(data) {
    		var response = $.parseJSON(data);
    		if (response.status == 'SUCCESS') {
    			$(response.extra.set).each(function() {
    				$newRow = $('<tr class="option-row"><td class="blank"></td></tr>');
    				BuildOptionRow($newRow, this.name, this.price, this.shipping, this.quantity, $('.option-row').length + 1);
    				if ($('.option-row').length) {
	    				$('.option-row:last').after($newRow);
    				} else {
	    				$('.option-label-row').after($newRow);	
    				}    				
    			});
    			$('.option-row:first td:first').append($('.options-label').detach());
    		}
    		else {
    		}
    	})
    	.fail(function() {
    		// Handle Error
    	});
	});
	
	// Add Option Set
	$(document).on('submit', '.option-set-form', function(event) {
		event.preventDefault();
		$('.add-option-set').click();
	});
	
	$(document).on('click', '.add-option-set', function(event) {
		event.preventDefault();
		$setName = $('input[name="option_set_name"]').val();
		if ($('input[name="option_set_name"]').val() != '') {
			$('input[name="option_set_name"]').val('').removeClass('error');
			$.post(cartlyAjaxUrl, {
	    		action : 'add-option-set',
	    		option_set_name : $setName
	    	})
	    	.done(function(data) {
	    		var response = $.parseJSON(data);
	    		if (response.status == 'SUCCESS') {
	    			$newSet = $('.set.blank').clone(true, true).removeClass('blank');
	    			$('h4', $newSet).html($setName);
	    			$newSet.attr('data-set-id', response.extra.id).removeClass('initial-hide').hide();
	    			$('.sets').prepend($newSet);
	    			$('.no-options').hide();
	    			$newSet.fadeIn();
	    		}
	    		else {
	    		}
	    	})
	    	.fail(function() {
	    		// Handle Error
	    	});
		} else {
			$('input[name="option_set_name"]').addClass('error');
		}
	});
	
	$(document).on('click', '.delete-option-set', function(event) {
		event.preventDefault();
		$anchor = $(this);
		$set = $anchor.parents('.set');
		$setId = $set.data('setId');
		$set.fadeOut(function() {
			$set.remove();
			if ($('.set').length == 0) {
				$('.no-options').removeClass('initial-hide');
			}
		});
		$.post(cartlyAjaxUrl, {
    		action : 'delete-option-set',
    		option_set_id : $setId
    	})
    	.done(function(data) {
    		var response = $.parseJSON(data);
    		if (response.status == 'SUCCESS') {
    		}
    		else {
    		}
    	})
    	.fail(function() {
    		// Handle Error
    	});
	});
	
	$(document).on('click', '.add-option-set-option', function(event) {
		event.preventDefault();
		$row = $(this).parents('tr');
		if ($('input[name="option_name"]', $row).val() != '') {
		
			$name = $('input[name="option_name"]', $row).val();
			$price = $('input[name="option_price"]', $row).val();
			$shipping = $('input[name="option_shipping"]', $row).val();
			$quantity = $('input[name="option_quantity"]', $row).val();
			$newRow = $('<tr/>');
			$newRow.append($('<td>').html($name));
			$newRow.append($('<td>').html($price));
			$newRow.append($('<td>').html($shipping));
			$newRow.append($('<td>').html($quantity));
			$newRow.append($('<td>').html('<a href="#" class="delete-option-set-option delete">Delete</a>'));
			$row.before($newRow.hide());
			$newRow.fadeIn();
			
			$('input[type=text]', $row).val('').removeClass('error');
			$.post(cartlyAjaxUrl, {
	    		action : 'add-option-set-option',
	    		set_id : $row.parents('.set').data('setId'),
	    		name : $name,
	    		price : $price,
	    		shipping : $shipping,
	    		quantity : $quantity
	    	})
	    	.done(function(data) {
	    		var response = $.parseJSON(data);
	    		if (response.status == 'SUCCESS') {
	    			$newRow.attr('data-set-option-id', response.extra.id);
	    		}
	    		else {
	    		}
	    	})
	    	.fail(function() {
	    		// Handle Error
	    	});
		} else {
			$('input[name="option_name"]', $row).addClass('error');
		}
	});
	
	$(document).on('click', '.delete-option-set-option', function(event) {
		event.preventDefault();
		$anchor = $(this);
		$option = $anchor.parents('tr');
		$optionId = $option.data('setOptionId');
		$option.fadeOut(function() {
			$option.remove();
		});
		$.post(cartlyAjaxUrl, {
    		action : 'delete-option-set-option',
    		option_id : $optionId
    	})
    	.done(function(data) {
    		var response = $.parseJSON(data);
    		if (response.status == 'SUCCESS') {
    		}
    		else {
    		}
    	})
    	.fail(function() {
    		// Handle Error
    	});
	});
	
});

function BuildOptionRow(row, name, price, shipping, quantity, index) {
	$(row).append('<td><input type="text" name="option_name[]" placeholder="Name" class="medium-text" value="'+name+'" /></td>');
	$(row).append('<td><input type="text" name="option_price[]" placeholder="Price" class="small-text" value="'+price+'" /></td>');
	$(row).append('<td><input type="text" name="option_shipping[]" placeholder="Shipping" class="small-text" value="'+shipping+'"></td>');
	$(row).append('<td><input type="text" name="option_quantity[]" placeholder="Quantity" class="small-text" value="'+quantity+'" /> <a href="#" class="delete-option delete">Delete</a><input type="hidden" name="option_key[]" value="cartly_option_'+MakeId()+'" /></td>');
}

function MakeId()
{
	var text = '';
	var possible = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';

    for (var index = 0; index < 8; index++) {
        text += possible.charAt(Math.floor(Math.random() * possible.length));
    }

    return text;
}

})(jQuery);