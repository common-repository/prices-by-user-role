<tr valign="top" >
	<td class="forminp" colspan="2" style="padding-left:0px; vertical-align">
	<div id="price_suffix_table">
	<h4><?php _e( 'Price Suffix Table', 'eh-woocommerce-pricing-discount' ); ?></h4>
		<table class="price_suffix_table widefat" id="eh_pricing_discount_role_price_suffix">
			<thead>
				<th class="sort">&nbsp;</th>
				<th ><?php _e( 'User Role', 'eh-woocommerce-pricing-discount' ); ?></th>
				<th style="text-align:center; width: 350px;"><?php echo __( 'Suffix Text', 'eh-woocommerce-pricing-discount' ); ?></th>
			</thead>
			<tbody>
			
				<?php
					global $wp_roles;
					$this->price_suffix_table = array();
					$i=0;
					$price_suffix = get_option('eh_pricing_discount_role_price_suffix');
					$wordpress_roles = $wp_roles->role_names;
					$wordpress_roles['unregistered_user'] = 'Unregistered User';
					if(empty($price_suffix))
					{
						foreach ( $wordpress_roles as $id => $value ) {
							$this->price_suffix_table[$i]['id'] = $id;
							$this->price_suffix_table[$i]['name'] = $value;
							$this->price_suffix_table[$i]['price_suffix'] = '';
							$i++;
						}
					} else {
						foreach ( $price_suffix as $id => $value ) {
							if(is_array($wordpress_roles) && key_exists($id,$wordpress_roles))
							{
								$this->price_suffix_table[$i]['id'] = $id;
								$this->price_suffix_table[$i]['name'] = $wordpress_roles[$id];
								$this->price_suffix_table[$i]['price_suffix'] = $price_suffix[$id]['price_suffix'];
							}
							$i++;
							unset($wordpress_roles[$id]);
						}
						if(!empty($wordpress_roles))
						{
							foreach ( $wordpress_roles as $id => $value ) {
								$this->price_suffix_table[$i]['id'] = $id;
								$this->price_suffix_table[$i]['name'] = $value;
								$this->price_suffix_table[$i]['price_suffix'] = '';
								$i++;
							}
						}
					}
					foreach ( $this->price_suffix_table as $key => $value ) {
						?>
						<tr>
							<td class="sort">
								<input type="hidden" class="order" name="eh_pricing_discount_role_price_suffix[<?php echo $this->price_suffix_table[ $key ]['id'] ?>]" value="<?php echo $this->price_suffix_table[ $key ]['id']; ?>" />
							</td>
							<td>
								<label name="eh_pricing_discount_role_price_suffix[<?php echo $this->price_suffix_table[ $key ]['id']; ?>][name]" ><?php echo isset( $this->price_suffix_table[ $key ]['name'] ) ? $this->price_suffix_table[ $key ]['name'] : ''; ?></label>
							</td>
							<td style="text-align:center;">
								<input type="text" style="width: 350px;" name="eh_pricing_discount_role_price_suffix[<?php echo $this->price_suffix_table[ $key ]['id']; ?>][price_suffix]" value="<?php echo isset( $this->price_suffix_table[$key]['price_suffix'] ) ? $this->price_suffix_table[$key]['price_suffix'] : ''; ?>" />
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
	jQuery('.price_suffix_table tbody').sortable({
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
			price_suffix_table_row_indexes();
		}
	});
});
</script>
<style type="text/css">
	.price_suffix_table td {
		vertical-align: middle;
		padding: 4px 7px;
	}
	.price_suffix_table th {
		padding: 9px 7px;
	}
	.price_suffix_table td input {
		margin-right: 4px;
	}
	.price_suffix_table th.sort {
		width: 16px;
		padding: 0 16px;
	}
	.price_suffix_table td.sort {
		cursor: move;
		width: 16px;
		padding: 0 16px;
		cursor: move;
		background: url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAgAAAAICAYAAADED76LAAAAHUlEQVQYV2O8f//+fwY8gJGgAny6QXKETRgEVgAAXxAVsa5Xr3QAAAAASUVORK5CYII=) no-repeat center;					}
</style>