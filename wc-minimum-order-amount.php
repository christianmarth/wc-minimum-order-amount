<?php

   /*
   Plugin Name: WC Minimum Order Amount With Override
   Description: Add the option for a WooCommerce minimum order amount, as well as the options to change the notification texts for the cart and checkout pages
   Version: 1.0
   Author: Hannah Swain
   Author URI: https://github.com/hannahswain
   License: GPLv3 or later License
   URI: http://www.gnu.org/licenses/gpl-3.0.html
   Original snippet source: https://docs.woocommerce.com/document/minimum-order-amount/
   */

   if ( ! defined( 'ABSPATH' ) ) {
       exit; // Exit if accessed directly
   }

  /* Check if WooCommerce is active */

   if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {

  /* Settings */

  add_filter( 'woocommerce_general_settings','hs_woo_minimum_order_settings', 10, 2 );
  function hs_woo_minimum_order_settings( $settings ) {

      $settings[] = array(
        'title' => __( 'Minimum order settings', 'wc_minimum_order_amount' ),
        'type' => 'title',
        'desc' => 'Set the minimum order amount and adjust notifications',
        'id' => 'wc_minimum_order_settings',
      );

        // Minimum order amount
        $settings[] = array(
          'title'             => __( 'Minimum order amount', 'woocommerce' ),
          'desc'              => __( 'Leave this empty if all orders are accepted, otherwise set the minimum order amount', 'wc_minimum_order_amount' ),
          'id'                => 'wc_minimum_order_amount_value',
          'default'           => '',
          'type'              => 'number',
          'desc_tip'          => true,
          'css'      => 'width:70px;',
      );

      
      // Name of coupon that overrides minimum order amount
       $settings[] = array(
          'title'             => __( 'Coupon override name', 'woocommerce' ),
          'desc'              => __( 'The name of the coupon that overrides the minimum order value', 'wc_minimum_order_amount' ),
          'id'                => 'wc_minimum_order_amount_coupon_override_name',
          'default'           => '',
          'type'              => 'text',
          'desc_tip'          => true,
          'css'      => 'width:500px;',          
      );   

      // Coupon override minimum order amount
      $settings[] = array(
          'title'             => __( 'Coupon override minimum order amount', 'woocommerce' ),
          'desc'              => __( 'If a coupon overrides the minimum order amount, what is the new minimum order amount', 'wc_minimum_order_amount' ),
          'id'                => 'wc_minimum_order_amount_coupon_override_value',
          'default'           => '',
          'type'              => 'number',
          'desc_tip'          => true,
          'css'      => 'width:70px;',          
      );

      // Cart message
        $settings[] = array(
          'title'    => __( 'Cart message', 'woocommerce' ),
          'desc'     => __( 'Show this message if the current order total is less than the defined minimum - for example "50".', 'wc_minimum_order_amount' ),
          'id'       => 'wc_minimum_order_cart_notification',
          'default'  => 'Your current order total is %s — your order total excluding delivery must be at least %s.',
          'type'     => 'text',
          'desc_tip' => true,
          'css'      => 'width:500px;',
      );

      // Checkout message
        $settings[] = array(
          'title'    => __( 'Checkout message', 'woocommerce' ),
          'desc'     => __( 'Show this message if the current order total is less than the defined minimum', 'wc_minimum_order_amount' ),
          'id'       => 'wc_minimum_order_checkout_notification',
          'default'  => 'Your current order total is %s — your order excluding delivery must be at least %s.',
          'type'     => 'text',
          'desc_tip' => true,
          'css'      => 'width:500px;',
        );

      $settings[] = array( 'type' => 'sectionend', 'id' => 'wc_minimum_order_settings' );
      return $settings;
  }

/* Notices and checks */

add_action( 'woocommerce_checkout_process', 'hs_wc_minimum_order_amount' );
add_action( 'woocommerce_before_cart' , 'hs_wc_minimum_order_amount' );

function hs_wc_minimum_order_amount() {

      // Get the minimum value from settings
      $minimum = get_option( 'wc_minimum_order_amount_value' );
	
	  // Check to see if override coupon exists
	  
	  $coupon = get_option( 'wc_minimum_order_amount_coupon_override_name'); //solacres"; // ID of the coupon
	  $coupon_mov = get_option ( 'wc_minimum_order_amount_coupon_override_value' ); // 15 ; // Minimun Order Value if coupon is present
      $applied_coupons = WC()->cart->applied_coupons;
      $cart_total = WC()->cart->subtotal; // Use the subtotal to exclude delivery from the minimum
    
      // check if the minimum value has even been set
      if ($minimum) {
      if ( !in_array($coupon,$applied_coupons) && $cart_total < $minimum  && $cart_total > $coupon_mov) {

        if( is_cart() ) {

            wc_print_notice(
                sprintf( get_option( 'wc_minimum_order_cart_notification' ),
                    wc_price( $cart_total ),
                    wc_price( $minimum )
                ), 'error'
            );

        } else {

            wc_add_notice(
                sprintf( get_option( 'wc_minimum_order_checkout_notification' ) ,
                    wc_price( $cart_total ),
                    wc_price( $minimum )
                ), 'error'
            );
                }
            }
        }
    }
}
