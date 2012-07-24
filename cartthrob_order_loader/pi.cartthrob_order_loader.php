<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
 
$plugin_info = array(
						'pi_name'			=> 'CartThrob Order Loader',
						'pi_version'		=> '1',
						'pi_author'			=> 'Chris Newton',
						'pi_author_url'		=> 'http://www.cartthrob.com',
						'pi_description'	=> 'This reloads an existing order to the cart. This does not reload subscriptions or permissions attached to items. ',
						'pi_usage'			=> Cartthrob_quantity_limiter::usage()
					);

class Cartthrob_order_loader
{
 
	function __construct()
	{
		$this->EE =& get_instance();
		$this->EE->load->add_package_path(PATH_THIRD.'cartthrob/');
		$this->EE->load->library('cartthrob_loader');
		$this->EE->load->library('number');
		
	}
	public function load()
	{
		$this->EE->load->model("order_model");
		$order_items = $this->EE->order_model->get_order_items($this->EE->TMPL->fetch_param('entry_id'));
		if (!$order_items)
		{
			return FALSE; 
		}
 
		$default_columns = array(
			'row_id',
			'row_order',
			'order_id',
			'entry_id',
			'title',
			'quantity',
			'price',
			'price_plus_tax',
			'weight',
			'shipping',
			'no_tax',
			'no_shipping',
			'license_number',
		); 
		foreach ($order_items  as $key => $item)
		{
 			$data = array(
				'entry_id' => element('entry_id',$item),
				'product_id' => element('entry_id',$item), 
				'quantity' => element('quantity',$item)
			);
			$data['no_shipping'] = bool_string(element("no_shipping", $item)); 
			$data['no_tax'] = bool_string(element("no_tax", $item)); 

			$data['item_options'] =  array_diff_key($item, array_flip($default_columns)); 

			if (! bool_string(element("on_the_fly", $item,"0")))
			{
				$data['class'] = 'product';
			}
			else
			{
				$data['price'] = element("price",$item); 
				$data['weight'] = element("weight",$item); 
				$data['shipping'] = element("shipping", $item); 
				$data['title'] = element("title",$item);
			}
		
			$new_item = $this->EE->cartthrob->cart->add_item($data);
		
			if ($new_item && $value = element("license_number", $item))
			{
				$new_item->set_meta('license_number', TRUE);
			}

		} 
		if ($this->EE->cartthrob->cart->check_inventory())
		{
			$this->EE->cartthrob->cart->save(); 
			return TRUE; 
		} 
		else
		{
			return FALSE; 
		}
 	}
 
	public function usage()
	{
		ob_start();
?>

Docs: 

This will all of the items from a past order
{exp:cartthrob_order_loader:load entry_id="123"}


<?php
		$buffer = ob_get_contents();
		ob_end_clean();
		return $buffer;
	} /* End of usage() function */
	
}