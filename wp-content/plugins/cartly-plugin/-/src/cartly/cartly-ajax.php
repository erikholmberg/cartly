<?php

require_once(sprintf("%s/cartly-data.php", dirname(__FILE__)));
require_once(sprintf("%s/cartly-utilities.php", dirname(__FILE__)));

// Check the POST
if (isset($_POST['action']))
{
	switch ($_POST['action'])
	{
		case 'update-order-status' : UpdateOrderStatus(); break;
		case 'delete-product-option' : DeleteProductOption(); break;
		case 'add-option-set' : AddOptionSet(); break;
		case 'delete-option-set' : DeleteOptionSet(); break;
		case 'add-option-set-option' : AddOptionSetOption(); break;
		case 'delete-option-set-option' : DeleteOptionSetOption(); break;
		case 'get-product-option-set' : GetProductOptionSet(); break;
	}
}

// Ajax Action Methods
function UpdateOrderStatus()
{
	$data = new CartlyData();
	
	if ($data->UpdateOrderStatus($_POST['order_id'], $_POST['status_id']))
	{
		CartlyUtilities::SendJSON('SUCCESS', 'Order status updated.', '001');	
	}
	else
	{
		CartlyUtilities::SendJSON('FAILURE', 'Unable to update order status.', '101');
	}
}

function DeleteProductOption()
{
	$data = new CartlyData();
	
	if ($data->DeleteProductOption($_POST['post_id'], $_POST['option_id']))
	{
		CartlyUtilities::SendJSON('SUCCESS', 'Option deleted.', '001');	
	}
	else
	{
		CartlyUtilities::SendJSON('FAILURE', 'Unable to delete option.', '101');
	}
}

function AddOptionSet()
{
	$data = new CartlyData();
	
	if ($insertId = $data->AddOptionSet($_POST['option_set_name']))
	{
		CartlyUtilities::SendJSON('SUCCESS', 'Option set added.', '001', array('id' => $insertId));	
	}
	else
	{
		CartlyUtilities::SendJSON('FAILURE', 'Unable to add option set.', '101');
	}
}

function DeleteOptionSet()
{
	$data = new CartlyData();
	
	if ($data->DeleteOptionSet($_POST['option_set_id']))
	{
		CartlyUtilities::SendJSON('SUCCESS', 'Option set deleted.', '001');	
	}
	else
	{
		CartlyUtilities::SendJSON('FAILURE', 'Unable to delete option set.', '101');
	}
}

function AddOptionSetOption()
{
	$data = new CartlyData();
	
	if ($insertId = $data->AddOptionSetOption($_POST['set_id'], $_POST['name'], $_POST['price'], $_POST['shipping'], $_POST['quantity']))
	{
		CartlyUtilities::SendJSON('SUCCESS', 'Option added.', '001', array('id' => $insertId));	
	}
	else
	{
		CartlyUtilities::SendJSON('FAILURE', 'Unable to add option.', '101');
	}
}

function DeleteOptionSetOption()
{
	$data = new CartlyData();
	
	if ($data->DeleteOptionSetOption($_POST['option_id']))
	{
		CartlyUtilities::SendJSON('SUCCESS', 'Option deleted.', '001');	
	}
	else
	{
		CartlyUtilities::SendJSON('FAILURE', 'Unable to delete option.', '101');
	}
}

function GetProductOptionSet()
{
	$data = new CartlyData();
	
	if ($set = $data->GetProductOptionSet($_POST['option_set_id']))
	{
		CartlyUtilities::SendJSON('SUCCESS', 'Returning option set.', '001', array('set' => $set));	
	}
	else
	{
		CartlyUtilities::SendJSON('FAILURE', 'Unable to retrieve option set.', '101');
	}
}

?>