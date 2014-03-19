<?php

$cartlyData = new CartlyData();
$options = $cartlyData->GetProductOptions($post->ID);
$optionSets = $cartlyData->GetOptionSets();
$optionIndex = 0;

?>

<table class="cartly-table product-table" data-post-id="<?php echo $post->ID ?>"> 
    <tr valign="top">
        <td class="metabox_label_column">
            <label for="price">Price</label>
        </td>
        <td>
            <input type="text" id="price" name="price" placeholder="0.00" value="<?php echo @get_post_meta($post->ID, 'price', true); ?>" class="small-text" />
        </td>
    </tr>
    <tr>
        <td class="metabox_label_column">
            <label for="shipping">Shipping</label>
        </td>
        <td>
            <input type="text" id="shipping" name="shipping" placeholder="0.00" value="<?php echo @get_post_meta($post->ID, 'shipping', true); ?>" class="small-text" />
        </td>
        <td></td>
        <td></td>
    </tr>
    <tr>
        <td class="metabox_label_column">
            <label for="quantity">Quantity</label>
        </td>
        <td>
            <input type="text" id="quantity" name="quantity" placeholder="0" value="<?php echo @get_post_meta($post->ID, 'quantity', true); ?>" class="small-text" />
        </td>
        <td></td>
        <td></td>
    </tr>
    <tr class="sale-row">
        <td class="metabox_label_column">
            <label for="on_sale_bold">Sale</label>
        </td>
        <td>
        	<label for="on_sale"><input type="checkbox" id="on_sale" name="on_sale" <?php echo @get_post_meta($post->ID, 'on_sale', true) == 'on' ? 'checked' : '' ?> /> Mark as on sale</label>
        </td>
        <td></td>
        <td></td>
    </tr>
    <tr class="new-row">
        <td class="metabox_label_column">
            <label for="is_new_bold">New</label>
        </td>
        <td>
        	<label for="is_new"><input type="checkbox" id="is_new" name="is_new" <?php echo @get_post_meta($post->ID, 'is_new', true) == 'on' ? 'checked' : '' ?> /> Mark as new</label>
        </td>
        <td></td>
        <td></td>
    </tr>
    
    <tr class="option-label-row <?php echo count($options) == 0 ? 'initial-hide' : ''; ?>">
    	<td></td>
    	<td><small class="option-label note">Name</small></td>
    	<td><small class="option-label note">Price</small></td>
    	<td><small class="option-label note">Shipping</small></td>
    	<td><small class="option-label note">Quantity</small></td>
    </tr>
    
    <?php if (count($options) > 0) : ?>
    
    <?php foreach($options as $option) : $optionPieces = explode(CARTLY_DELIMITER, $option->meta_value); ?>
    
    <tr class="option-row" data-option-id="<?php echo $option->meta_key ?>">
    	<?php if ($optionIndex == 0) : ?>
        <td class="metabox_label_column">
            <label class="options-label">Options</label>
        </td>
        <?php else : ?>
        <td></td>
    	<?php endif; ?>
    	
    	<td>
    		<input type="text" name="option_name[]" placeholder="Name" value="<?php echo $optionPieces[0] ?>" class="medium-text" />
    	</td>
    	<td>
	    	<input type="text" name="option_price[]" placeholder="Price" value="<?php echo $optionPieces[1] ?>" class="small-text" />
    	</td>
    	<td>
	    	<input type="text" name="option_shipping[]" placeholder="Shipping" value="<?php echo $optionPieces[2] ?>" class="small-text" />
    	</td>
    	<td>
	    	<input type="text" name="option_quantity[]" placeholder="Quantity" value="<?php echo $optionPieces[3] ?>" class="small-text" />
	    	<a href="#" class="delete-option delete">Delete</a>
	    	<input type="hidden" name="option_key[]" value="<?php echo $option->meta_key ?>" />
    	</td>
	</tr>
	
	<?php $optionIndex++; endforeach; ?>
	
	<tr class="add-option-row">
        <td class="metabox_label_column"></td>
        <td colspan="5" class="options">
        	<small><strong>Add Single Option:</strong></small> <a href="#" class="add-option">Add</a>
        </td>
    </tr> 
	
    <?php else : ?>
    
    <tr class="add-option-row">
        <td class="metabox_label_column">
            <label class="options-label">Options</label>
        </td>
        <td colspan="5" class="options">
        	<small><strong>Add Single Option:</strong></small> <a href="#" class="add-option">Add</a>
        </td>
    </tr>    
    
    <?php endif; ?>
    
    <?php if (!empty($optionSets)) : ?>
    <tr class="add-option-set-row">
        <td class="metabox_label_column"></td>
        <td colspan="5" class="options">
        	<small><strong>Add Option Set:</strong></small> 
        	<?php foreach($optionSets as $optionSet) : ?>
        	<a href="#" class="get-product-option-set" data-option-set-id="<?php echo $optionSet->id; ?>"><?php echo $optionSet->name; ?></a>
			<?php endforeach; ?>
        </td>
    </tr>
    <?php endif; ?>
    
</table>