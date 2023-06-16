<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Cartthrob_order_loader
{
    public function __construct()
    {
        ee()->load->add_package_path(PATH_THIRD . 'cartthrob/');
        ee()->load->library('cartthrob_loader');
        ee()->load->library('number');
    }

    public function load()
    {
        ee()->load->model('order_model');

        $return = ee()->TMPL->fetch_param('return') ?? 'store/view_cart';
        $order_items = ee()->order_model->getOrderItems(ee()->TMPL->fetch_param('entry_id'));

        if (!$order_items) {
            return false;
        }

        $default_columns = [
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
            'entry_date',
            'discount',
        ];

        foreach ($order_items  as $key => $item) {
            $data = [
                'entry_id' => element('entry_id', $item),
                'product_id' => element('entry_id', $item),
                'quantity' => element('quantity', $item),
            ];

            $data['no_shipping'] = bool_string(element('no_shipping', $item));
            $data['no_tax'] = bool_string(element('no_tax', $item));

            $data['item_options'] = array_diff_key($item, array_flip($default_columns));

            if ((!bool_string(element('on_the_fly', $item, '0')) && !element('nominal_charge', $item)) && !(ee()->TMPL->fetch_param('on_the_fly') !== false && bool_string(ee()->TMPL->fetch_param('on_the_fly')))) {
                $data['class'] = 'product';
            } else {
                $data['price'] = element('price', $item);
                $data['weight'] = element('weight', $item);
                $data['shipping'] = element('shipping', $item);
                $data['title'] = element('title', $item);
            }

            if (isset($data['item_options']['site_id'])) {
                $data['site_id'] = $data['item_options']['site_id'];
                unset($data['item_options']['site_id']);
            }

            $new_item = ee()->cartthrob->cart->add_item($data);

            if ($new_item) {
                if ($value = element('license_number', $item)) {
                    $new_item->set_meta('license_number', true);
                }

                // Price may be set by something else, so let's set it back ot the original order's price i.e. price multiplier
                if (bool_string(ee()->TMPL->fetch_param('update_price')) && (float)element('price', $item) != (float)$new_item->price()) {
                    $new_item->update([
                        'price' => element('price', $item),
                        'class' => 'default', ]
                    );
                }
            }

            // cartthrob_add_to_cart_end hook
            if (ee()->extensions->active_hook('cartthrob_add_to_cart_end') === true) {
                ee()->extensions->call('cartthrob_add_to_cart_end', $new_item);

                if (ee()->extensions->end_script === true) {
                    return;
                }
            }
        }

        if (ee()->cartthrob->cart->check_inventory()) {
            ee()->cartthrob->cart->save();
        }

        ee()->load->helper('url');
        redirect($return);
        exit;
    }
}
