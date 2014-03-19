<?php

$data = new CartlyData();
$sets = $data->GetOptionSets();

?>
<div class="wrap">
    <h2>Cartly Product Option Sets</h2>
    <h3>Create Option Set</h3>
    <form action="#" method="post" class="option-set-form clear">
	    <input type="text" name="option_set_name" placeholder="Name" class="required" />
	    <a href="#" class="add-option-set add-new-h2">Add New</a>
    </form>
    
    <div class="option-sets">
    	<h3>Option Sets</h3>
    	
    	<div class="sets">
    	
    	<?php if (!empty($sets)) : ?>
    	
    	<?php foreach($sets as $set) : ?>
    		
    		<?php $options = $data->GetOptionSetOptions($set->id); ?>
    		
    		<div class="set clear" data-set-id="<?php echo $set->id; ?>">
    		
    			<form action="#" method="post" class="option-set-option-form clear">
    			
    			<h4><?php echo $set->name; ?></h4>
    			<a href="#" class="delete-option-set">Delete</a>
    			<table class="option-set-table cartly-table" cellpadding="0" cellspacing="0">
	    			<tr class="header">
			 	   		<th>Name</th>
						<th>Price</th>
						<th>Shipping</th>
						<th>Quantity</th>
						<th></th>
					</tr>
					
					<?php if (!empty($options)) : ?>
					
						<?php foreach($options as $option) : ?>
						
						<tr class="option" data-set-option-id="<?php echo $option->id; ?>">
							<td><?php echo $option->name; ?></td>
							<td><?php echo $option->price; ?></td>
							<td><?php echo $option->shipping; ?></td>
							<td><?php echo $option->quantity; ?></td>
							<td><a href="#" class="delete-option-set-option delete">Delete</a></td>
						</tr>
						
						<?php endforeach; ?>
					
					<?php endif; ?>
					
					<tr class="option-add">
						<td>
							<input type="text" name="option_name" class="medium-text required" placeholder="Name" />
						</td>
						<td>
							<input type="text" name="option_price" class="small-text" placeholder="Price" />
						</td>
						<td>
							<input type="text" name="option_shipping" class="small-text" placeholder="Shipping" />
						</td>
						<td>
							<input type="text" name="option_quantity" class="small-text" placeholder="Quantity" />
						</td>
						<td>
							<a href="#" class="add-option-set-option add-new-h2">Add</a>
						</td>
					</tr>
				</table>
				
				</form>
				
    		</div>
    		
    	<?php endforeach; ?>
    	
    	<?php endif; ?>
    	
    	<div class="set blank clear initial-hide" data-set-id="">
    	
    		<form action="#" method="post" class="option-set-option-form clear">
    		
			<h4></h4>
			<a href="#" class="delete-option-set">Delete</a>
			<table class="option-set-table cartly-table" cellpadding="0" cellspacing="0">
    			<tr class="header">
		 	   		<th>Name</th>
					<th>Price</th>
					<th>Shipping</th>
					<th>Quantity</th>
					<th></th>
				</tr>
				<tr class="option-add">
					<td>
						<input type="text" name="option_name" class="medium-text required" placeholder="Name" />
					</td>
					<td>
						<input type="text" name="option_price" class="small-text" placeholder="Price" />
					</td>
					<td>
						<input type="text" name="option_shipping" class="small-text" placeholder="Shipping" />
					</td>
					<td>
						<input type="text" name="option_quantity" class="small-text" placeholder="Quantity" />
					</td>
					<td>
						<a href="#" class="add-option-set-option add-new-h2">Add</a>
					</td>
				</tr>
			</table>
			
    		</form>
			
		</div>
	 	   
		<p class="no-options <?php echo !empty($sets) == true ? 'initial-hide' : '' ?>">You have no option sets.</p>
		
 	   </div>
    </div>
</div>