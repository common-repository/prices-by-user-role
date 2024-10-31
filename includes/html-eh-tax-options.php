<tr valign="top" >
	<td class="forminp" colspan="2" style="padding-left:0px">
	<div id="tax_options_table">
	<h4><?php _e( 'Tax Options Table', 'eh-woocommerce-pricing-discount' ); ?></h4>
            <?php _e('This table handles only tax display option. To modify tax rate go to ', 'eh-woocommerce-pricing-discount');?>
        <a href="admin.php?page=wc-settings&tab=tax">Tax option</a>
            <?php _e('of Woocommerce.', 'eh-woocommerce-pricing-discount');?><br><br>
		<table class="tax_options widefat" id="eh_pricing_discount_price_tax_options">
			<thead>
				<th class="sort">&nbsp;</th>
				<th><?php _e( 'User Role', 'eh-woocommerce-pricing-discount' ); ?></th>
				<th><?php _E( 'Tax Class', 'eh-woocommerce-pricing-discount' ); ?></th>
				<th style="text-align:center;"><?php echo __( 'Tax Type', 'eh-woocommerce-pricing-discount' ); ?></th>
			</thead>
			<tbody>
			
				<?php
					global $wp_roles;
					$tax_options = array(
						'default' => 'Default',
						'show_price_including_tax' => 'Show Price Including Tax',
						'show_price_excluding_tax' => 'Show Price Excluding Tax',
						'show_price_including_tax_shop' => 'Show Price Including Tax in Shop',
						'show_price_including_tax_cart_checkout' => 'Show Price Including Tax in Cart and Checkout',
						'show_price_excluding_tax_shop' => 'Show Price Excluding Tax in Shop',
						'show_price_excluding_tax_cart_checkout' => 'Show Price Excluding Tax in Cart and Checkout'
					);
					$this->tax_table = array();
					$i=0;
					$user_role_tax_options = get_option('eh_pricing_discount_price_tax_options');
					$wordpress_roles = $wp_roles->role_names;
                                        $wordpress_roles['unregistered_user'] = 'Unregistered User';
					if(empty($user_role_tax_options))
					{
						foreach ( $wordpress_roles as $id => $value ) {
							$this->tax_table[$i]['id'] = $id;
							$this->tax_table[$i]['name'] = $value;
							$this->tax_table[$i]['tax_option'] = 'default';
							$this->tax_table[$i]['tax_calsses'] = 'default';
							$i++;
						}
					} else {
						foreach ( $user_role_tax_options as $id => $value ) {
							if(is_array($wordpress_roles) && key_exists($id,$wordpress_roles))
							{
								$this->tax_table[$i]['id'] = $id;
								$this->tax_table[$i]['name'] = $wordpress_roles[$id];
								if(is_array($user_role_tax_options) && key_exists($id,$user_role_tax_options)) {
									$this->tax_table[$i]['tax_option'] = $user_role_tax_options[$id]['tax_option'];
									$this->tax_table[$i]['tax_calsses'] = $user_role_tax_options[$id]['tax_calsses'];
								} else {
									$this->tax_table[$i]['tax_option'] = 'default';
									$this->tax_table[$i]['tax_calsses'] = 'default';

								}
							}
							$i++;
							unset($wordpress_roles[$id]);
						}
						if(!empty($wordpress_roles))
						{
							foreach ( $wordpress_roles as $id => $value ) {
								$this->tax_table[$i]['id'] = $id;
								$this->tax_table[$i]['name'] = $value;
								$this->tax_table[$i]['tax_option'] = 'default';
								$this->tax_table[$i]['tax_calsses'] = 'default';
								$i++;
							}
						}
					}

					$tax_classes       = WC_Tax::get_tax_classes();
    				$classes_names     = array();
    				$classes_names['default'] = __( 'Default', 'eh-woocommerce-pricing-discount' );
    				if ( ! empty( $tax_classes ) ) {
        			foreach ( $tax_classes as $class ) {
            			$classes_names[ sanitize_title( $class ) ] = esc_html( $class );
        			}
    				}

					foreach ( $this->tax_table as $key => $value ) {
						?>
						<tr>
							<td class="sort">
								<input type="hidden" class="order" name="eh_pricing_discount_price_tax_options[<?php echo $this->tax_table[ $key ]['id'] ?>]" value="<?php echo $this->tax_table[ $key ]['id']; ?>" />
							</td>
							<td>
								<label name="eh_pricing_discount_price_tax_options[<?php echo $this->tax_table[ $key ]['id']; ?>][name]" size="35" ><?php echo isset( $this->tax_table[ $key ]['name'] ) ? $this->tax_table[ $key ]['name'] : ''; ?></label>
							</td>
							<td>
								<select style="padding: 0px;" name="eh_pricing_discount_price_tax_options[<?php echo $this->tax_table[ $key ]['id']; ?>][tax_calsses]">
									<?php 
										foreach($classes_names as $id=>$value) {
											if($id == $this->tax_table[$key]['tax_calsses']){
												echo '<option value='.$id.' selected>'.$value.'</option>';
											} else {
												echo '<option value='.$id.'>'.$value.'</option>';
											}
										}
									?>
								</select>
							</td>
							<td style="text-align:center;">
								<select style="padding: 0px;" name="eh_pricing_discount_price_tax_options[<?php echo $this->tax_table[ $key ]['id']; ?>][tax_option]">
									<?php 
										foreach($tax_options as $id=>$value) {
											if($id == $this->tax_table[$key]['tax_option']){
												echo '<option value='.$id.' selected>'.$value.'</option>';
											} else {
												echo '<option value='.$id.'>'.$value.'</option>';
											}
										}
									?>
								</select>
							</td>
						</tr>
						<?php
					}
				?>
			</tbody>
		</table>
		</div>
	</td>
</tr>
<script type="text/javascript">

jQuery(window).load(function(){
	// Ordering
	jQuery('.tax_options tbody').sortable({
		items:'tr',
		cursor:'move',
		axis:'y',
		handle: '.sort',
		scrollSensitivity:40,
		forcePlaceholderSize: true,
		helper: 'clone',
		opacity: 0.65,
		placeholder: 'wc-metabox-sortable-placeholder',
		start:function(event,ui){
			ui.item.css('baclbsround-color','#f6f6f6');
		},
		stop:function(event,ui){
			ui.item.removeAttr('style');
			tax_options_row_indexes();
		}
	});
});
</script>
<style type="text/css">
	.tax_options td {
		vertical-align: middle;
		padding: 4px 7px;
	}
	.tax_options th {
		padding: 9px 7px;
	}
	.tax_options td input {
		margin-right: 4px;
	}
	.tax_options th.sort {
		width: 16px;
		padding: 0 16px;
	}
	.tax_options td.sort {
		cursor: move;
		width: 16px;
		padding: 0 16px;
		cursor: move;
		background: url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAgAAAAICAYAAADED76LAAAAHUlEQVQYV2O8f//+fwY8gJGgAny6QXKETRgEVgAAXxAVsa5Xr3QAAAAASUVORK5CYII=) no-repeat center;					}
</style>