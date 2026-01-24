<?php
	namespace google_marketing_tools;
	class twoperformant extends master
    {
		public function get_conversion($order_data) {
		    $tp_unique = $this->config->get('google_all_2performant_campaign_id_'.$this->id_store);
            $tp_confirm = $this->config->get('google_all_2performant_confirm_id_'.$this->id_store);

            if(empty($tp_unique) || empty($tp_confirm))
                return '';
            
            $description = "";
            foreach ($order_data['products'] as $product) {
                $description .= $product['name'] . ', ';
            }
            $description = rtrim($description, ', ');
            $description = urlencode($description);
            $sale_value = $this->getOrderSubtotal($order_data['order_id']);
            $transaction_id = $order_data['order_id'];

            $src = "https://event.2performant.com/events/salecheck?amount={$sale_value}&campaign_unique={$tp_unique}&confirm={$tp_confirm}&description={$description}&transaction_id={$transaction_id}";

		    $iframe = '<!-- 2Performant script -->
                    <iframe height="1" width="1" scrolling="no" marginheight="0" marginwidth="0" frameborder="0" src="'.$src.'"></iframe>
            <!-- End 2Performant script -->';
		    
		    return $iframe;
        }
	}
?>