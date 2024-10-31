<div>
    <?php
    global $wp_roles;
    global $post;
    $product = wc_get_product($_REQUEST['post']);
    if (!empty($product)) {
        if (WC()->version < '2.7.0') {
            $product_type = $product->product_type;
        } else {
            $product_type = $product->get_type();
        }
    }
    ?>
</h3>
<?php if ($product_type == 'simple') { ?>
    <!-- Option to hide add to cart for unregistered user-->
    <h4 style="padding-left: 3%;">Unregistered User Options:</h4>
    <div style="padding-left: 3%;height: 60px;">
        <label style="margin-left: 0px;width: 40%;float: left;"><?php _e('Remove Add to Cart', 'eh-woocommerce-pricing-discount'); ?></label>
        <?php $checked = (( get_post_meta($post->ID, 'product_adjustment_hide_addtocart_unregistered', true) ) == 'yes' ) ? true : false; ?>
        <input type="checkbox" style="float: left;margin-left: 0px;" name="product_adjustment_hide_addtocart_unregistered" id="product_adjustment_hide_addtocart_unregistered" <?php checked($checked, true); ?> />
        <label style="float: left;margin-left:5px;"><?php _e('Enable', 'eh-woocommerce-pricing-discount'); ?></label>
        <span class="description" style="width: 60%;float: right;margin-top: 6px;">
            <?php _e('Check to remove Add to Cart.', 'eh-woocommerce-pricing-discount'); ?></span>
    </div>

    <!-- Option to hide price for unregistered user-->
    <div style="padding-left: 3%;height: 60px;">
        <label style="margin-left: 0px;width: 40%;float: left;"><?php _e('Hide Price', 'eh-woocommerce-pricing-discount'); ?></label>
        <?php $checked = (( get_post_meta($post->ID, 'product_adjustment_hide_price_unregistered', true) ) == 'yes' ) ? true : false; ?>
        <input type="checkbox" style="float: left;margin-left: 0px;" name="product_adjustment_hide_price_unregistered" id="product_adjustment_hide_price_unregistered" <?php checked($checked, true); ?> />
        <label style="float: left;margin-left:5px;"><?php _e('Enable', 'eh-woocommerce-pricing-discount'); ?></label>
        <span class="description" style="width: 60%;float: right;margin-top: 6px;">
            <?php _e('Check to hide price.', 'eh-woocommerce-pricing-discount'); ?></span>
    </div>
    <h4 style="padding-left: 3%;">User Role Specific Options:</h4>
    <!-- Option to hide add to cart for user role-->
    <div style="padding-left: 3%;overflow : auto;">
        <label for="eh_pricing_adjustment_product_addtocart_user_role" style="margin-left: 0px;width: 40%;float: left;"><?php _e(' Remove Add to Cart', 'eh-woocommerce-pricing-discount'); ?></label>
        <select class="wc-enhanced-select" name="eh_pricing_adjustment_product_addtocart_user_role[]" id="eh_pricing_adjustment_product_addtocart_user_role" multiple="multiple" style="width: 50%;float: left;">
            <?php
            $hide_addtocart_role = get_post_meta($post->ID, 'eh_pricing_adjustment_product_addtocart_user_role', false);
            $user_roles = $wp_roles->role_names;
            foreach ($user_roles as $id => $name) {
                if (is_array(current($hide_addtocart_role)) && in_array($id, current($hide_addtocart_role))) {
                    echo '<option value="' . $id . '" selected="selected">' . $name . '</option>';
                } else {
                    echo '<option value="' . $id . '">' . $name . '</option>';
                }
            }
            ?>
        </select>
        <span class="description" style="float: right;text-align: left;width: 60%;height: 50px;"><?php _e(' Select the user role for which you want to remove Add to Cart.', 'eh-woocommerce-pricing-discount'); ?></span>
    </div>

    <!-- Option to hide price for user role-->
    <div style="padding-left: 3%;overflow : auto;">
        <label for="eh_pricing_adjustment_product_price_user_role" style="margin-left: 0px;width: 40%;float: left;"><?php _e(' Hide Price', 'eh-woocommerce-pricing-discount'); ?></label>
        <select class="wc-enhanced-select" name="eh_pricing_adjustment_product_price_user_role[]" id="eh_pricing_adjustment_product_price_user_role" multiple="multiple" style="width: 50%;float: left;">
            <?php
            $hide_price_role = get_post_meta($post->ID, 'eh_pricing_adjustment_product_price_user_role', false);
            $user_roles = $wp_roles->role_names;
            foreach ($user_roles as $id => $name) {
                if (is_array(current($hide_price_role)) && in_array($id, current($hide_price_role))) {
                    echo '<option value="' . $id . '" selected="selected">' . $name . '</option>';
                } else {
                    echo '<option value="' . $id . '">' . $name . '</option>';
                }
            }
            ?>
        </select>
        <span class="description" style="float: right;text-align: left;width: 60%;height: 50px;"><?php _e(' Select the user role for which you want to hide price.', 'eh-woocommerce-pricing-discount'); ?></span>
    </div>
    <!-- Option to enforce porduct based adjustment-->
    <?php
    $user_roles = get_option('eh_pricing_discount_product_price_user_role');
    if (is_array($user_roles) && !empty($user_roles)) {
        ?>
        <h4 style="padding-left: 3%;color: green">To set a price for individual user role go to <a href="">general settings</a></h4>


        <div style="padding-left: 3%;height: 50px;">
            <label style="width: 40%; height: 40px; float: left;margin-left: 0px;"><?php _e(' Enforce product price adjustment', 'eh-woocommerce-pricing-discount'); ?></label>
            <?php $checked = ( ( get_post_meta($post->ID, 'product_based_price_adjustment', true) ) == 'yes' ) ? true : false; ?>
            <input type="checkbox" name="product_based_price_adjustment" id="product_based_price_adjustment" <?php checked($checked, true); ?> />
            <label style="width: 57%;float: right;margin-top: 3px;"><?php _e(' Check to enforce indvidual price adjustment', 'eh-woocommerce-pricing-discount'); ?></label>

        </div>

        <!-- Option to determine user role based adjustment-->
        <div style="padding-left: 3%;padding-bottom: 3%;width: 94%;overflow : auto;">
            <p class="description" style="margin-left: 0px;"> <?php _e(' Drag and drop user roles to set priority. If a single user has multiple user roles assigned, the user role with the highest priority will be chosen. To give discount enter a negative value in the price adjustment field. For markup enter a positive value in the price adjustment field.', 'eh-woocommerce-pricing-discount'); ?></p>
            <table class="product_price_adjustment widefat" id="eh_pricing_discount_product_price_adjustment_data">
                <thead>
                    <tr>
                        <th class="sort">&nbsp;</th>
                        <th><?php _e('User Role', 'eh-woocommerce-pricing-discount'); ?></th>
                        <th style="text-align:center;"><?php echo sprintf(__('Price Adjustment (%s)', 'eh-woocommerce-pricing-discount'), get_woocommerce_currency_symbol()); ?></th>
                        <th style="text-align:center;"><?php _e('Price Adjustment (%)', 'eh-woocommerce-pricing-discount'); ?></th>
                        <th style="text-align:center;"><?php _e('Use Role Based Price', 'eh-woocommerce-pricing-discount'); ?></th>
                    </tr>
                </thead>
                <tbody>

                    <?php
                    $this->price_table = array();
                    $i = 0;
                    $product_adjustment_price = get_post_meta($post->ID, 'product_price_adjustment', false);
                    if (empty($product_adjustment_price)) {
                        foreach ($user_roles as $id => $value) {
                            $this->price_table[$i]['id'] = $value;
                            $this->price_table[$i]['name'] = $wp_roles->role_names[$value];
                            $this->price_table[$i]['adjustment_price'] = '';
                            $this->price_table[$i]['adjustment_percent'] = '';
                            $this->price_table[$i]['role_price'] = '';
                            $i++;
                        }
                    } else {
                        if (is_array(current($product_adjustment_price)) || is_object(current($product_adjustment_price))) {
                            foreach (current($product_adjustment_price) as $id => $value) {
                                if (in_array($id, $user_roles)) {
                                    $current_adjustment_price = current($product_adjustment_price);
                                    $this->price_table[$i]['id'] = $id;
                                    $this->price_table[$i]['name'] = $wp_roles->role_names[$id];
                                    $this->price_table[$i]['adjustment_price'] = $current_adjustment_price[$id]['adjustment_price'];
                                    $this->price_table[$i]['adjustment_percent'] = $current_adjustment_price[$id]['adjustment_percent'];
                                    if (key_exists('role_price', $current_adjustment_price[$id])) {
                                        $this->price_table[$i]['role_price'] = $current_adjustment_price[$id]['role_price'];
                                    } else {
                                        $this->price_table[$i]['role_price'] = '';
                                    }
                                    $i++;
                                    unset($user_roles[array_search($id, $user_roles)]);
                                }
                            }
                        }

                        if (!empty($user_roles)) {
                            foreach ($user_roles as $id => $value) {
                                $this->price_table[$i]['id'] = $value;
                                $this->price_table[$i]['name'] = $wp_roles->role_names[$value];
                                $this->price_table[$i]['adjustment_price'] = '';
                                $this->price_table[$i]['adjustment_percent'] = '';
                                $this->price_table[$i]['role_price'] = '';
                                $i++;
                            }
                        }
                    }

                    foreach ($this->price_table as $key => $value) {
                        ?>
                        <tr>
                            <td class="sort">
                                <input type="hidden" class="order" name="product_price_adjustment[<?php echo $this->price_table[$key]['id'] ?>]" value="<?php echo $this->price_table[$key]['id']; ?>" />
                            </td>
                            <td>
                                <label name="product_price_adjustment[<?php echo $this->price_table[$key]['id']; ?>][name]" style="margin-left:0px;"><?php echo isset($this->price_table[$key]['name']) ? $this->price_table[$key]['name'] : ''; ?></label>
                            </td>
                            <td style="text-align: center;">
                                <?php echo get_woocommerce_currency_symbol(); ?><input type="text" name="product_price_adjustment[<?php echo $this->price_table[$key]['id']; ?>][adjustment_price]" placeholder="N/A" value="<?php echo isset($this->price_table[$key]['adjustment_price']) ? $this->price_table[$key]['adjustment_price'] : ''; ?>" style="size:4; float: inherit;" />
                            </td>
                            <td style="text-align: center;">
                                <input type="text" name="product_price_adjustment[<?php echo $this->price_table[$key]['id']; ?>][adjustment_percent]" placeholder="N/A" value="<?php echo isset($this->price_table[$key]['adjustment_percent']) ? $this->price_table[$key]['adjustment_percent'] : ''; ?>" style="size:4; float: inherit;" />%
                            </td>
                            <td style="text-align:center;">
                                <label style="margin-left:0px;">
                                    <?php $checked = (!empty($this->price_table[$key]['role_price']) ) ? true : false; ?>
                                    <input type="checkbox" name="product_price_adjustment[<?php echo $this->price_table[$key]['id']; ?>][role_price]" <?php checked($checked, true); ?> />
                                </label>
                            </td>
                        </tr>
                        <?php
                    }
                    ?>
                </tbody>
            </table>
        </div>
    <?php }
} ?>
</div>
<script type="text/javascript">

    jQuery(window).load(function () {
        // Ordering
        jQuery('.product_price_adjustment tbody').sortable({
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

        function product_price_adjustment_row_indexes() {
            jQuery('.product_price_adjustment tbody tr').each(function (index, el) {
                jQuery('input.order', el).val(parseInt(jQuery(el).index('.product_price_adjustment tr')));
            });
        }
        ;

    });

</script>

<style type="text/css">
    .product_price_adjustment th.sort {
        width: 16px;
        padding: 0 16px;
    }
    .product_price_adjustment td.sort {
        cursor: move;
        width: 16px;
        padding: 0 16px;
        cursor: move;
        background: url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAgAAAAICAYAAADED76LAAAAHUlEQVQYV2O8f//+fwY8gJGgAny6QXKETRgEVgAAXxAVsa5Xr3QAAAAASUVORK5CYII=) no-repeat center;					}
</style>