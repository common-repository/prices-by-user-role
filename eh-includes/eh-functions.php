<?php

//check class dependencies exist or not
if (!class_exists("Eh_Dependencies")) {
    require_once('class-eh-dependencies.php');
}

//check woocommerce is active function exist
if (!function_exists('eh_is_woocommerce_active')) {

    function eh_is_woocommerce_active() {
        return Eh_Dependencies::woocommerce_active_check();
    }

}
add_action('woocommerce_init', 'eh_compatiblity_fix');

function eh_compatiblity_fix() {
    if (!function_exists('wc_get_price_including_tax')) {

        function wc_get_price_including_tax($product, $args = array()) {
            $args = wp_parse_args($args, array(
                'qty' => '',
                'price' => '',
                    ));

            $price = '' !== $args['price'] ? max(0.0, (float) $args['price']) : $product->get_price();
            $qty = '' !== $args['qty'] ? max(0.0, (float) $args['qty']) : 1;

            if ('' === $price) {
                return '';
            } elseif (empty($qty)) {
                return 0.0;
            }

            $line_price = $price * $qty;
            $return_price = $line_price;

            if ($product->is_taxable()) {
                if (!wc_prices_include_tax()) {
                    $tax_rates = WC_Tax::get_rates($product->get_tax_class());
                    $taxes = WC_Tax::calc_tax($line_price, $tax_rates, false);
                    $tax_amount = WC_Tax::get_tax_total($taxes);
                    $return_price = round($line_price + $tax_amount, wc_get_price_decimals());
                } else {
                    $tax_rates = WC_Tax::get_rates($product->get_tax_class());
                    $base_tax_rates = WC_Tax::get_base_tax_rates($product->get_tax_class(true));

                    /**
                     * If the customer is excempt from VAT, remove the taxes here.
                     * Either remove the base or the user taxes depending on woocommerce_adjust_non_base_location_prices setting.
                     */
                    $customer_wc = WC()->customer;
                    if (!empty($customer_wc) && WC()->customer->get_is_vat_exempt()) {
                        $remove_taxes = apply_filters('woocommerce_adjust_non_base_location_prices', true) ? WC_Tax::calc_tax($line_price, $base_tax_rates, true) : WC_Tax::calc_tax($line_price, $tax_rates, true);
                        $remove_tax = array_sum($remove_taxes);
                        $return_price = round($line_price - $remove_tax, wc_get_price_decimals());

                        /**
                         * The woocommerce_adjust_non_base_location_prices filter can stop base taxes being taken off when dealing with out of base locations.
                         * e.g. If a product costs 10 including tax, all users will pay 10 regardless of location and taxes.
                         * This feature is experimental @since 2.4.7 and may change in the future. Use at your risk.
                         */
                    } elseif ($tax_rates !== $base_tax_rates && apply_filters('woocommerce_adjust_non_base_location_prices', true)) {
                        $base_taxes = WC_Tax::calc_tax($line_price, $base_tax_rates, true);
                        $modded_taxes = WC_Tax::calc_tax($line_price - array_sum($base_taxes), $tax_rates, false);
                        $return_price = round($line_price - array_sum($base_taxes) + array_sum($modded_taxes), wc_get_price_decimals());
                    }
                }
            }
            return apply_filters('woocommerce_get_price_including_tax', $return_price, $qty, $product);
        }

    }

    if (!function_exists('wc_get_price_to_display')) {

        function wc_get_price_to_display($product, $args = array()) {
            $args = wp_parse_args($args, array(
                'qty' => 1,
                'price' => $product->get_price(),
            ));

            $price = $args['price'];
            $qty = $args['qty'];

            return 'incl' === get_option('woocommerce_tax_display_shop') ? wc_get_price_including_tax($product, array('qty' => $qty, 'price' => $price)) : wc_get_price_excluding_tax($product, array('qty' => $qty, 'price' => $price));
        }

    }

    if (!function_exists('wc_get_price_excluding_tax')) {

        function wc_get_price_excluding_tax($product, $args = array()) {
            $args = wp_parse_args($args, array(
                'qty' => '',
                'price' => '',
            ));

            $price = '' !== $args['price'] ? max(0.0, (float) $args['price']) : $product->get_price();
            $qty = '' !== $args['qty'] ? max(0.0, (float) $args['qty']) : 1;

            if ('' === $price) {
                return '';
            } elseif (empty($qty)) {
                return 0.0;
            }

            if ($product->is_taxable() && wc_prices_include_tax()) {
                $tax_rates = WC_Tax::get_base_tax_rates($product->get_tax_class(true));
                $taxes = WC_Tax::calc_tax($price * $qty, $tax_rates, true);
                $price = WC_Tax::round($price * $qty - array_sum($taxes));
            } else {
                $price = $price * $qty;
            }

            return apply_filters('woocommerce_get_price_excluding_tax', $price, $qty, $product);
        }

    }

    if (!function_exists('wc_format_sale_price')) {

        function wc_format_sale_price($regular_price, $sale_price) {
            $price = '<del>' . ( is_numeric($regular_price) ? wc_price($regular_price) : $regular_price ) . '</del> <ins>' . ( is_numeric($sale_price) ? wc_price($sale_price) : $sale_price ) . '</ins>';
            return apply_filters('woocommerce_format_sale_price', $price, $regular_price, $sale_price);
        }

    }

    if (!function_exists('wc_format_price_range')) {

        function wc_format_price_range($from, $to) {
            $price = sprintf(_x('%1$s &ndash; %2$s', 'Price range: from-to', 'woocommerce'), is_numeric($from) ? wc_price($from) : $from, is_numeric($to) ? wc_price($to) : $to);
            return apply_filters('woocommerce_format_price_range', $price, $from, $to);
        }

    }
}
