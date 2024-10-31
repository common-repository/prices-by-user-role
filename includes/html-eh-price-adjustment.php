<?php
?>
<script type="text/javascript">

    jQuery(window).load(function () {
        // Ordering
        jQuery('.price_adjustment tbody').sortable({
            items: 'tr',
            cursor: 'move',
            axis: 'y',
            handle: '.sort',
            scrollSensitivity: 40,
            forcePlaceholderSize: true,
            helper: 'clone',
            opacity: 0.65,
            placeholder: 'wc-metabox-sortable-placeholder',
            start: function (event, ui) {
                ui.item.css('baclbsround-color', '#f6f6f6');
            },
            stop: function (event, ui) {
                ui.item.removeAttr('style');
                price_adjustment_row_indexes();
            }
        });

        hide_cart_placeholder_text('#eh_pricing_discount_cart_unregistered_user', '#eh_pricing_discount_cart_unregistered_user_text');
        hide_placeholder_text('#eh_pricing_discount_price_unregistered_user', '#eh_pricing_discount_price_unregistered_user_text');
        hide_user_placeholder_text('#eh_pricing_discount_price_user_role', '#eh_pricing_discount_price_user_role_text');
        hide_user_placeholder_text('#eh_pricing_discount_cart_user_role', '#eh_pricing_discount_cart_user_role_text');
        hide_user_replace_addtocart();
        hide_tax_options_table('#eh_pricing_discount_enable_tax_options', '#tax_options_table');
        replace_addtocart();
        price_suffix();


        jQuery('#eh_pricing_discount_cart_unregistered_user').change(function () {
            hide_cart_placeholder_text('#eh_pricing_discount_cart_unregistered_user', '#eh_pricing_discount_cart_unregistered_user_text');
        });

        jQuery('#eh_pricing_discount_price_unregistered_user').change(function () {
            hide_placeholder_text('#eh_pricing_discount_price_unregistered_user', '#eh_pricing_discount_price_unregistered_user_text');
        });

        jQuery('#eh_pricing_discount_cart_user_role').change(function () {
            hide_user_placeholder_text('#eh_pricing_discount_cart_user_role', '#eh_pricing_discount_cart_user_role_text');
        });

        jQuery('#eh_pricing_discount_price_user_role').change(function () {
            hide_user_placeholder_text('#eh_pricing_discount_price_user_role', '#eh_pricing_discount_price_user_role_text');
        });

        jQuery('#eh_pricing_discount_replace_cart_user_role').change(function () {
            hide_user_replace_addtocart();
        });

        jQuery('#eh_pricing_discount_enable_tax_options').change(function () {
            hide_tax_options_table('#eh_pricing_discount_enable_tax_options', '#tax_options_table');
        });

        jQuery('#eh_pricing_discount_replace_cart_unregistered_user').change(function () {
            replace_addtocart();
        });

        jQuery('#eh_pricing_discount_enable_price_suffix').change(function () {
            price_suffix();
        });


        function price_adjustment_row_indexes() {
            jQuery('.price_adjustment tbody tr').each(function (index, el) {
                jQuery('input.order', el).val(parseInt(jQuery(el).index('.price_adjustment tr')));
            });
        }
        ;

        function hide_placeholder_text(check, hide_field) {
            if (jQuery(check).is(":checked")) {
                jQuery(hide_field).closest("tr").show();
            } else {
                jQuery(hide_field).closest("tr").hide();
            }
        }
        ;

        function hide_cart_placeholder_text(check, hide_field) {
            if (jQuery(check).is(":checked")) {
                jQuery(hide_field).closest("tr").show();

            } else {
                jQuery(hide_field).closest("tr").hide();

            }
        }
        ;

        function hide_user_placeholder_text(check, hide_field) {
            options = jQuery(check).val();
            if (options != null) {
                jQuery(hide_field).closest("tr").show();
            } else {
                jQuery(hide_field).closest("tr").hide();
            }
        }
        ;


        function hide_user_replace_addtocart() {
            options = jQuery('#eh_pricing_discount_replace_cart_user_role').val();
            if (options != null) {
                jQuery('#eh_pricing_discount_replace_cart_user_role_text_product').closest("tr").show();
                jQuery('#eh_pricing_discount_replace_cart_user_role_text_shop').closest("tr").show();
                jQuery('#eh_pricing_discount_replace_cart_user_role_url_shop').closest("tr").show();
            } else {
                jQuery('#eh_pricing_discount_replace_cart_user_role_text_product').closest("tr").hide();
                jQuery('#eh_pricing_discount_replace_cart_user_role_text_shop').closest("tr").hide();
                jQuery('#eh_pricing_discount_replace_cart_user_role_url_shop').closest("tr").hide();
            }
        }
        ;
        function hide_tax_options_table(check, hide_field) {
            if (jQuery(check).is(":checked")) {
                jQuery(hide_field).show();
            } else {
                jQuery(hide_field).hide();
            }
        }
        ;
        //---------------------------edited by nandana
        //To show/hide placeholder text and url for replace add to cart button for unregistered user
        function replace_addtocart() {
            if (jQuery('#eh_pricing_discount_replace_cart_unregistered_user').is(":checked")) {
                jQuery('#eh_pricing_discount_replace_cart_unregistered_user_text_shop').closest("tr").show();
                jQuery('#eh_pricing_discount_replace_cart_unregistered_user_url_shop').closest("tr").show();
                jQuery('#eh_pricing_discount_replace_cart_unregistered_user_text_product').closest("tr").show();
            } else {
                jQuery('#eh_pricing_discount_replace_cart_unregistered_user_text_shop').closest("tr").hide();
                jQuery('#eh_pricing_discount_replace_cart_unregistered_user_url_shop').closest("tr").hide();
                jQuery('#eh_pricing_discount_replace_cart_unregistered_user_text_product').closest("tr").hide();
            }
        }
        ;
        //----------------------------
        function price_suffix() {
            options = jQuery('#eh_pricing_discount_enable_price_suffix').val();
            if (options == 'general') {
                jQuery('#eh_pricing_discount_price_general_price_suffix').closest("tr").show();
                jQuery('#price_suffix_table').hide();
            } else if (options == 'role_specific') {
                jQuery('#eh_pricing_discount_price_general_price_suffix').closest("tr").hide();
                jQuery('#price_suffix_table').show();
            } else {
                jQuery('#eh_pricing_discount_price_general_price_suffix').closest("tr").hide();
                jQuery('#price_suffix_table').hide();
            }
        }
        ;

    });

</script>
