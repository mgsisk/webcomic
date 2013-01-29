<?php
/** Contains the WebcomicUsers class.
 * 
 * @package Webcomic
 */

/** Handle user-related tasks.
 * 
 * @package Webcomic
 */
class WebcomicUsers extends Webcomic {
	/** Register hooks.
	 * 
	 * @uses WebcomicUsers::profile_update()
	 * @uses WebcomicUsers::user_profile()
	 */
	public function __construct() {
		add_action( 'profile_update', array( $this, 'profile_update' ), 10, 2 );
		add_action( 'show_user_profile', array( $this, 'user_profile' ), 10, 1 );
		add_action( 'edit_user_profile', array( $this, 'user_profile' ), 10, 1 );
	}
	
	/** Save user metadata.
	 * 
	 * @param integer $id Current user ID.
	 * @param array $data Current user data array.
	 * @hook profile_update
	 */
	public function profile_update( $id, $data ) {
		if ( isset( $_POST[ 'webcomic_birthday' ], $_POST[ 'webcomic_meta_user' ] ) and wp_verify_nonce( $_POST[ 'webcomic_meta_user' ], 'webcomic_meta_user' ) ) {
			update_user_meta( $id, 'webcomic_birthday', $_POST[ 'webcomic_birthday' ] );
		}
	}
	
	/** Render meta inputs for user profile form.
	 * 
	 * @param object $user Current user object.
	 * @hook show_user_profile
	 * @hook edit_user_profile
	 */
	public function user_profile( $user ) {
		$birthday = get_user_meta( $user->ID, 'webcomic_birthday', true );
		
		wp_nonce_field( 'webcomic_meta_user', 'webcomic_meta_user' );
		?>
		<h3><?php _e( 'Webcomic', 'webcomic' ); ?></h3>
		<table class="form-table">
			<tr>
				<th scope="row"><label for="webcomic_birthday"><?php _e( 'Birthday', 'webcomic' ); ?></label></th>
				<td>
					<input type="date" name="webcomic_birthday" id="webcomic_birthday" value="<?php echo $birthday; ?>">
					<span class="description"><?php _e( 'Your birthday is used for age verification.', 'webcomic' ); ?></span>
				</td>
			</tr>
		</table>
		<?php
	}
}