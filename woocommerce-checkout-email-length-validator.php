<?php
/**
 * Plugin Name: WooCommerce Checkout Email Length Validator
 * Plugin URI:  https://github.com/amirrezashf/WooCommerce-Checkout-Email-Length-Validator
 * Description: Validates the minimum length of the local part of billing email addresses during WooCommerce checkout.
 * Version:     1.0.0
 * Author:      Amirreza Shayesteh Far
 * Author URI:  https://github.com/amirrezashf
 * License:     GPL-3.0-only
 * Text Domain: woocommerce-checkout-email-length-validator
 * Requires at least: 6.0
 * Requires PHP: 7.4
 * WC requires at least: 7.0
 */

defined( 'ABSPATH' ) || exit;

final class WCELV_Checkout_Email_Length_Validator {
    const VERSION = '1.0.0';
    const TEXT_DOMAIN = 'woocommerce-checkout-email-length-validator';

    public static function init() {
        add_action( 'before_woocommerce_init', array( __CLASS__, 'declare_compatibility' ) );
        add_action( 'woocommerce_checkout_process', array( __CLASS__, 'validate_classic_checkout' ) );
        add_action( 'woocommerce_after_checkout_validation', array( __CLASS__, 'validate_checkout_data' ), 10, 2 );
    }

    public static function declare_compatibility() {
        if ( class_exists( '\\Automattic\\WooCommerce\\Utilities\\FeaturesUtil' ) ) {
            \Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', __FILE__, true );
        }
    }

    public static function minimum_local_part_length() {
        /**
         * Filter the minimum number of bytes required before the @ in a billing email address.
         *
         * @param int $minimum Minimum length. Default 4.
         */
        return max( 1, absint( apply_filters( 'wcelv_minimum_email_local_part_length', 4 ) ) );
    }

    public static function error_message() {
        return __( 'The entered email address has been detected as unusually short. Please check it and enter a valid email address.', self::TEXT_DOMAIN );
    }

    private static function get_posted_billing_email() {
        if ( ! isset( $_POST['billing_email'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing -- WooCommerce owns checkout nonce verification.
            return '';
        }

        return sanitize_email( wp_unslash( $_POST['billing_email'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Missing
    }

    private static function is_too_short( $email ) {
        $email = sanitize_email( $email );
        if ( '' === $email || ! is_email( $email ) ) {
            return false; // Let WooCommerce/core email validation report malformed addresses.
        }

        $at = strrpos( $email, '@' );
        if ( false === $at ) {
            return false;
        }

        $local_part = substr( $email, 0, $at );
        return strlen( $local_part ) < self::minimum_local_part_length();
    }

    public static function validate_classic_checkout() {
        static $validated = false;
        if ( $validated ) {
            return;
        }
        $validated = true;

        $email = self::get_posted_billing_email();
        if ( self::is_too_short( $email ) && function_exists( 'wc_add_notice' ) ) {
            $message = self::error_message();
            if ( ! function_exists( 'wc_has_notice' ) || ! wc_has_notice( $message, 'error' ) ) {
                wc_add_notice( $message, 'error' );
            }
        }
    }

    public static function validate_checkout_data( $data, $errors ) {
        if ( ! is_wp_error( $errors ) ) {
            return;
        }

        $email = isset( $data['billing_email'] ) ? sanitize_email( $data['billing_email'] ) : '';
        if ( self::is_too_short( $email ) && ! $errors->get_error_message( 'wcelv_email_local_part_too_short' ) ) {
            $errors->add( 'wcelv_email_local_part_too_short', self::error_message() );
        }
    }
}

add_action( 'plugins_loaded', array( 'WCELV_Checkout_Email_Length_Validator', 'init' ) );
