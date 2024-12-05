<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://www.mayankpandya.com/
 * @since      1.0.0
 *
 * @package    MP_Pets_Importer
 * @subpackage MP_Pets_Importer/admin/partials
 */

?>

<!-- This file should primarily consist of HTML with a little bit of PHP. -->

<?php
$admin_ajax = admin_url('admin-ajax.php');

$auth_key = AUTH_KEY;

?>
<div class="wrap">
	<h1><?php _e( 'Petpoint Integration', 'mp-pp-integration' ); ?></h1>
    <div id="msg" style="display:none"></div>
	<table class="form-table">
		<tbody>
			<tr>
				<th scope="row">
					<label for="mp_pp_auth_key">Auth Key</label>
				</th>
				<td>
					<input name="mp_pp_auth_key" type="text" id="mp_pp_auth_key" value="<?php echo $auth_key; ?>" class="regular-text" />
				</td>
			</tr>
			
		</tbody>
	</table>
	<p class="submit">
		<a href="#" class="button button-primary connect-petpoint">Connect PetPoint API</a>
	</p>
</div>
<!-- Connect Vincer API ( Ajax call on button click ) -->
<script type = "text/javascript">
	jQuery(document).ready(function($){
		$(".connect-petpoint").on("click", function(e){
			e.preventDefault();
			var authKey = $("#mp_pp_auth_key").val();
			
			if(!authKey || authKey=="") {
				alert('Auth Key is null');
				return;
			}
			
			$(this).html("Loading...").prop("disabled", true);				
			$.ajax({ 
				method: "POST",
				url: '<?php echo $admin_ajax; ?>',
				dataType : "json",
				data:{
					action:'mp_pp_save_auth_key',
					value: authKey,
				},
				success:function(result){
					if(!result.status) {
						alert('Something is wrong! auth key is not save in database. Please try again.');
						return;
					}
                    jQuery('#msg').html('<span color="#4d7902">Successfully updated Auth key!</span>');
                    jQuery('#msg').show();
				},
				error:function(error){
					console.log(error);
				}
			});
		});
	});
</script>