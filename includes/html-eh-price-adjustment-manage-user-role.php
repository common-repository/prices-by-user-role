<div class="wrap" style="padding-left: 25px;width: 70%;">
   <div id="content">
	 <input type="hidden" id="pricing_discount_manage_user_roles" name="pricing_discount_manage_user_roles" value="add_user_role">
	 <div id="poststuff">
		<div class="postbox">
		   <h3 class="hndle_add_user_role" style="cursor:pointer;padding-left: 15px;padding-bottom: 15px;border-bottom: solid 1.5px black;color: #5b9dd9;"><?php
			  _e('Add Custom User Role:', 'eh-woocommerce-pricing-discount'); ?></h3>
		   <div class="add_user_role" style="border-bottom: solid 1.5px black;">
			  <table class="form-table">
				 <tr>
					<th style="padding: 15px;">
					   <label for="eh_woocommerce_pricing_discount_user_role_name"><b><?php
						  _e('User Role', 'eh-woocommerce-pricing-discount'); ?></b></label>
					</th>
					<td>
					   <input type="text" name="eh_woocommerce_pricing_discount_user_role_name" class="regular-text" value= ><br />
					   <span class="description"><?php
						  _e('Enter the name of the user role you want to create', 'eh-woocommerce-pricing-discount');
						  ?></span>
					</td>
				 </tr>
			  </table>
		   </div>
		   <h3 class="hndle_delete_user_role" style="cursor:pointer;padding-left: 15px;color: #5b9dd9;"><?php
			  _e('Remove User Role:', 'eh-woocommerce-pricing-discount'); ?></h3>
		   <div class="delete_user_role" style="border-top: solid 1.5px black;">
			  <table class="form-table">
				<?php 
					global $wp_roles;
					$user_roles = $wp_roles->role_names;
					foreach($user_roles as $id=>$name) { 
				?>
				<tr>
					<td>
					</td>
					<td>
					<label><input type="checkbox" name="pricing_discount_remove_user_role[<?php _e( $id, 'eh-woocommerce-pricing-discount');?>]" ><?php echo $name; ?></label>
					</td>
				</tr>
				<?php } ?>
				<br/><span class="description" style="padding: 15px;"><?php
					_e('Select the user role you want to delete.', 'eh-woocommerce-pricing-discount');
				?></span>
			</table>
		   </div>
		</div>
	 </div>
   </div>
</div>
<script>
jQuery(document).ready(function(){
	jQuery('.hndle_add_user_role').click(function(){
		eh_pricing_discount_manage_role('add_user_role');
	});
	jQuery('.hndle_delete_user_role').click(function(){
		eh_pricing_discount_manage_role('delete_user_role');
	});
	eh_pricing_discount_manage_role('add_user_role');
});
function eh_pricing_discount_manage_role(manage_role){
	switch(manage_role){
		case 'add_user_role':
			jQuery('.add_user_role').show();
			jQuery('.delete_user_role').hide();
			jQuery('#pricing_discount_manage_user_roles').val('add_user_role');
			jQuery("input[name='save']").val('Add User Role');
			break;
		case 'delete_user_role':
			jQuery('.add_user_role').hide();
			jQuery('.delete_user_role').show();
			jQuery('#pricing_discount_manage_user_roles').val('remove_user_role');
			jQuery("input[name='save']").val('Delete User Role');
			break;
	}
}
</script>