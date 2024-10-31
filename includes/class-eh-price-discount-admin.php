<?php

class Class_Eh_Price_Discount_Admin {

    public function __construct() {
        $this->add_filter_for_get_price();

        if (WC()->version < '2.7.0') {
            add_filter('woocommerce_product_tax_class', array($this, 'product_tax_class'), 99, 1);
        } else {
            add_filter('woocommerce_product_get_tax_class', array($this, 'product_tax_class'), 99, 1);
        }

        add_filter('woocommerce_loop_add_to_cart_link', array($this, 'shop_remove_add_to_cart'), 100, 2); // function to remove add to cart from shop page
       // add_action('wp_head', array($this, 'custom_css_for_add_to_cart'));
        add_action('woocommerce_single_product_summary', array($this, 'product_page_remove_add_to_cart_option')); //function to remove add to cart at product page
        add_filter('woocommerce_is_purchasable', array(&$this, 'is_product_purchasable'), 10, 2); //to hide add to cart button when price is hidden
        add_filter('woocommerce_loop_add_to_cart_link', array($this, 'add_to_cart_text_url_replace'), 1, 2); //to replace add to cart with user defined url
        add_filter('woocommerce_product_single_add_to_cart_text', array($this, 'add_to_cart_text_content_replace'), 1, 2); //to replace add to cart with user defined placeholder text for product page
        add_filter('woocommerce_get_price_html', array($this, 'get_price_html'), 9, 2); //to modify display for various options of settings page
        add_filter('woocommerce_product_is_on_sale', array($this, 'product_is_on_sale'), 99, 2);
        //--------------for cart page woocommerce 3.2
        if (WC()->version > '3.1') {
            add_filter('woocommerce_cart_subtotal', array($this, 'xa_get_cart_subtotal'), 100, 1);
            add_filter('woocommerce_cart_product_price', array($this, 'xa_get_product_price'), 100, 2);
            add_filter('woocommerce_cart_product_subtotal', array($this, 'xa_get_product_subtotal'), 100, 3);
        }
        //--------------
        //----for price filter
        add_filter('woocommerce_price_filter_widget_min_amount', array($this, 'eh_get_min_price'), 100, 1);
        add_filter('woocommerce_price_filter_widget_max_amount', array($this, 'eh_get_max_price'), 100, 1);
        //----------
        $this->init_fields();
    }
    
    
        public function shop_remove_add_to_cart($args, $product) {
        if($this->get_product_type($product) == 'variable' || $this->get_product_type($product) == 'grouped'){
            return $args;
        }
        $product_id = $this->get_product_id($product);
        $add_to_cart_link = $args;       
        if (is_user_logged_in()) {
            $remove_settings_cart_roles = get_option('eh_pricing_discount_cart_user_role');
            $remove_product_cart_roles = get_post_meta($product_id, 'eh_pricing_adjustment_product_addtocart_user_role', true);
            if (is_array($remove_product_cart_roles) && in_array($this->current_user_role, $remove_product_cart_roles)) {               
                $add_to_cart_link = '';
            } elseif (is_array($remove_settings_cart_roles) && in_array($this->current_user_role, $remove_settings_cart_roles)) {
                $add_to_cart_link = '';
                $this->get_add_to_cart_placeholder_text();
            }
        } else {
            if ('yes' == (get_post_meta($product_id, 'product_adjustment_hide_addtocart_unregistered', true))) {
                $add_to_cart_link = '';
            } elseif ('yes' == get_option('eh_pricing_discount_cart_unregistered_user')) {
                $add_to_cart_link = '';
                $this->get_add_to_cart_placeholder_text();
            }
        }
        
        return $add_to_cart_link ;
    }

    public function xa_get_product_subtotal($product_subtotal, $product = '', $quantity = '') {
        $price = $product->get_price();
        if ($this->get_product_type($product) == 'variable' || $this->get_product_type($product) == 'variation' || $this->get_product_type($product) == 'grouped') {
            return $product_subtotal;
        }
        if ($product->is_taxable()) {
            if ($this->enable_role_tax) {
                if (is_user_logged_in()) {
                    $user_role = $this->current_user_role;
                } else {
                    $user_role = 'unregistered_user';
                }
                //checks if we have to show price including tax or excluding tax on cart page
                if (!empty($this->role_tax_option[$user_role]) && !empty($this->role_tax_option[$user_role]['tax_option'])) {
                    switch ($this->role_tax_option[$user_role]['tax_option']) {
                        case 'show_price_including_tax':
                            $row_price = wc_get_price_including_tax($product, array('qty' => $quantity));
                            $product_subtotal = wc_price($row_price);


                            $product_subtotal .= ' <small class="tax_label">' . WC()->countries->inc_tax_or_vat() . '</small>';

                            break;
                        case 'show_price_excluding_tax':
                            $row_price = wc_get_price_excluding_tax($product, array('qty' => $quantity));
                            $product_subtotal = wc_price($row_price);


                            $product_subtotal .= ' <small class="tax_label">' . WC()->countries->ex_tax_or_vat() . '</small>';

                            break;
                        case 'show_price_including_tax_shop':
                            $row_price = wc_get_price_excluding_tax($product, array('qty' => $quantity));
                            $product_subtotal = wc_price($row_price);


                            $product_subtotal .= ' <small class="tax_label">' . WC()->countries->ex_tax_or_vat() . '</small>';

                            break;
                        case 'show_price_including_tax_cart_checkout':
                            $row_price = wc_get_price_including_tax($product, array('qty' => $quantity));
                            $product_subtotal = wc_price($row_price);


                            $product_subtotal .= ' <small class="tax_label">' . WC()->countries->inc_tax_or_vat() . '</small>';

                            break;
                        case 'show_price_excluding_tax_shop':
                            $row_price = wc_get_price_including_tax($product, array('qty' => $quantity));
                            $product_subtotal = wc_price($row_price);


                            $product_subtotal .= ' <small class="tax_label">' . WC()->countries->inc_tax_or_vat() . '</small>';

                            break;
                        case 'show_price_excluding_tax_cart_checkout':
                            $row_price = wc_get_price_excluding_tax($product, array('qty' => $quantity));
                            $product_subtotal = wc_price($row_price);


                            $product_subtotal .= ' <small class="tax_label">' . WC()->countries->ex_tax_or_vat() . '</small>';

                            break;
                        case 'default':
                            break;
                    }
                }
            }
        } else {
            $row_price = $price * $quantity;
            $product_subtotal = wc_price($row_price);
        }
        return $product_subtotal;
    }

    public function xa_get_product_price($product_price, $product) {
        if ($this->get_product_type($product) == 'variable' || $this->get_product_type($product) == 'variation' || $this->get_product_type($product) == 'grouped') {
            return $product_price;
        }
        if ($this->enable_role_tax) { //to make dependent on enable tax checkbox
            if (is_user_logged_in()) {
                $user_role = $this->current_user_role;
            } else {
                $user_role = 'unregistered_user';
            }
            //checks if we have to show price including tax or excluding tax on cart page
            if (!empty($this->role_tax_option[$user_role]) && !empty($this->role_tax_option[$user_role]['tax_option'])) {
                switch ($this->role_tax_option[$user_role]['tax_option']) {
                    case 'show_price_including_tax':
                        $product_price = wc_get_price_including_tax($product);
                        $product_price = wc_price($product_price);
                        break;
                    case 'show_price_excluding_tax':
                        $product_price = wc_get_price_excluding_tax($product);
                        $product_price = wc_price($product_price);
                        break;
                    case 'show_price_including_tax_shop':
                        $product_price = wc_get_price_excluding_tax($product);
                        $product_price = wc_price($product_price);
                        break;
                    case 'show_price_including_tax_cart_checkout':
                        $product_price = wc_get_price_including_tax($product);
                        $product_price = wc_price($product_price);
                        break;
                    case 'show_price_excluding_tax_shop':
                        $product_price = wc_get_price_including_tax($product);
                        $product_price = wc_price($product_price);
                        break;
                    case 'show_price_excluding_tax_cart_checkout':
                        $product_price = wc_get_price_excluding_tax($product);
                        $product_price = wc_price($product_price);
                        break;
                    case 'default':
                        break;
                }
            }
        }
        return $product_price;
    }

    public function xa_get_cart_subtotal($cart_subtotal) {

        if ($this->enable_role_tax) { //to make dependent on enable tax checkbox
            //checks if we have to show price including tax or excluding tax on specific page
            if (is_user_logged_in()) {
                $user_role = $this->current_user_role;
            } else {
                $user_role = 'unregistered_user';
            }

            if (!empty($this->role_tax_option[$user_role]) && !empty($this->role_tax_option[$user_role]['tax_option'])) {
                switch ($this->role_tax_option[$user_role]['tax_option']) {
                    case 'show_price_including_tax':
                        $cart_subtotal = wc_price(WC()->cart->cart_contents_total + WC()->cart->tax_total);
                        $cart_subtotal .= ' <small class="tax_label">' . WC()->countries->inc_tax_or_vat() . '</small>';
                        break;
                    case 'show_price_excluding_tax':
                        $cart_subtotal = wc_price(WC()->cart->cart_contents_total);
                        $cart_subtotal .= ' <small class="tax_label">' . WC()->countries->ex_tax_or_vat() . '</small>';
                        break;
                    case 'show_price_including_tax_shop':
                        $cart_subtotal = wc_price(WC()->cart->cart_contents_total);
                        $cart_subtotal .= ' <small class="tax_label">' . WC()->countries->ex_tax_or_vat() . '</small>';
                        break;
                    case 'show_price_including_tax_cart_checkout':
                        $cart_subtotal = wc_price(WC()->cart->cart_contents_total + WC()->cart->tax_total);
                        $cart_subtotal .= ' <small class="tax_label">' . WC()->countries->inc_tax_or_vat() . '</small>';
                        break;
                    case 'show_price_excluding_tax_shop':
                        $cart_subtotal = wc_price(WC()->cart->cart_contents_total + WC()->cart->tax_total);
                        $cart_subtotal .= ' <small class="tax_label">' . WC()->countries->inc_tax_or_vat() . '</small>';
                        break;
                    case 'show_price_excluding_tax_cart_checkout':
                        $cart_subtotal = wc_price(WC()->cart->cart_contents_total);
                        $cart_subtotal .= ' <small class="tax_label">' . WC()->countries->ex_tax_or_vat() . '</small>';
                        break;
                    case 'default':
                        break;
                }
            }
        }return $cart_subtotal;
    }

    public function eh_get_min_price($price) {
        $user_roles = get_option('eh_pricing_discount_product_price_user_role');
        if (is_array($user_roles) && in_array($this->current_user_role, $user_roles)) {
            global $wpdb;
            $all_product_data = $wpdb->get_results("SELECT ID FROM `" . $wpdb->prefix . "posts` where post_type='product' and post_status = 'publish'");
            $min_prices = array();
            for ($i = 0; $i < count($all_product_data); $i++) {
                $p_id = $all_product_data[$i]->{'ID'};
                $product_data = wc_get_product($p_id);
                if ($product_data->is_type('simple')) {
                    $min_prices[$i] = $product_data->get_price();
                } elseif ($product_data->is_type('variable')) {
                    $prices = $product_data->get_variation_prices(true);
                    if (empty($prices['price'])) {
                        continue;
                    }
                    foreach ($prices['price'] as $pid => $old_price) {
                        $pobj = wc_get_product($pid);
                        $prices['price'][$pid] = wc_get_price_to_display($pobj);
                    }
                    $min_prices[$i] = min($prices['price']);
                }
            }
            $price = min($min_prices);
        }
        return $price;
    }

    public function eh_get_max_price($price) {
        $user_roles = get_option('eh_pricing_discount_product_price_user_role');

        if (is_array($user_roles) && in_array($this->current_user_role, $user_roles)) {
            global $wpdb;
            $all_product_data = $wpdb->get_results("SELECT ID FROM `" . $wpdb->prefix . "posts` where post_type='product' and post_status = 'publish'");
            $max_prices = array();
            for ($i = 0; $i < count($all_product_data); $i++) {
                $p_id = $all_product_data[$i]->{'ID'};
                $product_data = wc_get_product($p_id);
                if ($product_data->is_type('simple')) {

                    $max_prices[$i] = $product_data->get_price();
                } elseif ($product_data->is_type('variable')) {
                    $prices = $product_data->get_variation_prices(true);
                    if (empty($prices['price'])) {
                        continue;
                    }
                    foreach ($prices['price'] as $pid => $old_price) {
                        $pobj = wc_get_product($pid);
                        $prices['price'][$pid] = wc_get_price_to_display($pobj);
                    }
                    $max_prices[$i] = max($prices['price']);
                }
            }
            $price = max($max_prices);
        }
        return $price;
    }

    public function product_is_on_sale($on_sale, $product) {
        if ($this->get_product_type($product) == 'grouped' || $this->get_product_type($product) == 'variable' || $this->get_product_type($product) == 'variation') {
            return $on_sale;
        }
        if ($this->is_hide_price($product) === true || $this->is_catalog_mode_on($product) === true || $this->is_hide_regular_price($product)) {
            $on_sale = false;
        } else {
            if ($this->get_product_type($product) != 'grouped') {
                $regular_price = $product->get_regular_price();
                $sale_price = $product->get_price();
                if (empty($sale_price)) {
                    $on_sale = false;
                } else {
                    if ($sale_price < $regular_price) {
                        $on_sale = true;
                    }
                }
            }
        }
        return $on_sale;
    }

    public function add_filter_for_get_price() {
        if (WC()->version < '2.7.0') {
            add_filter('woocommerce_get_regular_price', array($this, 'get_price'), 99, 2); //function to modify product sale price
            add_filter('woocommerce_get_sale_price', array($this, 'get_price'), 99, 2); //function to modify product sale price
            add_filter('woocommerce_get_price', array($this, 'get_price'), 99, 2); //function to modify product price at all level
        } else {
            add_filter('woocommerce_product_get_regular_price', array($this, 'get_price'), 99, 2);
            add_filter('woocommerce_product_get_sale_price', array($this, 'get_price'), 99, 2);
            add_filter('woocommerce_product_get_price', array($this, 'get_price'), 99, 2);
            add_filter('woocommerce_product_variation_get_price', array($this, 'get_price'), 99, 2);
        }
    }

    public function remove_filter_for_get_price() {
        if (WC()->version < '2.7.0') {
            remove_filter('woocommerce_get_regular_price', array($this, 'get_price'), 99, 2); //function to modify product sale price
            remove_filter('woocommerce_get_sale_price', array($this, 'get_price'), 99); //function to modify product sale price
            remove_filter('woocommerce_get_price', array($this, 'get_price'), 99); //function to modify product price at all level
        } else {
            remove_filter('woocommerce_product_get_regular_price', array($this, 'get_price'), 99, 2);
            remove_filter('woocommerce_product_get_sale_price', array($this, 'get_price'), 99);
            remove_filter('woocommerce_product_get_price', array($this, 'get_price'), 99);
            remove_filter('woocommerce_product_variation_get_price', array($this, 'get_price'), 99);
        }
    }

    public function eh_override_tax_display_setting_in($status) {
        if ($this->enable_role_tax) { //to make dependent on enable tax checkbox
            //checks if we have to show price including tax or excluding tax on specific page
            if (is_user_logged_in()) {
                $user_role = $this->current_user_role;
            } else {
                $user_role = 'unregistered_user';
            }
            if (!empty($this->role_tax_option[$user_role]) && !empty($this->role_tax_option[$user_role]['tax_option'])) {
                $no_tax_class = 'excl';
                switch ($this->role_tax_option[$user_role]['tax_option']) {
                    case 'show_price_including_tax': return 'incl';
                        break;
                    case 'show_price_excluding_tax': return $no_tax_class;
                        break;
                    case 'show_price_including_tax_shop': if (is_shop()) {
                            return 'incl';
                        } else {
                            return $no_tax_class;
                        }
                        break;
                    case 'show_price_including_tax_cart_checkout': if (is_cart() || is_checkout()) {
                            return 'incl';
                        } else {
                            return $no_tax_class;
                        }
                        break;
                    case 'show_price_excluding_tax_shop': if (is_shop()) {
                            return $no_tax_class;
                        } else {
                            return 'incl';
                        }
                        break;
                    case 'show_price_excluding_tax_cart_checkout': if (is_cart() || is_checkout()) {
                            return $no_tax_class;
                        } else {
                            return 'incl';
                        }
                        break;
                }
            }
        }
        return $status;
    }

    function product_tax_class($tax_class) {
        if ($this->enable_role_tax) {

            // returns selected tax class
            if (isset($this->role_tax_option[$this->current_user_role]) && isset($this->role_tax_option[$this->current_user_role]['tax_calsses']) && $this->role_tax_option[$this->current_user_role]['tax_calsses'] != 'default') {
                return $this->role_tax_option[$this->current_user_role]['tax_calsses'];
            }
        }
        return $tax_class;
    }

    function get_add_to_cart_placeholder_text() {
        if (is_user_logged_in()) {
            $add_to_cart_text = get_option('eh_pricing_discount_cart_user_role_text');
        } else {
            $add_to_cart_text = get_option('eh_pricing_discount_cart_unregistered_user_text');
        }

        if (!empty($add_to_cart_text)) {
            echo $add_to_cart_text;
        }
    }

    public function product_page_remove_add_to_cart_option() {
        global $product;
        $product_id = $this->get_product_id($product);
        if ($this->get_product_type($product) == 'simple') {
            if (is_user_logged_in()) {
                $remove_settings_cart_roles = get_option('eh_pricing_discount_cart_user_role');
                $remove_product_cart_roles = get_post_meta($product_id, 'eh_pricing_adjustment_product_addtocart_user_role', true);
                if (is_array($remove_product_cart_roles) && in_array($this->current_user_role, $remove_product_cart_roles)) {
                    remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30);
                } elseif (is_array($remove_settings_cart_roles) && in_array($this->current_user_role, $remove_settings_cart_roles)) {
                    remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30);
                    $this->get_add_to_cart_placeholder_text();
                }
            } else {
                if ('yes' == (get_post_meta($product_id, 'product_adjustment_hide_addtocart_unregistered', true))) {
                    remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30);
                } elseif ('yes' == get_option('eh_pricing_discount_cart_unregistered_user')) {
                    remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30);
                    $this->get_add_to_cart_placeholder_text();
                }
            }
        }
    }

    function woocommerce_before_add_to_cart_button() {
        global $product;
        if ($this->get_product_type($product) != 'grouped') {
            ob_start();
        }
    }

    function woocommerce_before_add_to_cart_button_grouped_product() {
        global $product;
        if ($this->get_product_type($product) == 'grouped') {
            ob_start();
        }
    }

    function woocommerce_after_add_to_cart_button() {
        ob_end_clean();
        $this->get_add_to_cart_placeholder_text();
    }

    function woocommerce_after_add_to_cart_button_no_placeholder() {
        ob_end_clean();
    }

    public function init_fields() {
        $this->role_price_adjustment = get_option('eh_pricing_discount_price_adjustment_options', array());
        $this->current_user_role = $this->get_priority_user_role(wp_get_current_user()->roles, $this->role_price_adjustment);
        $this->enable_role_tax = get_option('eh_pricing_discount_enable_tax_options') == 'yes' ? true : false;
        $this->role_tax_option = get_option('eh_pricing_discount_price_tax_options', array());
        $this->tax_user_role = $this->get_priority_user_role(wp_get_current_user()->roles, $this->role_tax_option);
        $this->price_suffix_option = get_option('eh_pricing_discount_enable_price_suffix', 'none');
        $this->general_price_suffix = get_option('eh_pricing_discount_price_general_price_suffix', '');
        $this->role_price_suffix = get_option('eh_pricing_discount_role_price_suffix', array());
        $this->suffix_user_role = $this->get_priority_user_role(wp_get_current_user()->roles, $this->role_price_suffix);
        $this->price_suffix_user_role = $this->suffix_user_role != '' ? $this->suffix_user_role : 'unregistered_user';
        $this->replace_add_to_cart = get_option('eh_pricing_discount_replace_cart_unregistered_user') == 'yes' ? true : false;
        $this->replace_add_to_cart_button_text_product = get_option('eh_pricing_discount_replace_cart_unregistered_user_text_product', '');
        $this->replace_add_to_cart_button_text_shop = get_option('eh_pricing_discount_replace_cart_unregistered_user_text_shop', '');
        $this->replace_add_to_cart_button_url_shop = get_option('eh_pricing_discount_replace_cart_unregistered_user_url_shop', '');
        $this->replace_add_to_cart_user_role = get_option('eh_pricing_discount_replace_cart_user_role', array());
        $this->replace_add_to_cart_user_role_button_text_product = get_option('eh_pricing_discount_replace_cart_user_role_text_product', '');
        $this->replace_add_to_cart_user_role_button_text_shop = get_option('eh_pricing_discount_replace_cart_user_role_text_shop', '');
        $this->replace_add_to_cart_user_role_url_shop = get_option('eh_pricing_discount_replace_cart_user_role_url_shop', '');
        $this->individual_product_adjustment_roles = get_option('eh_pricing_discount_product_price_user_role', array());
        $separator = stripslashes(get_option('woocommerce_price_decimal_sep'));
        $this->decimal_separator = $separator ? $separator : '.';
    }

    //function to determine the user role to use in case of multiple user roles for one user
    public function get_priority_user_role($user_roles, $role_priority_list) {
        if (is_user_logged_in()) {
            if (!empty($role_priority_list)) {
                foreach ($role_priority_list as $id => $value) {
                    if (in_array($id, $user_roles)) {
                        return $id;
                    }
                }
            } else {
                return $user_roles[0];
            }
        } else {
            return 'unregistered_user';
        }
    }

    //function to replace add to cart with another url for user role and unregistered user 
    public function add_to_cart_text_url_replace($link, $product) {
        $temp_data = $this->get_product_type($product);
        $cart_text_content = $link;
        if ($temp_data === 'simple') {
            if ((is_user_logged_in())) {
                if (is_array($this->replace_add_to_cart_user_role) && in_array($this->current_user_role, $this->replace_add_to_cart_user_role) && $this->replace_add_to_cart_user_role_button_text_shop != '') {
                    if (empty($this->replace_add_to_cart_user_role_url_shop)) {
                        $cart_text_content = str_replace('Add to cart', $this->replace_add_to_cart_user_role_button_text_shop, $cart_text_content);
                        $cart_text_content = str_replace('Select options', $this->replace_add_to_cart_user_role_button_text_shop, $cart_text_content);
                        $cart_text_content = str_replace('View products', $this->replace_add_to_cart_user_role_button_text_shop, $cart_text_content);
                    } else {
                        $secure = strpos('https://', $this->replace_add_to_cart_user_role_url_shop);
                        $this->replace_add_to_cart_user_role_url_shop = str_replace('https://', '', $this->replace_add_to_cart_user_role_url_shop);
                        $this->replace_add_to_cart_user_role_url_shop = str_replace('http://', '', $this->replace_add_to_cart_user_role_url_shop);
                        $suff = ($secure === false) ? 'http://' : 'https://';
                        $cart_text_content = '<a href="' . $suff . $this->replace_add_to_cart_user_role_url_shop . '" class="button alt">' . $this->replace_add_to_cart_user_role_button_text_shop . '</a>';
                    }
                }
            }
            if (!is_user_logged_in()) {
                if ($this->replace_add_to_cart && $this->replace_add_to_cart_button_text_shop != '') {
                    if (empty($this->replace_add_to_cart_button_url_shop)) {
                        $cart_text_content = str_replace('Add to cart', $this->replace_add_to_cart_button_text_shop, $cart_text_content);
                        $cart_text_content = str_replace('Select options', $this->replace_add_to_cart_button_text_shop, $cart_text_content);
                        $cart_text_content = str_replace('View products', $this->replace_add_to_cart_button_text_shop, $cart_text_content);
                    } else {
                        $secure = strpos('https://', $this->replace_add_to_cart_button_url_shop);
                        $this->replace_add_to_cart_button_url_shop = str_replace('https://', '', $this->replace_add_to_cart_button_url_shop);
                        $this->replace_add_to_cart_button_url_shop = str_replace('http://', '', $this->replace_add_to_cart_button_url_shop);
                        $suff = ($secure === false) ? 'http://' : 'https://';
                        $cart_text_content = '<a href="' . $suff . $this->replace_add_to_cart_button_url_shop . '" class="button alt">' . $this->replace_add_to_cart_button_text_shop . '</a>';
                    }
                }
            }
        }
        return $cart_text_content;
    }

    //function to edit add to cart text of product page with placeholder text when replace add to cart button is selected

    public function add_to_cart_text_content_replace($text, $product) {
        $cart_text_content = $text;
        if ($this->get_product_type($product) == 'variable' || $this->get_product_type($product) == 'grouped') {
            return $cart_text_content;
        }
        if ((is_user_logged_in())) {
            if (is_array($this->replace_add_to_cart_user_role) && in_array($this->current_user_role, $this->replace_add_to_cart_user_role) && $this->replace_add_to_cart_user_role_button_text_product != '') {
                $cart_text_content = $this->replace_add_to_cart_user_role_button_text_product;
            }
        }
        if (!is_user_logged_in()) {
            if ($this->replace_add_to_cart && $this->replace_add_to_cart_button_text_product != '') {
                $cart_text_content = $this->replace_add_to_cart_button_text_product;
            }
        }
        return $cart_text_content;
    }

    //to get category ids for a product
    public function get_product_category_using_id($prod_id) {
        $terms = get_the_terms($prod_id, 'product_cat');
        if ($terms) {
            $cats_ids_array = array();
            foreach ($terms as $key => $term) {
                array_push($cats_ids_array, $term->term_id);
                $term2 = $term;

                if (!in_array($term2->parent, $cats_ids_array)) {
                    while ($term2->parent > 0) {
                        array_push($cats_ids_array, $term2->parent);
                        $term2 = get_term_by("id", $term2->parent, "product_cat");
                    }
                }
            }
            return $cats_ids_array;
        }
        return array();
    }

    public function get_price($price = '', $product = null) {
        if (empty($price)) {
            return $price;
        }
        
        //If decimal seperator is ',' and thousand seperator is '.'
        $dec_seperator = wc_get_price_decimal_separator();
        
        
        $pid = $this->get_product_id($product);
        $temp_data = $this->get_product_type($product);
       
        if( apply_filters('xa_pbu_skip_product',false,$pid) != false ) {
            //Role Based Price (individual product page price change)
            $enforce_button_check_for_product = get_post_meta($pid, 'product_based_price_adjustment', true);
            $product_price_adjustment = get_post_meta($pid, 'product_price_adjustment', true);
            if ( !($enforce_button_check_for_product == 'yes' && isset($product_price_adjustment[$this->current_user_role]) && isset($product_price_adjustment[$this->current_user_role]['role_price']) && $product_price_adjustment[$this->current_user_role]['role_price'] == 'on' )) {
                $product_user_price = get_post_meta($pid, 'product_role_based_price');
                if ( is_array($product_user_price) && isset($product_user_price[0]) && !empty($product_user_price[0]) ) {
                    $product_user_price = $product_user_price[0];
                }
                if ( !empty($product_user_price) && is_array($this->individual_product_adjustment_roles) && in_array($this->current_user_role, $this->individual_product_adjustment_roles) ) {
                    if ( isset($product_user_price[$this->current_user_role]) ) {
                        $product_user_price_value = $product_user_price[$this->current_user_role]['role_price'];
                        $product_user_price_value = $this->eh_cwrp_fix_price_decimal_format( $dec_seperator, $product_user_price_value); 
                        if (is_numeric($product_user_price_value)) {
                            $price = $product_user_price_value;
                        }
                    }
                }
                return $price;
            }
        }
        
        
        if ($this->get_product_type($product) == 'variable' || $this->get_product_type($product) == 'variation' || $this->get_product_type($product) == 'grouped') {
            remove_filter('pre_option_woocommerce_tax_display_shop', array($this, 'eh_override_tax_display_setting_in'));
            remove_filter('pre_option_woocommerce_tax_display_cart', array($this, 'eh_override_tax_display_setting_in'));
            remove_filter('pre_option_woocommerce_tax_display_checkout', array($this, 'eh_override_tax_display_setting_in'));
            return $price;
        }
        if(doing_filter('woocommerce_get_cart_item_from_session')){
            return $price;
        }
        if ($this->is_hide_price($product)) {
            if ($this->is_price_hidden_in_product_meta($product)) {
                $price = '';
            } else {
                $price = '';
            }
            return $price;
        }
        add_filter('pre_option_woocommerce_tax_display_shop', array($this, 'eh_override_tax_display_setting_in'));
        add_filter('pre_option_woocommerce_tax_display_cart', array($this, 'eh_override_tax_display_setting_in'));
        add_filter('pre_option_woocommerce_tax_display_checkout', array($this, 'eh_override_tax_display_setting_in'));

        $this->remove_filter_for_get_price();
        $pid = $this->get_product_id($product);
        $temp_data = $this->get_product_type($product);

        if (doing_filter('woocommerce_product_get_regular_price')) {
            $this->add_filter_for_get_price();
            return $price;
        }

        $adjustment_value = 0;
        if (is_array($this->individual_product_adjustment_roles) && in_array($this->current_user_role, $this->individual_product_adjustment_roles)) {
            //Role Based Price (individual product page price change)
            $product_user_price = get_post_meta($pid, 'product_role_based_price');
            if (is_array($product_user_price) && isset($product_user_price[0]) && !empty($product_user_price[0])) {
                $product_user_price = $product_user_price[0];
            }
            if (!empty($product_user_price)) {
                if (isset($product_user_price[$this->current_user_role])) {
                    $product_user_price_value = $product_user_price[$this->current_user_role]['role_price'];
                    $product_user_price_value = $this->eh_cwrp_fix_price_decimal_format( $dec_seperator, $product_user_price_value); 
                    if (is_numeric($product_user_price_value)) {
                        $price = $product_user_price_value;
                    }
                }
            }
            //individual product page price adjustment (discount/markup from settings page))
            $enforce_button_check_for_product = get_post_meta($pid, 'product_based_price_adjustment', true);
            $product_price_adjustment = get_post_meta($pid, 'product_price_adjustment', true);

            if ($enforce_button_check_for_product == 'yes' && isset($product_price_adjustment[$this->current_user_role]) && isset($product_price_adjustment[$this->current_user_role]['role_price']) && $product_price_adjustment[$this->current_user_role]['role_price'] == 'on') {

                $current_user_product_rule = $product_price_adjustment[$this->current_user_role];
                $current_user_product_rule['adjustment_price'] = $this->eh_cwrp_fix_price_decimal_format( $dec_seperator, $current_user_product_rule['adjustment_price']); 
                if (!empty($current_user_product_rule['adjustment_price']) && is_numeric($current_user_product_rule['adjustment_price'])) {
                    $adjustment_value += (float) $current_user_product_rule['adjustment_price'];
                }
                $current_user_product_rule['adjustment_percent'] = $this->eh_cwrp_fix_price_decimal_format( $dec_seperator, $current_user_product_rule['adjustment_percent']); 
                if (!empty($current_user_product_rule['adjustment_percent']) && is_numeric($current_user_product_rule['adjustment_percent'])) {
                    $adjustment_value += $price * ((float) $current_user_product_rule['adjustment_percent']) / 100;
                }
                //discount/markup ajustment to $price
                $price += $adjustment_value;
                $this->add_filter_for_get_price();
                return $price;
            }
        }
        //common price adjustment 
        if (WC()->version < '2.7.0') {
            $temp_post_id = $product->post->ID;
        } else {
            $temp_post_id = $product->get_id();
        }
        $prdct_id = $this->get_product_category_using_id($temp_post_id);

        $common_price_adjustment_table = get_option('eh_pricing_discount_price_adjustment_options', array());
        if (isset($common_price_adjustment_table[$this->current_user_role]) && isset($common_price_adjustment_table[$this->current_user_role]['role_price']) && $common_price_adjustment_table[$this->current_user_role]['role_price'] == 'on') {
            $current_user_product_rule = $common_price_adjustment_table[$this->current_user_role];
            $current_user_product_rule['adjustment_price'] = $this->eh_cwrp_fix_price_decimal_format( $dec_seperator, $current_user_product_rule['adjustment_price']); 
            if (!empty($current_user_product_rule['adjustment_price']) && is_numeric($current_user_product_rule['adjustment_price'])) {
                if (isset($this->role_price_adjustment[$this->current_user_role]['category'])) {
                    $cat_display = $this->role_price_adjustment[$this->current_user_role]['category'];
                    if ($temp_data != 'grouped')
                        $result_chk = array_intersect($prdct_id, $cat_display);
                    if (empty($result_chk)) {
                        $adjustment_value = 0;
                    } else {
                        $adjustment_value += (float) $current_user_product_rule['adjustment_price'];
                    }
                } else {
                    $adjustment_value += (float) $current_user_product_rule['adjustment_price'];
                }
            }
            $current_user_product_rule['adjustment_percent'] = $this->eh_cwrp_fix_price_decimal_format( $dec_seperator, $current_user_product_rule['adjustment_percent']); 
            if (!empty($current_user_product_rule['adjustment_percent']) && is_numeric($current_user_product_rule['adjustment_percent'])) {
                if (isset($this->role_price_adjustment[$this->current_user_role]['category'])) {
                    $cat_display = $this->role_price_adjustment[$this->current_user_role]['category'];
                    if ($temp_data != 'grouped')
                        $result_chk = array_intersect($prdct_id, $cat_display);
                    if (empty($result_chk)) {
                        $adjustment_value = 0;
                    } else {
                        $adjustment_value += $price * ((float) $current_user_product_rule['adjustment_percent']) / 100;
                    }
                } else {
                    $adjustment_value += $price * ((float) $current_user_product_rule['adjustment_percent']) / 100;
                }
            }
        }
        //discount/markup ajustment to $price
        $price += $adjustment_value;

        $this->add_filter_for_get_price();

        return $price;
    }
    
    private function eh_cwrp_fix_price_decimal_format( $dec_seperator, $price_to_update ){
        if ( $dec_seperator == ',' ) {
            if ( strpos($price_to_update, ',') ) {
                $updated_price = str_replace(',', '.', $price_to_update);
            }
            else {
                $updated_price = str_replace('.', ',', $price_to_update);
            }
        }
        else {
            $updated_price = $price_to_update;
        }
        
        return $updated_price;
    }

    public function get_price_html($price = '', $product) {
        if ($this->get_product_type($product) == 'simple') {
            if ($product->is_on_sale() && $this->is_hide_regular_price($product) === false) {
                $price = wc_format_sale_price(wc_get_price_to_display($product, array('price' => $product->get_regular_price())), wc_get_price_to_display($product)) . $product->get_price_suffix();
            } else {
                $price = wc_price(wc_get_price_to_display($product)) . $product->get_price_suffix();
            }
            if ($this->is_hide_price($product)) {
                if ($this->is_price_hidden_in_product_meta($product)) {
                    $price = '';
                } else {
                    $price = $this->get_placeholder_text($product, $price);
                }
            }
            if ($this->is_catalog_mode_on($product)) {
                $price = '';
            }
            return apply_filters('eh_pricing_adjustment_modfiy_price', $this->eh_pricing_add_price_suffix($price, $product), $this->current_user_role);
        } elseif ($this->get_product_type($product) == 'grouped') {
            $tax_display_mode = get_option('woocommerce_tax_display_shop');
            $child_prices = array();

            foreach ($product->get_children() as $child_id) {
                $child = wc_get_product($child_id);

                if ('' !== $child->get_price() && !$this->is_hide_price($child)) {
                    $child_prices[] = 'incl' === $tax_display_mode ? wc_get_price_including_tax($child) : wc_get_price_excluding_tax($child);
                }
            }

            if (!empty($child_prices)) {
                $min_price = min($child_prices);
                $max_price = max($child_prices);
            } else {
                $min_price = '';
                $max_price = '';
            }

            if ('' !== $min_price) {
                $price = $min_price !== $max_price ? sprintf(_x('%1$s&ndash;%2$s', 'Price range: from-to', 'woocommerce'), wc_price($min_price), wc_price($max_price)) : wc_price($min_price);
                $is_free = ( 0 == $min_price && 0 == $max_price );

                if ($is_free) {
                    $price = __('Free!', 'woocommerce');
                }
            } else {
                $price = '';
            }
            return apply_filters('eh_pricing_adjustment_modfiy_price', $price . $product->get_price_suffix());
        } else {
            return apply_filters('eh_pricing_adjustment_modfiy_price', $price);
        }
    }

    function is_hide_regular_price($product) {
        $hide = false;
        if (!is_user_logged_in()) {
            $hide = get_option('eh_pricing_discount_hide_regular_price_unregistered', 'no') == 'yes';
        } else {
            $remove_settings_regular_price_roles = get_option('eh_pricing_discount_regular_price_user_role', array());
            if (is_array($remove_settings_regular_price_roles) && in_array($this->current_user_role, $remove_settings_regular_price_roles)) {
                $hide = true;
            }
        }
        return $hide;
    }

    function is_hide_price($product) {
        $hide = false;
        $product_id = $this->get_product_id($product);
        $temp_data = $this->get_product_type($product);
        if ($temp_data == 'variable' || $temp_data == 'grouped' || $temp_data == 'variation') {
            return $hide;
        }
        if (is_user_logged_in()) {
            $remove_settings_price_roles = get_option('eh_pricing_discount_price_user_role', array());
            $remove_product_price_roles = get_post_meta($product_id, 'eh_pricing_adjustment_product_price_user_role', true);
            if (is_array($remove_settings_price_roles) && in_array($this->current_user_role, $remove_settings_price_roles)) {
                $hide = true;
            }
            if (is_array($remove_product_price_roles) && in_array($this->current_user_role, $remove_product_price_roles)) {
                $hide = true;
            }
        } else {
            $remove_product_price_roles = get_post_meta($product_id, 'product_adjustment_hide_price_unregistered', true);
            if (get_option('eh_pricing_discount_price_unregistered_user') == 'yes' || $remove_product_price_roles == 'yes') {
                $hide = true;
            }
        }
        return $hide;
    }

    function is_catalog_mode_on($product) {
        $hide = get_option('eh_pricing_discount_catalog_mode_on', 'no') == 'yes';
        if ($hide == true && $this->get_product_type($product) == 'simple') {
            return true;
        } else {
            return false;
        }
    }

    public function is_product_purchasable($is_purchasable, $product) {
        if ($this->is_hide_price($product) === true || $this->is_catalog_mode_on($product) === true) {
            return false;
        } else {
            return true;
        }
    }

    function is_price_hidden_in_product_meta($product) {
        $product_id = $this->get_product_id($product);

        if ($this->get_product_type($product) == 'variation') {
//            if (WC()->version < '2.7.0') {
//                $product_id = $product->post->ID;
//            } else {
//                $product_id = $product->get_id();
//            }
            $product_id = $this->get_product_parent_id($product);
        }
        if (is_user_logged_in()) {
            $remove_product_price_roles = get_post_meta($product_id, 'eh_pricing_adjustment_product_price_user_role', true);
            if (is_array($remove_product_price_roles) && in_array($this->current_user_role, $remove_product_price_roles)) {
                return true;
            } else {
                return false;
            }
        } else {
            $remove_product_price_roles = get_post_meta($product_id, 'product_adjustment_hide_price_unregistered', true);
            if ($remove_product_price_roles == 'yes') {
                return true;
            } else {
                return false;
            }
        }
    }

    function get_placeholder_text($product, $price) {
        $placeholder = '';
        if ($this->is_hide_price($product) == true) {
            if (is_user_logged_in()) {
                $placeholder = get_option('eh_pricing_discount_price_user_role_text');
            } else {
                $placeholder = get_option('eh_pricing_discount_price_unregistered_user_text');
            }
            return $placeholder;
        } else {
            return $price;
        }
    }

    function get_product_type($product) {
        if (empty($product)) {
            return 'not a valid object';
        }
        if (WC()->version < '2.7.0') {
            $product_type = $product->product_type;
        } else {
            $product_type = $product->get_type();
        }
        return $product_type;
    }

    function get_product_id($product) {
        if (empty($product)) {
            return 'not a valid object';
        }
        if (WC()->version < '2.7.0') {
            $product_id = $product->post->id;
        } else {
            $product_id = $product->get_id();
        }
        return $product_id;
    }

    function get_product_parent_id($product) {
        if (empty($product)) {
            return 'not a valid object';
        }
        if (WC()->version < '2.7.0') {
            $product_parent_id = $product->parent->id;
        } else {
            $product_parent_id = $product->get_parent_id();
        }
        return $product_parent_id;
    }

    //function to add price suffix
    public function eh_pricing_add_price_suffix($price, $product) {
        $price_suffix;
        if ($this->price_suffix_option == 'general') {
            $price_suffix = ' <small class="woocommerce-price-suffix">' . $this->general_price_suffix . '</small>';
        } else if ($this->price_suffix_option == 'role_specific') {
            $user_role;
            if (is_user_logged_in()) {
                $user_role = $this->price_suffix_user_role;
            } else {
                $user_role = 'unregistered_user';
            }
            if (is_array($this->role_price_suffix) && key_exists($user_role, $this->role_price_suffix) && isset($this->role_price_suffix[$user_role]['price_suffix']) && $this->role_price_suffix[$user_role]['price_suffix'] != '') {
                $price_suffix = ' <small class="woocommerce-price-suffix">' . $this->role_price_suffix[$user_role]['price_suffix'] . '</small>';
            }
        }
        if (!empty($price_suffix)&& $this->is_hide_price($product) === false) {

            $find = array(
                '{price_including_tax}',
                '{price_excluding_tax}'
            );
            $replace = array(
                wc_price((WC()->version < '2.7.0') ? $product->get_price_including_tax() : wc_get_price_including_tax($product)),
                wc_price((WC()->version < '2.7.0') ? $product->get_price_excluding_tax() : wc_get_price_excluding_tax($product))
            );
            $price_suffix = str_replace($find, $replace, $price_suffix);
            $price .= $price_suffix;
        }
        return $price;
    }

}

new Class_Eh_Price_Discount_Admin();
