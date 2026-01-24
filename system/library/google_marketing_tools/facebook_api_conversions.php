<?php
namespace google_marketing_tools;
class facebook_api_conversions extends master
{
    /**
     * Product identification
     *
     * @var string
     */
    private $product_id;

    public function __construct($registry) {
        parent::__construct($registry);
        $this->product_id = $this->config->get("google_all_fb_pixel_id_like_".$this->id_store);
    }

    public function set_track_info_api($data_gmt) {
        $track_info = array();

        $current_view = $this->get_current_view();

        //ViewContent
        if (in_array($current_view, array("product"))) {
            $product_info = array_key_exists('product_details', $data_gmt) ? $data_gmt['product_details']['product'] : '';

            if(empty($product_info)) {
                $product_id = $product_id_original = $this->get_current_product_id();
                $product_info = $this->model_catalog_product->getProduct($product_id);
                $price = $this->get_product_price($product_info);
            } else {
                $product_id = $product_info['product_id_fb'];
                $product_id_original = $product_info['product_id'];
                $price = $product_info['prices']['price']['price'];
            }

            $cat_tree = $this->get_product_category_name($product_id_original);
            if(!empty($product_info)) {


                $track_info = array(
                    'event_name' => 'ViewContent',
                    'content_type' => 'product',
                    'currency' => $this->currency_code,
                    'value' => $price,
                    'content_ids' => array($product_id),
                    'content_name' => htmlspecialchars_decode(strip_tags($product_info['name'])),
                    'content_category' => $cat_tree,
                    "contents" => array(
                        array(
                            "id" => $product_id,
                            "quantity" => 1,
                            "item_price" => $price,
                        ),
                    ),
                    "num_items" => 1,
                );
            }
        }
        elseif(in_array($current_view, array("category")))
        {
            $product_listed = array_key_exists('products_listed', $data_gmt) ? $data_gmt['products_listed'] : '';

            if(!empty($product_listed)) {
                $cat_tree = implode (' > ', $this->get_category_names_array_category_view());
                $track_info = array(
                    'event_name' => 'ViewCategory',
                    'content_type' => 'product_group',
                    'content_category' => $cat_tree,
                    'value' => !empty($product_listed) ? $this->get_total_price_products($product_listed) : '0.00',
                    'currency' => $this->currency_code,
                    'content_ids' => $this->get_product_ids_array($product_listed, "product_id_fb"),
                    'contents' => $this->get_contents_array($product_listed),
                    'num_items' => count($product_listed),
                );

            }
        }
        elseif(in_array($current_view, array("search")))
        {
            $product_listed = array_key_exists('products_listed', $data_gmt) ? $data_gmt['products_listed'] : '';

            if(!empty($product_listed)) {
                $track_info = array(
                    'event_name' => 'Search',
                    'content_type' => 'product_group',
                    'search_string' => $this->search,
                    'value' => !empty($product_listed) ? $this->get_total_price_products($product_listed) : '0.00',
                    'content_ids' => $this->get_product_ids_array($product_listed, "product_id_fb"),
                    'currency' => $this->currency_code,
                    'contents' => $this->get_contents_array($product_listed),
                    'num_items' => count($product_listed),
                );
            }
        }
        elseif(in_array($current_view, array("account_success", "affiliate_register")))
        {
            $track_info = array(
                'event_name' => 'Customer registered',
                'currency' => $this->currency_code,
                'value' => '0.00'
            );
        }
        elseif( in_array($current_view, array("checkout")) && $this->cart_products != '')
        {
            $quantity = 0;
            $content_ids = array();
            foreach ($this->cart_products as $key => $pro) {
                $content_ids[] = $this->get_real_product_identificator($pro, $this->product_id);
            }

            $value = $this->get_total_price_products($this->cart_products);

            $track_info = array(
                'event_name' => 'InitiateCheckout',
                'currency' => $this->currency_code,
                'value' => $value,
                'content_ids' => $content_ids,
                'content_type' => count($this->cart_products) > 1 ? 'product_group' : 'product',
                'contents' => $this->get_contents_array($this->cart_products),
                'num_items' => $this->cart_units,
            );
        }
        elseif( in_array($current_view, array("purchase")) && array_key_exists('order_data', $data_gmt))
        {
            $order_data = $data_gmt['order_data'];

            if(!empty($order_data))
            {
                $quantity = 0;
                $content_ids = array();
                foreach ($order_data['products'] as $key => $pro) {
                    $content_ids[] = $this->get_real_product_identificator($pro, $this->product_id);
                    $quantity += $pro['quantity'];
                }

                $email = !empty($order_data['customer']['general']['email']) ? $order_data['customer']['general']['email'] : '';
                $phone = !empty($order_data['customer']['general']['phone']) ? $order_data['customer']['general']['phone'] : '';

                $track_info = array(
                    'event_name' => 'Purchase',
                    'content_type' => count($order_data['products']) > 1 ? 'product_group' : 'product',
                    'currency' => $this->currency_code,
                    'value' => $order_data['order']['total'],
                    'tax' => $order_data['order']['tax'],
                    'shipping_charge' => $order_data['order']['shipping'],
                    'content_ids' => $content_ids,
                    'num_items' => $quantity,
                    'contents' => $this->get_contents_array($order_data['products'], $this->product_id),
                    'extra_info' => array('email' => $email, 'phone' => $phone)
                );
            }
        } else {
            $track_info = array(
                'event_name' => 'PageView',
                'currency' => $this->currency_code,
                'value' => '0.00'
            );
        }

        $track_info["event_source_url"] = $this->get_page_url();

        return $track_info;
    }

    public function get_contents_array($products) {
        $contents = array();
        foreach ($products as $prod) {
            $contents[] = array(
                "id" => !empty($prod['product']['product_id_fb']) ? $prod['product']['product_id_fb'] : $this->get_real_product_identificator($prod, $this->product_id),
                "quantity" => 1,
                "item_price" => !empty($prod['product']['prices']) ? $prod['product']['prices']['price']['price'] : $this->get_product_price($prod['product_id'], true),
            );
        }

        return $contents;
    }
}
?>