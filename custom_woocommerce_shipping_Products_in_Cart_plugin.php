<?php

/**
 * Plugin Name: Ansum custom woocommerce Shipping
 * Plugin URI: https://ansum.com.np
 * Author: Ansum
 * Author URI: https://ansum.com.np
 * Description: Ansum custom woocommerce Shipping plugin
 * Version: 0.0.1
 */
 
 add_action( 'woocommerce_shipping_init', 'ansum_shipping_init' );
 
 function ansum_shipping_init() {
     if ( ! class_exists( 'WC_ANSUM_SHIPPING') ) {
         class WC_ANSUM_SHIPPING extends WC_Shipping_Method {
            
            public function __construct() {
                $this->id                 = 'ansum_shipping'; // Id for your shipping method. Should be uunique.
				$this->method_title       = __( 'ansum Shipping' );  // Title shown in admin
				$this->method_description = __( 'This plugin is custom designed by Ansum. This plugin is used to trigger different shipping prices based on the products available in the cart' ); // Description shown in admin

				$this->enabled            = "yes"; // This can be added as an setting but for this example its forced enabled
				$this->title              = "Ansum Shipping"; // This can be added as an setting but for this example its forced.

				$this->init();
            }
            
            public function init() {
                // Load the settings API
				$this->init_form_fields(); // This is part of the settings API. Override the method to add your own settings
				$this->init_settings(); // This is part of the settings API. Loads settings you previously init.

				// Save settings in admin if you have any defined
				add_action( 'woocommerce_update_options_shipping_' . $this->id, array( $this, 'process_admin_options' ) );
            }
            
            public function calculate_shipping($package = array()) {
	    // Get the products in the cart
	    $products = WC()->cart->get_cart_contents();
	    // Get the state of shipping
	    $state = WC()->customer->get_shipping_state();

	    // Set the base price of the shipping method
	    $price = 0;

	    // Loop through each product in the cart
	    foreach ( $products as $product ) {
		// Get the product's category
		$product_category = get_the_terms( $product['product_id'], 'product_cat' );
		// Check if the product is in the "Clothing" category
		if ( $product_category && $product_category[0]->slug == 'clothing' ) {
		    // Add an additional $5 to the shipping cost for each product in the "Clothing" category
		    $price += 5;
		}
		// Check if the shipping state is "California"
		if ( $state == 'CA' ) {
		    // Add an additional $10 to the shipping cost for orders being shipped to California
		    $price += 10;
		}
	    }

	    $rate = array(
		'label' => $this->title,
		'cost' => $price,
		'calc_tax' => 'per_item'
	    );

	    // Register the rate
	    $this->add_rate( $rate );
	}    
            
            
            
         }
     }
 }
 
 add_filter( 'woocommerce_shipping_methods', 'add_ansum_method');
 
 function add_ansum_method( $methods ) {
    $methods['ansum_shipping'] = 'WC_ANSUM_SHIPPING';
    return $methods;
 }
