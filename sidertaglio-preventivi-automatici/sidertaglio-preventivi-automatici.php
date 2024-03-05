<?php
/**
 * Plugin Name: WooCrypt - Cryptocurrency gateway for WooCommerce
 * Plugin URI: http://woocrypt.com
 * Description: Extends WooCommerce with a crypto gateway.
 * Version: 1.0.0
 * Author: Markven Srl
 * Author URI: http://markeven.it/
 * Developer: Marco Albertini
 * Text Domain: woocrypt-payment-gateway
 * WC requires at least: 8.1.0
 * WC tested up to: 8.1.1
 * Woo:
 * License: GNU General Public License v3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

require_once ABSPATH . 'wp-load.php';
define( 'SPA_VERSION', '1.0' );
define( 'SPA_FILE', __FILE__ );
define( 'SPA_PATH', plugin_dir_path( SPA_FILE ) );
define( 'SPA_URL', plugin_dir_url( SPA_FILE ) );
/**
 * Renders the form for api settings.
 *
 * @since 1.0.0
 * @return void
 */
function sidertaglio_settings_form() {
	wp_enqueue_script( 'jquery-tiptip' );
	wp_enqueue_style( 'spa_custom_css', SPA_URL . 'assets/css/sidertaglio-preventivi-automatici.css', array(), SPA_VERSION, null, 'all' );
	wp_enqueue_script( 'spa_custom_js', SPA_URL . 'assets/js/sidertaglio-preventivi-automatici.js', array( 'jquery' ), SPA_VERSION, true );
	?>
	<form method="post" action="options.php">
		<?php settings_fields( 'sidertaglio_options_group' ); ?>
		<h3>API Key per JWT</h3>
		<input type="text" name="sidertaglio_jwt_token" value="<?php echo esc_attr( get_option( 'sidertaglio_jwt_token' ) ); ?>" />
		<?php submit_button(); ?>
	</form>
	<?php
	$custom_tokens = get_all_custom_tokens();
	?>
	<style>
		#tiptip_holder {
			display: none;
			z-index: 8675309;
			position: absolute;
			top: 0;
			pointer-events: none;
			left: 0
		}

		#tiptip_holder.tip_top {
			padding-bottom: 5px
		}

		#tiptip_holder.tip_top #tiptip_arrow_inner {
			margin-top: -7px;
			margin-left: -6px;
			border-top-color: #333
		}

		#tiptip_holder.tip_bottom {
			padding-top: 5px
		}

		#tiptip_holder.tip_bottom #tiptip_arrow_inner {
			margin-top: -5px;
			margin-left: -6px;
			border-bottom-color: #333
		}

		#tiptip_holder.tip_right {
			padding-left: 5px
		}

		#tiptip_holder.tip_right #tiptip_arrow_inner {
			margin-top: -6px;
			margin-left: -5px;
			border-right-color: #333
		}

		#tiptip_holder.tip_left {
			padding-right: 5px
		}

		#tiptip_holder.tip_left #tiptip_arrow_inner {
			margin-top: -6px;
			margin-left: -7px;
			border-left-color: #333
		}

		#tiptip_content,.chart-tooltip,.wc_error_tip {
			color: #fff;
			font-size: .8em;
			max-width: 150px;
			background: #333;
			text-align: center;
			border-radius: 3px;
			padding: .618em 1em;
			box-shadow: 0 1px 3px rgba(0,0,0,.2)
		}

		#tiptip_content code,.chart-tooltip code,.wc_error_tip code {
			padding: 1px;
			background: #888
		}

		#tiptip_arrow,#tiptip_arrow_inner {
			position: absolute;
			border-color: transparent;
			border-style: solid;
			border-width: 6px;
			height: 0;
			width: 0
		}
	</style>
		<!-- HTML Code For Form -->
	<div class="woocommerce_form_wrapper">
	
		<h3>My custom Cryptocurrencies</h3>
		<p>Please note that in order to be able to retrive the price of your custom cryptocurrency it must be listed on CoinGecko.com</p>
		<p>Otherwise our payment gateway will not be able to handle the payment.</p>

		<?php
		if ( ! empty( $custom_tokens ) ) {
			foreach ( $custom_tokens as $token ) {
				$chain   = $token['chain'];
				$address = $token['address'];
				$data    = $token['data'];
				$name    = $data['name'];
				$erc20   = 'true' === $data['erc20'] ? true : false;
				$abifile = str_replace( '\\"', '', $data['abifile'] );
				$id      = $chain . '_' . $address;
				?>
					<div class="token-row">
						<div class="handle" id="<?php echo esc_attr( $id ); ?>">
							<ul class="closed-token">
								<li class="li-field-label">
									<strong>
										<span><?php echo esc_html( $name ); ?></span>
									</strong>
								</li>
								<li class="li-field-name"><span><?php echo esc_html( $address ); ?></span></li>
							</ul>
						</div>

						<div class="settings">
							<ul class="dropdownMenu">
									<!-- First DropDown -->
							<li>
								<label for="<?php echo esc_attr( $id . '_address' ); ?>">Address:
										<?php echo wc_help_tip( 'Enter the address where the contract of this coin is located', false ); ?>
								</label>
								<input type="text" class="address" readonly id="<?php echo esc_attr( $id . '_address' ); ?>" value="<?php echo esc_attr( $address ); ?>"/>

								<br/>
								
								<label for="<?php echo esc_attr( $id . '_name' ); ?>">Name of Token:</label>
								<input type="text" class="name" value="<?php echo esc_attr( $name ); ?>" id="<?php echo esc_attr( $id . '_name' ); ?>"/>
					
								<br/>
								

								<label for="<?php echo esc_attr( $id . '_chain' ); ?>">Select which chain is this token on: <?php echo wc_help_tip( 'Select which chain is this token on', false ); ?></label>
								<fieldset>
										<legend class="screen-reader-text"><span>Select which chain is this token on</span></legend>
										<select disabled class="wc-enhanced-select select2-hidden-accessible enhanced chain" name="chain_selector" id="<?php echo esc_attr( $id . '_chain' ); ?>" style="width: 400px;" data-placeholder="Select from our supported chains" tabindex="-1" aria-hidden="true">
											<option 
											<?php
											if ( 'bsc' === $chain ) {
												echo esc_attr( 'selected' );
											}
											?>
											value="bsc">Binance Chain</option>
											<option 
											<?php
											if ( 'eth' === $chain ) {
												echo esc_attr( 'selected' );
											}
											?>
											value="eth">Ethereum Chain</option>
											<option 
											<?php
											if ( 'bscTestnet' === $chain ) {
												echo esc_attr( 'selected' );
											}
											?>
											value="bscTestnet">Binance Chain</option>
											<option 
											<?php
											if ( 'goerli' === $chain ) {
												echo esc_attr( 'selected' );
											}
											?>
											value="goerli">Ethereum Goerli Testnet Chain</option>
										</select>
								</fieldset>

								<br/>
								<div class="demo">
									<!-- begin toggle markup	 -->
									
									<label class="toggle" for="<?php echo esc_attr( $id . '_erc20' ); ?>">
										<input disabled type="checkbox" value="" class="toggle__input erc20" id="<?php echo esc_attr( $id . '_erc20' ); ?>" 
										<?php
										if ( $erc20 ) {
											echo esc_attr( 'checked' );
										}
										?>
										/>
										<span class="toggle-track">
											<span class="toggle-indicator">
												<!-- 	This check mark is optional	 -->
												<span class="checkMark">
													<svg viewBox="0 0 24 24" id="ghq-svg-check" role="presentation" aria-hidden="true">
														<path d="M9.86 18a1 1 0 01-.73-.32l-4.86-5.17a1.001 1.001 0 011.46-1.37l4.12 4.39 8.41-9.2a1 1 0 111.48 1.34l-9.14 10a1 1 0 01-.73.33h-.01z"></path>
													</svg>
												</span>
											</span>
										</span>
										Token standard ERC20
									</label>
							</li>

							<!-- Second DropDown (Only Appears When Checkbox Is Checked)-->
							<li class="secondDropDown abi-dropdown" style="
							<?php
							if ( ! $erc20 ) {
								echo esc_attr( 'display:block' );
							} else {
								echo esc_attr( 'display:none' );
							}
							?>
							">
								<h4><label for="<?php echo esc_attr( $id . '_abi' ); ?>">ABI JSON:</label></h4>
								<textarea class="abi" rows="30" cols="100%" style="width: 100%; outline: none; padding: 10px; border-radius: 5px; margin: 10px 0; border: 1px dotted lightgray;" id="<?php echo esc_attr( $id . '_abi' ); ?>"><?php echo esc_html( $abifile ); ?></textarea>
							</li>

							<!-- Save Button -->
							<li class="saveButtonLi">
								<?php $nonce = wp_create_nonce( 'save_custom_token' ); ?>
								<input type="hidden" class="saveNonce" name="_wpnonce" value="<?php echo esc_attr( $nonce ); ?>" />
								<p class="button-left">
									<input class='saveButton' value="Save Changes" type="button" id="<?php echo esc_attr( $id . '_save' ); ?>">
								</p>
								<?php $nonce = wp_create_nonce( 'delete_custom_token' ); ?>
								<input type="hidden" class="deleteNonce" name="_wpnonce" value="<?php echo esc_attr( $nonce ); ?>" />
								<p class="button-right">
									<input class='deleteButton' value="Delete" type="button"  id="<?php echo esc_attr( $id . '_delete' ); ?>">
								</p>
							</li>

							</ul>
						</div>
					</div>
				<?php
			}
		}
		?>

		<br>
		

		<!-- Dropdown Menu -->
		<ul style="display:none;" class="dropdownMenu" id="dropdownMenu">

		<!-- First DropDown -->
		<li class="firstDropDown">
			<?php $nonce = wp_create_nonce( 'save_custom_token' ); ?>
			<input type="hidden" id="_wpnonce" name="_wpnonce" value="<?php echo esc_attr( $nonce ); ?>" />
			<label for="newAddress">Enter Address:
				<?php echo wc_help_tip( 'Enter the address where the contract of this coin is located', false ); ?>
			</label>
			<input type="text" placeholder="e.g.: 0x123456789ABCDEF..." id="newAddress"/>

			<br/>
			
			<label for="newName">Enter Name of Token:</label>
			<input type="text" placeholder="e.g.: My Token" id="newName"/>
			
			<br/>
			

			<label for="chain_selector">Select which chain is this token on <?php echo wc_help_tip( 'Select which chain is this token on', false ); ?></label>
			<fieldset>
					<legend class="screen-reader-text"><span>Select which chain is this token on</span></legend>
					<select class="wc-enhanced-select select2-hidden-accessible enhanced" name="chain_selector" id="chain_selector" style="width: 400px;" data-placeholder="Select from our supported chains" tabindex="-1" aria-hidden="true">
						<option hidden selected disabled value="">Select from our supported chains</option>
						<option value="bsc">Binance Chain</option>
						<option value="eth">Ethereum Chain</option>
						<option value="bscTestnet">Binance Testnet Chain</option>
						<option value="goerli">Ethereum Goerli Testnet Chain</option>
					</select>
			</fieldset>

			<br/>
			<div class="demo">
				<!-- begin toggle markup	 -->

				<label class="toggle" for="standardErcCheckbox">
					<input type="checkbox" value="" class="toggle__input" id="standardErcCheckbox" checked/>
					<span class="toggle-track">
						<span class="toggle-indicator">
							<!-- 	This check mark is optional	 -->
							<span class="checkMark">
								<svg viewBox="0 0 24 24" id="ghq-svg-check" role="presentation" aria-hidden="true">
									<path d="M9.86 18a1 1 0 01-.73-.32l-4.86-5.17a1.001 1.001 0 011.46-1.37l4.12 4.39 8.41-9.2a1 1 0 111.48 1.34l-9.14 10a1 1 0 01-.73.33h-.01z"></path>
								</svg>
							</span>
						</span>
					</span>
					Token standard ERC20
				</label>
		</li>


		<!-- Second DropDown (Only Appears When Checkbox Is Checked)-->
		<li class="secondDropDown" style="display: none">
			<h4><label for="abiUpload">Paste here the Contract ABI JSON:</label></h4>
			<textarea rows="30" cols="100%" id="abiUpload"></textarea>
		</li>

		<!-- Save Button -->
		<li class="saveButtonLi">
			<button type="submit" id="saveTokenBtn">
			Submit New Coin
			</button>
		</li>

		</ul>

		<!-- Add New Token Button -->
		<p class="button-right">
			<input type="button" value="Add a new cryptocurrency" id="addTokenBtn">
		</p>
	</div>
		
	<?php
}

/**
 * Returns all the customized token created by user.
 *
 * @since 1.0.0
 * @return array
 */
function get_all_macchine() {
	$saved_data = array();
	$token      = get_option( 'sidertaglio_jwt_token' );
	if ( empty( $token ) ) {
		return;
	}
	$url = 'URL_DEL_TUO_SISTEMA_FLASK';

	$args = array(
		'headers' => array( 'Authorization' => 'Bearer ' . $token ),
	);

	$response = wp_remote_get( $url, $args );

	if ( is_wp_error( $response ) ) {
		return;
	}

	$body = wp_remote_retrieve_body( $response );
	foreach ( $option_names as $option_name => $value ) {
		if ( preg_match( '/^woocrypt_(bsc|eth|goerli|bscTestnet)_(0x[0-9a-fA-F]+)$/', $option_name, $matches ) ) {
			$chain   = $matches[1];
			$address = $matches[2];
			$data    = get_option( $option_name );
			array_push(
				$saved_data,
				array(
					'chain'   => $chain,
					'address' => $address,
					'data'    => $data,
				)
			);
		}
	}

	return $saved_data;
}

add_action( 'wp_ajax_get_all_custom_tokens', 'get_all_custom_tokens' );
add_action( 'wp_ajax_nopriv_get_all_custom_tokens', 'get_all_custom_tokens' );
/**
 * Deletes customized token given its id.
 *
 * @since 1.0.0
 * @return void
 */
function delete_custom_token() {
	check_ajax_referer( 'delete_custom_token', 'security' );
	if ( isset( $_POST['chain'] ) && isset( $_POST['address'] ) ) {
		$chain_address = 'woocrypt_' . sanitize_text_field( wp_unslash( $_POST['chain'] ) ) . '_' . sanitize_text_field( wp_unslash( $_POST['address'] ) );
		delete_option( $chain_address );
	}
	wp_die();
}

add_action( 'wp_ajax_delete_custom_token', 'delete_custom_token' );
add_action( 'wp_ajax_nopriv_delete_custom_token', 'delete_custom_token' );
/**
 * Stores a new customized token as option.
 *
 * @since 1.0.0
 * @return void
 */
function save_custom_token() {
	check_ajax_referer( 'save_custom_token', 'security' );
	if ( isset( $_POST['address'] ) && isset( $_POST['name'] ) && isset( $_POST['chain'] ) && isset( $_POST['erc20'] ) && isset( $_POST['abifile'] ) ) {
		$chain_address = 'woocrypt_' . sanitize_text_field( wp_unslash( $_POST['chain'] ) ) . '_' . sanitize_text_field( wp_unslash( $_POST['address'] ) );
		update_option(
			$chain_address,
			array(
				'name'    => sanitize_text_field( wp_unslash( $_POST['name'] ) ),
				'erc20'   => sanitize_text_field( wp_unslash( $_POST['erc20'] ) ),
				'abifile' => sanitize_text_field( wp_unslash( $_POST['abifile'] ) ),
			)
		);
	}
	wp_die();
}

add_action( 'wp_ajax_save_custom_token', 'save_custom_token' );
add_action( 'wp_ajax_nopriv_save_custom_token', 'save_custom_token' );

/**
 * Register settings options.
 *
 * @since 1.0.0
 * @return void
 */
function sidertaglio_register_settings() {
	add_option( 'sidertaglio_jwt_token', '' );
	register_setting( 'sidertaglio_options_group', 'sidertaglio_jwt_token', 'sidertaglio_callback' );
}

add_action( 'admin_init', 'sidertaglio_register_settings' );

add_action( 'admin_menu', 'register_sidertaglio_preventivi_automatici_settings' );
/**
 * Adds menu and submenu for plugin settings.
 *
 * @since 1.0.0
 * @return void
 */
function register_sidertaglio_preventivi_automatici_settings() {
	add_menu_page( esc_html__( 'Preventivi Automatici Settings', 'sidertaglio-preventivi-automatici-settings' ), esc_html__( 'Preventivi Automatici Settings', 'sidertaglio-preventivi-automatici-settings' ), 'manage_options', 'sidertaglio-preventivi-automatici-settings', 'sidertaglio_settings_form', '', 900 );
}
add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'woocrypt_settings_link' );
/**
 * Generates link for menu and submenu plugin's settings page in Plugins admin page.
 *
 * @since 1.0.0
 *
 * @param array $actions Actions.
 * @return array
 */
function woocrypt_settings_link( $actions ) {
	$actions[] = '<a href="' . admin_url( 'admin.php?page=sidertaglio-preventivi-automatici-settings' ) . '">Impostazioni</a>';
	return $actions;
}
?>
