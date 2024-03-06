<?php
/**
 * Plugin Name: Sidertaglio - Preventivi Automatici
 * Plugin URI: https://www.sidertagliomodena.it/
 * Description: Genera preventivi automatici per Sidertaglio.
 * Version: 1.0.0
 * Author: Markven Srl
 * Author URI: http://markeven.it/
 * Developer: Marco Albertini
 * Text Domain: sidertaglio-preventivi-automatici
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
 * Renders the form for plugin settings.
 *
 * @since 1.0.0
 * @return void
 */
function sidertaglio_settings_form() {
	wp_enqueue_script( 'jquery-tiptip' );
	wp_enqueue_style( 'spa_custom_css', SPA_URL . 'assets/css/sidertaglio-preventivi-automatici.css', array(), SPA_VERSION, null, 'all' );
	wp_enqueue_script( 'spa_custom_js', SPA_URL . 'assets/js/sidertaglio-preventivi-automatici.js', array( 'jquery' ), SPA_VERSION, true );
	$macchine = get_all_macchine();
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
		
			<h3>Macchine da taglio</h3>

			<?php
			if ( ! empty( $macchine ) ) {
				foreach ( $macchine as $macchina ) {
					$id       = $macchina['id'];
					$data     = $macchina['data'];
					$name     = $data['name'];
					$offset   = $data['name'];
					$spessore = $data['name'];
					?>
						<div class="token-row">
							<div class="handle" id="<?php echo esc_attr( $id ); ?>">
								<ul class="closed-token">
									<li class="li-field-label">
										<strong>
											<span><?php echo esc_html( $name ); ?></span>
										</strong>
									</li>
								</ul>
							</div>

							<div class="settings">
								<ul class="dropdownMenu">
										<!-- First DropDown -->
								<li>
								<label for="<?php echo esc_attr( $id . '_id' ); ?>">ID della macchina:</label>
									<input type="text" class="id" readonly value="<?php echo esc_attr( $id ); ?>" id="<?php echo esc_attr( $id . '_id' ); ?>"/>
						
									<br/>

									<label for="<?php echo esc_attr( $id . '_name' ); ?>">Nome della macchina:</label>
									<input type="text" class="name" value="<?php echo esc_attr( $name ); ?>" id="<?php echo esc_attr( $id . '_name' ); ?>"/>
						
									<br/>

									<label for="<?php echo esc_attr( $id . '_offset' ); ?>">Larghezza del taglio in millimetri:</label>
									<input type="number" class="offset" value="<?php echo esc_attr( $offset ); ?>" id="<?php echo esc_attr( $id . '_offset' ); ?>"/>
						
									<br/>

									<label for="<?php echo esc_attr( $id . '_spessore' ); ?>">Spessore massimo tagliabile in millimetri:</label>
									<input type="number" class="spessore" value="<?php echo esc_attr( $spessore ); ?>" id="<?php echo esc_attr( $id . '_spessore' ); ?>"/>
						
									<br/>
									
								</li>

								<!-- Save Button -->
								<li class="saveButtonLi">
									<?php $nonce = wp_create_nonce( 'save_macchina' ); ?>
									<input type="hidden" class="saveNonce" name="_wpnonce" value="<?php echo esc_attr( $nonce ); ?>" />
									<p class="button-left">
										<input class='saveButton' value="Salva" type="button" id="<?php echo esc_attr( $id . '_save' ); ?>">
									</p>
									<?php $nonce = wp_create_nonce( 'delete_macchina' ); ?>
									<input type="hidden" class="deleteNonce" name="_wpnonce" value="<?php echo esc_attr( $nonce ); ?>" />
									<p class="button-right">
										<input class='deleteButton' value="Elimina" type="button"  id="<?php echo esc_attr( $id . '_delete' ); ?>">
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
				<?php $nonce = wp_create_nonce( 'save_macchina' ); ?>
				<input type="hidden" id="_wpnonce" name="_wpnonce" value="<?php echo esc_attr( $nonce ); ?>" />
				<label for="newId">ID macchina:
					<?php echo wc_help_tip( 'Inserire un codice identificativo UNICO della macchina', false ); ?>
				</label>
				<input type="text" placeholder="e.g.: 0x123456789ABCDEF..." id="newId"/>

				<br/>
				
				<label for="newName">Nome della macchina:</label>
				<input type="text" placeholder="e.g.: OSSIT" id="newName"/>
				
				<br/>

				<label for="newOffset">Larghezza del taglio in millimetri:</label>
				<input type="number" placeholder="e.g.: 15" id="newOffset"/>
				
				<br/>

				<label for="newSpessore">Spessore massimo tagliabile in millimetri:</label>
				<input type="number" placeholder="e.g.: 100" id="newSpessore"/>
				
				<br/>
				
			</li>

			<!-- Save Button -->
			<li class="saveButtonLi">
				<button type="submit" id="saveMachineBtn">
				Salva Nuova Macchina
				</button>
			</li>

			</ul>

			<!-- Add New Token Button -->
			<p class="button-right">
				<input type="button" value="Crea Nuova Macchina" id="addMachineBtn">
			</p>
		</div>
		
	<?php
	$materiali = get_all_materiali();
	?>
		<div class="woocommerce_form_wrapper">
			
			<h3>Materiali</h3>

			<?php
			if ( ! empty( $materiali ) ) {
				foreach ( $materiali as $materiale ) {
					$id   = $materiale['id'];
					$data = $materiale['data'];
					$peso = $data['peso_specifico'];
					$prezzo = $data['prezzo_kilo'];
					?>
						<div class="token-row">
							<div class="handle" id="<?php echo esc_attr( $id ); ?>">
								<ul class="closed-token">
									<li class="li-field-label">
										<strong>
											<span><?php echo esc_html( $id ); ?></span>
										</strong>
									</li>
								</ul>
							</div>

							<div class="settings">
								<ul class="dropdownMenu">
										<!-- First DropDown -->
								<li>
								<label for="<?php echo esc_attr( $id . '_id' ); ?>">Codice materiale:</label>
									<input type="text" class="id" readonly value="<?php echo esc_attr( $id ); ?>" id="<?php echo esc_attr( $id . '_id' ); ?>"/>
						
									<br/>

									<label for="<?php echo esc_attr( $id . '_peso' ); ?>">Peso specifico in kg/m<sup>3</sup>:</label>
									<input type="number" class="peso" value="<?php echo esc_attr( $peso ); ?>" id="<?php echo esc_attr( $id . '_peso' ); ?>"/>
						
									<br/>

									<label for="<?php echo esc_attr( $id . '_prezzo' ); ?>">Prezzo al kg:</label>
									<input type="number" class="prezzo" value="<?php echo esc_attr( $prezzo ); ?>" id="<?php echo esc_attr( $id . '_prezzo' ); ?>"/>
						
									<br/>								
								</li>

								<!-- Save Button -->
								<li class="saveButtonLi">
									<?php $nonce = wp_create_nonce( 'save_materiale' ); ?>
									<input type="hidden" class="saveNonce" name="_wpnonce" value="<?php echo esc_attr( $nonce ); ?>" />
									<p class="button-left">
										<input class='saveButton' value="Salva" type="button" id="<?php echo esc_attr( $id . '_save' ); ?>">
									</p>
									<?php $nonce = wp_create_nonce( 'delete_materiale' ); ?>
									<input type="hidden" class="deleteNonce" name="_wpnonce" value="<?php echo esc_attr( $nonce ); ?>" />
									<p class="button-right">
										<input class='deleteButton' value="Elimina" type="button"  id="<?php echo esc_attr( $id . '_delete' ); ?>">
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
				<?php $nonce = wp_create_nonce( 'save_materiale' ); ?>
				<input type="hidden" id="_wpnonce" name="_wpnonce" value="<?php echo esc_attr( $nonce ); ?>" />
				<label for="newId">Codice materiale:
					<?php echo wc_help_tip( 'Inserire un codice identificativo UNICO del materiale', false ); ?>
				</label>
				<input type="text" placeholder="e.g.: A1B2C3..." id="newId"/>

				<br/>

				<label for="newPeso">Peso specifico in kg/m<sup>3</sup>:</label>
				<input type="number" placeholder="e.g.: 15" id="newPeso"/>
				
				<br/>

				<label for="newPrezzo">Prezzo al kg:</label>
				<input type="number" placeholder="e.g.: 20" id="newPrezzo"/>
				
				<br/>
				
			</li>

			<!-- Save Button -->
			<li class="saveButtonLi">
				<button type="submit" id="saveMaterialBtn">
				Salva Nuovo Materiale
				</button>
			</li>

			</ul>

			<!-- Add New Token Button -->
			<p class="button-right">
				<input type="button" value="Crea Nuovo Materiale" id="addMaterialBtn">
			</p>
		</div>
	<?php
	$partnerships = get_all_partnership_level();
	?>
		<div class="woocommerce_form_wrapper">
			
			<h3>Macchine da taglio</h3>

			<?php
			if ( ! empty( $partnerships ) ) {
				foreach ( $partnerships as $partnership ) {
					$id       	 = $partnership['id'];
					$data     	 = $partnership['data'];
					$percentuale = $data['percentage'];
					?>
						<div class="token-row">
							<div class="handle" id="<?php echo esc_attr( $id ); ?>">
								<ul class="closed-token">
									<li class="li-field-label">
										<strong>
											<span><?php echo esc_html( $id ); ?></span>
										</strong>
									</li>
								</ul>
							</div>

							<div class="settings">
								<ul class="dropdownMenu">
										<!-- First DropDown -->
								<li>
								<label for="<?php echo esc_attr( $id . '_id' ); ?>">Livello di partnership:</label>
									<input type="text" class="id" readonly value="<?php echo esc_attr( $id ); ?>" id="<?php echo esc_attr( $id . '_id' ); ?>"/>
						
									<br/>

									<label for="<?php echo esc_attr( $id . '_percentuale' ); ?>">Percentuale di ricarico sul totale:</label>
									<input type="number" class="percentuale" value="<?php echo esc_attr( $percentuale ); ?>" id="<?php echo esc_attr( $id . '_percentuale' ); ?>"/>
						
									<br/>
									
								</li>

								<!-- Save Button -->
								<li class="saveButtonLi">
									<?php $nonce = wp_create_nonce( 'save_partnership' ); ?>
									<input type="hidden" class="saveNonce" name="_wpnonce" value="<?php echo esc_attr( $nonce ); ?>" />
									<p class="button-left">
										<input class='saveButton' value="Salva" type="button" id="<?php echo esc_attr( $id . '_save' ); ?>">
									</p>
									<?php $nonce = wp_create_nonce( 'delete_partnership' ); ?>
									<input type="hidden" class="deleteNonce" name="_wpnonce" value="<?php echo esc_attr( $nonce ); ?>" />
									<p class="button-right">
										<input class='deleteButton' value="Elimina" type="button"  id="<?php echo esc_attr( $id . '_delete' ); ?>">
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
				<?php $nonce = wp_create_nonce( 'save_partnership' ); ?>
				<input type="hidden" id="_wpnonce" name="_wpnonce" value="<?php echo esc_attr( $nonce ); ?>" />
				<label for="newId">Livello di partnership:
					<?php echo wc_help_tip( 'Inserire un nomw identificativo UNICO del livello di partnership', false ); ?>
				</label>
				<input type="text" placeholder="e.g.: NORMAL" id="newId"/>

				<br/>

				<label for="newPercentuale">Percentuale di ricarico sul totale:</label>
				<input type="number" placeholder="e.g.: 15" id="newPercentuale"/>
				
				<br/>
				
			</li>

			<!-- Save Button -->
			<li class="saveButtonLi">
				<button type="submit" id="savePartnershipBtn">
				Salva Nuovo Livello Partnership
				</button>
			</li>

			</ul>

			<!-- Add New Token Button -->
			<p class="button-right">
				<input type="button" value="Crea Nuovo Livello Partnership" id="addPartnershipBtn">
			</p>
		</div>
	<?php
}

/**
 * Returns all the machines created by user.
 *
 * @since 1.0.0
 * @return array
 */
function get_all_macchine() {
	$saved_data   = array();
	$option_names = wp_load_alloption();
	foreach ( $option_names as $option_name => $value ) {
		if ( preg_match( '/^sidertaglio_macchina_(0x[0-9a-fA-F]+)$/', $option_name, $matches ) ) {
			$id   = $matches[1];
			$data    = get_option( $option_name );
			array_push(
				$saved_data,
				array(
					'id'   => $id,
					'data'    => $data,
				)
			);
		}
	}

	return $saved_data;
}

add_action( 'wp_ajax_get_all_macchine', 'get_all_macchine' );
add_action( 'wp_ajax_nopriv_get_all_macchine', 'get_all_macchine' );

/**
 * Returns all the materials created by user.
 *
 * @since 1.0.0
 * @return array
 */
function get_all_materiali() {
	$saved_data   = array();
	$option_names = wp_load_alloption();
	foreach ( $option_names as $option_name => $value ) {
		if ( preg_match( '/^sidertaglio_materiale_(0x[0-9a-fA-F]+)$/', $option_name, $matches ) ) {
			$id   = $matches[1];
			$data    = get_option( $option_name );
			array_push(
				$saved_data,
				array(
					'id'   => $id,
					'data'    => $data,
				)
			);
		}
	}

	return $saved_data;
}

add_action( 'wp_ajax_get_all_materiali', 'get_all_materiali' );
add_action( 'wp_ajax_nopriv_get_all_materiali', 'get_all_materiali' );

/**
 * Returns all the partnership levels created by user.
 *
 * @since 1.0.0
 * @return array
 */
function get_all_partnership_level() {
	$saved_data   = array();
	$option_names = wp_load_alloption();
	foreach ( $option_names as $option_name => $value ) {
		if ( preg_match( '/^sidertaglio_partnership_(0x[0-9a-fA-F]+)$/', $option_name, $matches ) ) {
			$id   = $matches[1];
			$data    = get_option( $option_name );
			array_push(
				$saved_data,
				array(
					'id'   => $id,
					'data'    => $data,
				)
			);
		}
	}

	return $saved_data;
}

add_action( 'wp_ajax_get_all_partnership_level', 'get_all_partnership_level' );
add_action( 'wp_ajax_nopriv_get_all_partnership_level', 'get_all_partnership_level' );

/**
 * Deletes machine given its id.
 *
 * @since 1.0.0
 * @return void
 */
function delete_macchina() {
	check_ajax_referer( 'delete_macchina', 'security' );
	if ( isset( $_POST['id'] ) ) {
		$id = 'sidertaglio_macchina_' . sanitize_text_field( wp_unslash( $_POST['id'] ) );
		delete_option( $id );
	}
	wp_die();
}

add_action( 'wp_ajax_delete_macchina', 'delete_macchina' );
add_action( 'wp_ajax_nopriv_delete_macchina', 'delete_macchina' );

/**
 * Deletes material given its id.
 *
 * @since 1.0.0
 * @return void
 */
function delete_materiale() {
	check_ajax_referer( 'delete_materiale', 'security' );
	if ( isset( $_POST['id'] ) ) {
		$id = 'sidertaglio_materiale_' . sanitize_text_field( wp_unslash( $_POST['id'] ) );
		delete_option( $id );
	}
	wp_die();
}

add_action( 'wp_ajax_delete_materiale', 'delete_materiale' );
add_action( 'wp_ajax_nopriv_delete_materiale', 'delete_materiale' );

/**
 * Deletes partnership level given its id.
 *
 * @since 1.0.0
 * @return void
 */
function delete_partnership_level() {
	check_ajax_referer( 'delete_partnership_level', 'security' );
	if ( isset( $_POST['id'] ) ) {
		$id = 'sidertaglio_partnership_' . sanitize_text_field( wp_unslash( $_POST['id'] ) );
		delete_option( $id );
	}
	wp_die();
}

add_action( 'wp_ajax_delete_partnership_level', 'delete_partnership_level' );
add_action( 'wp_ajax_nopriv_delete_partnership_level', 'delete_partnership_level' );

/**
 * Stores a new machine as option.
 *
 * @since 1.0.0
 * @return void
 */
function save_new_macchina() {
	check_ajax_referer( 'save_new_macchina', 'security' );
	if ( isset( $_POST['id'] ) && isset( $_POST['name'] ) && isset( $_POST['offset'] ) && isset( $_POST['spessore'] ) ) {
		$id = 'sidertaglio_macchina_' . sanitize_text_field( wp_unslash( $_POST['id'] ) );
		update_option(
			$id,
			array(
				'name'    => sanitize_text_field( wp_unslash( $_POST['name'] ) ),
				'offset'   => sanitize_text_field( wp_unslash( $_POST['offset'] ) ),
				'spessore' => sanitize_text_field( wp_unslash( $_POST['spessore'] ) ),
			)
		);
	}
	wp_die();
}

add_action( 'wp_ajax_save_save_new_macchina', 'save_new_macchina' );
add_action( 'wp_ajax_nopriv_save_new_macchina', 'save_new_macchina' );

/**
 * Stores a new material as option.
 *
 * @since 1.0.0
 * @return void
 */
function save_new_materiale() {
	check_ajax_referer( 'save_new_materiale', 'security' );
	if ( isset( $_POST['id'] ) && isset( $_POST['peso_specifico'] ) ) {
		$id = 'sidertaglio_materiale_' . sanitize_text_field( wp_unslash( $_POST['id'] ) );
		update_option(
			$id,
			array(
				'peso_specifico' => sanitize_text_field( wp_unslash( $_POST['peso_specifico'] ) ),
				'prezzo_kilo' => sanitize_text_field( wp_unslash( $_POST['prezzo_kilo'] ) ),
			)
		);
	}
	wp_die();
}

add_action( 'wp_ajax_save_new_materiale', 'save_new_materiale' );
add_action( 'wp_ajax_nopriv_save_new_materiale', 'save_new_materiale' );

/**
 * Stores a new partnership level as option.
 *
 * @since 1.0.0
 * @return void
 */
function save_new_partnership_level() {
	check_ajax_referer( 'save_new_partnership_level', 'security' );
	if ( isset( $_POST['id'] ) && isset( $_POST['name'] ) && isset( $_POST['percentage'] )) {
		$id = 'sidertaglio_partnership_' . sanitize_text_field( wp_unslash( $_POST['id'] ) );
		update_option(
			$id,
			array(
				'precentage'    => sanitize_text_field( wp_unslash( $_POST['percentage'] ) ),
			)
		);
	}
	wp_die();
}

add_action( 'wp_ajax_save_new_partnership_level', 'save_new_partnership_level' );
add_action( 'wp_ajax_nopriv_save_new_partnership_level', 'save_new_partnership_level' );

// /**
//  * Register settings options.
//  *
//  * @since 1.0.0
//  * @return void
//  */
// function sidertaglio_register_settings() {
// 	add_option( 'sidertaglio_jwt_token', '' );
// 	register_setting( 'sidertaglio_options_group', 'sidertaglio_jwt_token', 'sidertaglio_callback' );
// }

// add_action( 'admin_init', 'sidertaglio_register_settings' );

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