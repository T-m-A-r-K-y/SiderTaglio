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
define( 'SPA_VERSION', '1.5' );
define( 'SPA_FILE', __FILE__ );
define( 'SPA_PATH', plugin_dir_path( SPA_FILE ) );
define( 'SPA_URL', plugin_dir_url( SPA_FILE ) );
require_once dirname( __FILE__ ) . '/assets/utility/tcpdf/tcpdf.php';

/**
 * Extends TPCPDF class to customize header
 *
 * @since 1.0.0
 */
class MYPDF extends TCPDF {
	/**
	 * Sets custom header
	 *
	 * @since 1.0.0
	 */
	public function Header() {
		$image_file = ABSPATH . '/wp-content/uploads/2021/03/logo-esteso-blu-1.png';
		$this->Image( $image_file, 10, -5, 0, 40, 'PNG', '', 'T', false, 300, '', false, false, 0, false, false, false );
	}
	/**
	 * Sets custom footer
	 *
	 * @since 1.0.0
	 */
	public function Footer() {
		$b_margin        = $this->getBreakMargin();
		$auto_page_break = $this->AutoPageBreak;
		$this->SetAutoPageBreak( false, 0 );
		$this->SetAutoPageBreak( $auto_page_break, $b_margin );
		$this->setPageMark();
	}
}

/**
 * Adds billing information fields
 *
 * @since 1.0.0
 *
 * @param object $user User.
 */
function billing_information_fields( $user ) {
	?>
	<h3>Informazioni di fatturazione</h3>

	<table class="form-table">
	<tr>
		<th><label for="address">Indirizzo</label></th>
		<td>
			<input type="text" name="address" id="address" value="<?php echo esc_attr( get_the_author_meta( 'address', $user->ID ) ); ?>" class="regular-text" /><br />
		</td>
	</tr>
	<tr>
		<th><label for="city">Città</label></th>
		<td>
			<input type="text" name="city" id="city" value="<?php echo esc_attr( get_the_author_meta( 'city', $user->ID ) ); ?>" class="regular-text" /><br />
		</td>
	</tr>
	<tr>
		<th><label for="postalcode">CAP</label></th>
		<td>
			<input type="text" name="postalcode" id="postalcode" value="<?php echo esc_attr( get_the_author_meta( 'postalcode', $user->ID ) ); ?>" class="regular-text" /><br />
		</td>
	</tr>
	<tr>
		<th><label for="vatcode">P.IVA</label></th>
		<td>
			<input type="text" name="vatcode" id="vatcode" value="<?php echo esc_attr( get_the_author_meta( 'vatcode', $user->ID ) ); ?>" class="regular-text" /><br />
		</td>
	</tr>
	<tr>
		<th><label for="country">Stato</label></th>
		<td>
			<input type="text" name="country" id="country" value="<?php echo esc_attr( get_the_author_meta( 'country', $user->ID ) ); ?>" class="regular-text" /><br />
		</td>
	</tr>
	<tr>
		<th><label for="phonenumber">Telefono</label></th>
		<td>
			<input type="tel" name="phonenumber" id="phonenumber" value="<?php echo esc_attr( get_the_author_meta( 'phonenumber', $user->ID ) ); ?>" class="regular-text" /><br />
		</td>
	</tr>
	</table>
	<?php
}

add_action( 'show_user_profile', 'billing_information_fields' );
add_action( 'edit_user_profile', 'billing_information_fields' );

/**
 * Saves billing information to user profile
 *
 * @since 1.0.0
 *
 * @param object $user_id User ID.
 */
function save_billing_information_fields_fields( $user_id ) {
	if ( empty( $_POST['_wpnonce'] ) || ! wp_verify_nonce( sanitize_key( wp_unslash( $_POST['_wpnonce'] ) ), 'update-user_' . $user_id ) ) {
		return;
	}

	if ( ! current_user_can( 'edit_user', $user_id ) ) {
		return false;
	}

	if ( isset( $_POST['address'] ) ) {
		update_user_meta( $user_id, 'address', sanitize_text_field( wp_unslash( $_POST['address'] ) ) );
	}
	if ( isset( $_POST['city'] ) ) {
		update_user_meta( $user_id, 'city', sanitize_text_field( wp_unslash( $_POST['city'] ) ) );
	}
	if ( isset( $_POST['postalcode'] ) ) {
		update_user_meta( $user_id, 'postalcode', sanitize_text_field( wp_unslash( $_POST['postalcode'] ) ) );
	}
	if ( isset( $_POST['vatcode'] ) ) {
		update_user_meta( $user_id, 'vatcode', sanitize_text_field( wp_unslash( $_POST['vatcode'] ) ) );
	}
	if ( isset( $_POST['country'] ) ) {
		update_user_meta( $user_id, 'country', sanitize_text_field( wp_unslash( $_POST['country'] ) ) );
	}
	if ( isset( $_POST['phonenumber'] ) ) {
		update_user_meta( $user_id, 'phonenumber', sanitize_text_field( wp_unslash( $_POST['phonenumber'] ) ) );
	}
}

add_action( 'personal_options_update', 'save_billing_information_fields_fields' );
add_action( 'edit_user_profile_update', 'save_billing_information_fields_fields' );

/**
 * Adds custom meta field
 *
 * @since 1.0.0
 *
 * @param object $user User.
 */
function add_partnership_level_field( $user ) {
	if ( ! current_user_can( 'edit_user' ) ) {
		return;
	}
	$custom_field_value = get_user_meta( $user->ID, 'partnership_level', true );
	?>
	<h3>Partnership</h3>
	<table class="form-table">
		<tr>
			<th><label for="partnership_level">Livello di partnership</label></th>
			<td>
				<input type="text" name="partnership_level" id="partnership_level" value="<?php echo esc_attr( $custom_field_value ); ?>" class="regular-text" />
			</td>
		</tr>
	</table>
	<?php
}
add_action( 'show_user_profile', 'add_partnership_level_field' );
add_action( 'edit_user_profile', 'add_partnership_level_field' );

/**
 * Saves custom meta to user profile
 *
 * @since 1.0.0
 *
 * @param object $user_id User ID.
 */
function save_partnership_level_field( $user_id ) {
	if ( empty( $_POST['_wpnonce'] ) || ! wp_verify_nonce( sanitize_key( wp_unslash( $_POST['_wpnonce'] ) ), 'update-user_' . $user_id ) ) {
		return;
	}
	if ( ! current_user_can( 'edit_user' ) ) {
		return;
	}

	// Verify if this is an auto save routine.
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}

	if ( isset( $_POST['partnership_level'] ) ) {
		update_user_meta( $user_id, 'partnership_level', sanitize_text_field( wp_unslash( $_POST['partnership_level'] ) ) );
	}
}
add_action( 'personal_options_update', 'save_partnership_level_field' );
add_action( 'edit_user_profile_update', 'save_partnership_level_field' );

/**
 * Shortcode per form di login.
 *
 * @return string
 */
function custom_user_login_shortcode() {
	if ( is_user_logged_in() ) {
		return do_shortcode( '[sidertaglio_form_preventivi_shortcode]' );
	}
	$redirect_url = home_url( add_query_arg( null, null ) );
	ob_start();
	wp_enqueue_script( 'spa-jquery-tiptip', SPA_URL . 'assets/js/jquery.tipTip.min.js', array( 'jquery' ), SPA_VERSION, true );
	wp_enqueue_style( 'spa_custom_user_css', SPA_URL . 'assets/css/sidertaglio-preventivi-automatici-user.css', array(), SPA_VERSION, null, 'all' );
	wp_enqueue_script( 'spa_custom_user_js', SPA_URL . 'assets/js/sidertaglio-preventivi-automatici-user.js', array( 'jquery' ), SPA_VERSION, true );
	?>
	<div class="login-page">
		<div class="form">
			<form class="login-form" action="<?php echo esc_url( wp_login_url( $redirect_url ) ); ?>" method="post">
				<input type="text" name="log" placeholder="Username" required />
				<input type="password" name="pwd" placeholder="Password" required />
				<input type="submit" value="Login" />
				<p class="message">
					<a href="#"> Non sei registrato? </a>
				</p>
				<p class="message">
					<a href="mailto:info@sidertagliomodena.it"><strong style="text-decoration: underline;">Scrivi</strong> a info@sidertagliomodena.it per ottenere le tue credenziali</a>
				</p>
			</form>
		</div>
	</div>

	<?php
	return ob_get_clean();
}
add_shortcode( 'custom_user_login', 'custom_user_login_shortcode' );

/**
 * Shortcode per la generazione del form.
 *
 * @return string
 */
function form_preventivi_shortcode() {
	ob_start();
	wp_enqueue_script( 'spa-jquery-tiptip', SPA_URL . 'assets/js/jquery.tipTip.min.js', array( 'jquery' ), SPA_VERSION, true );
	wp_enqueue_style( 'spa_custom_user_css', SPA_URL . 'assets/css/sidertaglio-preventivi-automatici-user.css', array(), SPA_VERSION, null, 'all' );
	wp_enqueue_script( 'spa_custom_user_js', SPA_URL . 'assets/js/sidertaglio-preventivi-automatici-user.js', array( 'jquery' ), SPA_VERSION, true );
	$materiali   = get_all_materiali();
	$lavorazioni = get_all_lavorazioni();
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

		.campoDimensioni {
			display: none;
		}
	</style>
	<div class="sidertaglio_form_wrapper">
		<div id="overlay">
			<div class="cv-spinner">
				<span class="spinner"></span>
			</div>
		</div>
		<h3 style="text-align:center;">Ricevi il tuo preventivo in tempo reale</h3>
		<!-- Dropdown Menu -->
		<ul class="dropdownMenu" id="dropdownMenu">

			<!-- First DropDown -->
			<li class="firstDropDown">
				<?php $nonce = wp_create_nonce( 'genera_preventivo' ); ?>
				<input type="hidden" id="_wpnonce" name="_wpnonce" value="<?php echo esc_attr( $nonce ); ?>" />
				<?php $nonce2 = wp_create_nonce( 'retrieve_machine_parameters' ); ?>
				<input type="hidden" id="_wpnonce_machines" name="_wpnonce_machines" value="<?php echo esc_attr( $nonce2 ); ?>" />
				<div class="shapeWrapper">
				<div class="selectForma">
					<label for="forma">Scegli una forma tra le seguenti:</label>
					<select name="forma" id="forma">
					<option value="">Seleziona una forma...</option>
					<option value="rettangolo">Piastra</option>
					<option value="cerchio">Disco</option>
					<option value="anello">Anello</option>
					</select>
				</div>
				<div class="verticalDivider">
					<div class="dashedDivider"> </div>
					<span>OPPURE</span>
					<div class="dashedDivider"> </div>
				</div>
				<div class="uploadForma">
					<label for="uploadSVG">Carica il file SVG della tua figura:</label>
					<input type="file" id="uploadSVG" name="uploadSVG" accept=".svg">
					<input type="text" id="formaSvg" name="formaSvg" style="display: none;" placeholder="Enter the forma of the SVG">
				</div>
				</div>
				
				<div id="dimensioniRettangolo" class="campoDimensioni">
					<label for="larghezza">Larghezza (X) in millimetri:</label>
					<input type="number" id="larghezza" name="larghezza">
					<label for="altezza">Altezza (Y) in millimetri:</label>
					<input type="number" id="altezza" name="altezza">
					<br/>
				</div>

				<div id="dimensioniCerchio" class="campoDimensioni">
					<label for="raggio">Raggio in millimetri:</label>
					<input type="number" id="raggio" name="raggio">
					<br/>
				</div>

				<div id="dimensioniAnello" class="campoDimensioni">
					<label for="raggioExt">Raggio esterno in millimetri:</label>
					<input type="number" id="raggioExt" name="raggioExt">
					<label for="raggioInt">Raggio interno in millimetri:</label>
					<input type="number" id="raggioInt" name="raggioInt">
					<br/>
				</div>

				<label for="materiale">Materiale:</label>
				<br/>
				<select name="materiale" id="materiale">
					<option value="">Seleziona un materiale</option>
					<?php
					if ( ! empty( $materiali ) ) {
						foreach ( $materiali as $materiale ) {
							?>
							<option value="<?php echo esc_attr( $materiale['parent_id'] ); ?>">
								<?php echo esc_html( strtoupper( $materiale['parent_id'] ) ); ?>
							</option>
							<?php
						}
					}
					?>
				</select>
				<br/>

				<label for="spessore">Spessore:</label>
				<select name="spessore" id="spessore" disabled>
					<option value="">Seleziona uno spessore</option>
					<?php
					if ( ! empty( $materiali ) ) {
						foreach ( $materiali as $materiale ) {
							foreach ( $materiale['children'] as $child_id => $child_data ) {
								?>
								<?php $spessore = substr( $child_id, strlen( $materiale['parent_id'] ) ); ?>
								<option class="<?php echo esc_attr( $materiale['parent_id'] ); ?>" value="<?php echo esc_attr( $spessore ); ?>" style="display: none;">
									<?php echo esc_html( $spessore ); ?> millimetri
								</option>
								<?php
							}
						}
					}
					?>
				</select>
				<br/>
				
				<div id="lavorazioni-options">
					<label>Lavorazioni:</label><br/>
					<?php
					foreach ( $lavorazioni as $lavorazione ) {
						?>
						<input type="checkbox" id="lavorazione-<?php echo esc_attr( $lavorazione['id'] ); ?>" name="lavorazioni[<?php echo esc_attr( $lavorazione['id'] ); ?>]" value="1">
						<label for="lavorazione-<?php echo esc_attr( $lavorazione['id'] ); ?>">
							<?php echo esc_html( strtoupper( $lavorazione['id'] ) ); ?>
						</label><br/>
						<?php
					}
					?>
				</div>
			
				<br/>

				<label for="quantità">Numero pezzi:</label>
				<input type="number" id="quantità" name="quantità">
				<br/>
			</li>

			<!-- Save Button -->
			<li class="saveButtonLi">
				<button type="submit" id="generaPreventivo">
				Genera Preventivo
				</button>
			</li>
		</ul>
	</div>
	<?php
	return ob_get_clean();
}
add_shortcode( 'sidertaglio_form_preventivi_shortcode', 'form_preventivi_shortcode' );

/**
 * Renders the form for plugin settings.
 *
 * @since 1.0.0
 * @return void
 */
function sidertaglio_settings_form() {
	wp_enqueue_script( 'spa-jquery-tiptip', SPA_URL . 'assets/js/jquery.tipTip.min.js', array( 'jquery' ), SPA_VERSION, true );
	wp_enqueue_style( 'spa_custom_admin_css', SPA_URL . 'assets/css/sidertaglio-preventivi-automatici-admin.css', array(), SPA_VERSION, null, 'all' );
	wp_enqueue_script( 'spa_custom_admin_js', SPA_URL . 'assets/js/sidertaglio-preventivi-automatici-admin.js', array( 'jquery' ), SPA_VERSION, true );
	?>
		<div class="wrap">
			<h1>Sidertaglio Preventivi Automatici Settings</h1>
			<form action="options.php" method="post">
			<?php
			settings_fields( 'sidertaglio_preventivi_automatici_options_group' );
			do_settings_sections( 'sidertaglio_preventivi_automatici_settings' );
			submit_button();
			?>
			</form>
		</div>
	<?php
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
		<div class="sidertaglio_form_wrapper">
			<div id="overlay">
				<div class="cv-spinner">
					<span class="custom-spinner"></span>
				</div>
			</div>
			<h3>Macchine da taglio</h3>

			<?php
			if ( ! empty( $macchine ) ) {
				foreach ( $macchine as $macchina ) {
					$parent_id       = $macchina['parent_id'];
					$common_data     = $macchina['common_data'];
					$children        = $macchina['children'];
					$name            = $common_data['name'];
					$spessore_max    = $common_data['spessore_max'];
					$numero_di_canne = $common_data['numero_di_canne'];

					?>
					<div class="parent-token-row">
						<div class="handle" id="<?php echo esc_attr( $parent_id ); ?>">
							<ul class="closed-token">
								<li class="li-field-parent-label">
									<strong>
										<span><?php echo esc_html( $parent_id ); ?></span>
									</strong>
								</li>
								<!-- <li class="new-child-button">
									<div class="icon-plus-button">&plus;</div>
								</li> -->
							</ul>
						</div>
						<div class="child-list">
							<div class="parent-settings">
								<ul class="dropdownMenu">
									<li>
										<label for="<?php echo esc_attr( $parent_id . '_id' ); ?>">ID della macchina:</label>
										<input type="text" class="parent_id" readonly value="<?php echo esc_attr( $parent_id ); ?>" id="<?php echo esc_attr( $parent_id . '_id' ); ?>"/>
					
										<br/>

										<label for="<?php echo esc_attr( $parent_id . '_name' ); ?>">Nome della macchina:</label>
										<input type="text" class="name" value="<?php echo esc_attr( $name ); ?>" id="<?php echo esc_attr( $id . '_name' ); ?>"/>
										
										<br/>

										<label for="<?php echo esc_attr( $parent_id . '_spessore_max' ); ?>">Spessore massimo tagliabile in millimetri:</label>
										<input type="number" class="spessore_max" value="<?php echo esc_attr( $spessore_max ); ?>" id="<?php echo esc_attr( $parent_id . '_spessore_max' ); ?>"/>
							
										<br/>

										<label for="<?php echo esc_attr( $parent_id . '_numero_di_canne' ); ?>">Numero di canne disponibili:</label>
										<input type="number" class="numero_di_canne" value="<?php echo esc_attr( $numero_di_canne ); ?>" id="<?php echo esc_attr( $parent_id . '_numero_di_canne' ); ?>"/>
							
										<br/>
									</li>
									<li class="saveButtonLi">
										<?php $nonce = wp_create_nonce( 'save_array_macchine' ); ?>
										<input type="hidden" class="saveNonce" name="_wpnonce" value="<?php echo esc_attr( $nonce ); ?>" />
										<p class="button-left">
											<input class='saveMachineArrayButton saveButton' value="Salva" type="button" id="<?php echo esc_attr( $parent_id . '_save' ); ?>">
										</p>
										<?php $nonce = wp_create_nonce( 'delete_array_macchine' ); ?>
										<input type="hidden" class="deleteNonce" name="_wpnonce" value="<?php echo esc_attr( $nonce ); ?>" />
										<p class="button-right">
											<input class='deleteMachineArrayButton deleteButton' value="Elimina" type="button" id="<?php echo esc_attr( $parent_id . '_delete' ); ?>">
										</p>
									</li>
								</ul>
							</div>
							<?php
							foreach ( $children as $child_id => $child_data ) {
								$offset       = $child_data['offset'];
								$spessore     = $child_data['spessore'];
								$v_taglio     = $child_data['v_taglio'];
								$costo_orario = $child_data['costo_orario'];
								$innesco      = $child_data['innesco'];
								?>
								<div class="token-row">
									<div class="handle" id="<?php echo esc_attr( $child_id ); ?>">
										<ul class="closed-token">
											<li class="li-field-label">
												<strong>
													<span><?php echo esc_html( $child_id ); ?></span>
												</strong>
											</li>
										</ul>
									</div>

									<div class="settings">
										<ul class="dropdownMenu">
											<li>
												<label for="<?php echo esc_attr( $child_id . '_id' ); ?>">ID della macchina:</label>
												<input type="text" class="id" readonly value="<?php echo esc_attr( $child_id ); ?>" id="<?php echo esc_attr( $child_id . '_id' ); ?>"/>
									
												<br/>

												<label for="<?php echo esc_attr( $child_id . '_offset' ); ?>">Larghezza del taglio in millimetri:</label>
												<input type="number" class="offset" value="<?php echo esc_attr( $offset ); ?>" id="<?php echo esc_attr( $child_id . '_offset' ); ?>"/>
									
												<br/>
												
												<label for="<?php echo esc_attr( $child_id . '_spessore' ); ?>">Spessore massimo tagliabile in millimetri:</label>
												<input type="number" class="spessore" value="<?php echo esc_attr( $spessore ); ?>" id="<?php echo esc_attr( $child_id . '_spessore' ); ?>"/>
									
												<br/>

												<label for="<?php echo esc_attr( $child_id . '_v_taglio' ); ?>">Velocità di taglio in millimetri/secondo:</label>
												<input type="number" class="v_taglio" value="<?php echo esc_attr( $v_taglio ); ?>" id="<?php echo esc_attr( $child_id . '_v_taglio' ); ?>"/>
									
												<br/> 

												<label for="<?php echo esc_attr( $child_id . '_costo_orario' ); ?>">Costo orario in €/ora:</label>
												<input type="number" class="costo_orario" value="<?php echo esc_attr( $costo_orario ); ?>" id="<?php echo esc_attr( $child_id . '_costo_orario' ); ?>"/>
									
												<br/>

												<label for="<?php echo esc_attr( $child_id . '_innesco' ); ?>">Tempo di innesco in secondi:</label>
												<input type="number" class="innesco" value="<?php echo esc_attr( $innesco ); ?>" id="<?php echo esc_attr( $child_id . '_innesco' ); ?>"/>
									
												<br/>
											</li>

											<li class="saveButtonLi">
												<?php $nonce = wp_create_nonce( 'save_macchina' ); ?>
												<input type="hidden" class="saveNonce" name="_wpnonce" value="<?php echo esc_attr( $nonce ); ?>" />
												<p class="button-left">
													<input class='saveMachineButton saveButton' value="Salva" type="button" id="<?php echo esc_attr( $child_id . '_save' ); ?>">
												</p>
												<?php $nonce = wp_create_nonce( 'delete_macchina' ); ?>
												<input type="hidden" class="deleteNonce" name="_wpnonce" value="<?php echo esc_attr( $nonce ); ?>" />
												<p class="button-right">
													<input class='deleteMachineButton deleteButton' value="Elimina" type="button" id="<?php echo esc_attr( $child_id . '_delete' ); ?>">
												</p>
											</li>
										</ul>
									</div>
								</div>
								<?php
							}
							?>
						</div>
					</div>
					<?php
				}
			}
			?>

			<br>
			
			<!-- Dropdown Menu -->
			<ul style="display:none;" class="dropdownMenu" id="dropdownNewMacchinaMenu">

			<!-- First DropDown -->
			<li class="firstDropDown">
				<?php $nonce = wp_create_nonce( 'save_macchina' ); ?>
				<input type="hidden" id="_wpMachinenonce" name="_wpMachinenonce" value="<?php echo esc_attr( $nonce ); ?>" />
				<label for="newMachineId">ID macchina:
					<?php echo sidertaglio_help_tip( 'Inserire un codice identificativo UNICO della macchina', false ); ?>
				</label>
				<input type="text" placeholder="e.g.: 0x123456789ABCDEF..." id="newMachineId"/>

				<br/>
				
				<label for="newName">Nome della macchina:</label>
				<input type="text" placeholder="e.g.: OSSIT" id="newName"/>
				
				<br/>
				
				<label for="newSpessoreMax">Spessore massimo tagliabile in millimetri:</label>
				<input type="number" placeholder="e.g.: 100" id="newSpessoreMax"/>
				
				<br/>

				<label for="newNumeroDiCanne">Numero di canne disponibili:</label>
				<input type="number" placeholder="e.g.: 6" id="newNumeroDiCanne"/>
				
				<br/>

				<label for="newSpessore">Spessore del materiale da tagliare:</label>
				<input type="number" placeholder="e.g.: 8" id="newSpessore"/>
				
				<br/>

				<label for="newOffset">Larghezza del taglio in millimetri:</label>
				<input type="number" placeholder="e.g.: 15" id="newOffset"/>
				
				<br/>

				<label for="newVTaglio">Velocità di taglio in millimetri/secondo:</label>
				<input type="number" placeholder="e.g.: 50" id="newVTaglio"/>
				
				<br/> 

				<label for="newCostoOrario">Costo orario in €/ora:</label>
				<input type="number" placeholder="e.g.: 250" id="newCostoOrario"/>
				
				<br/>

				<label for="newInnesco">Tempo di innesco in secondi:</label>
				<input type="number" placeholder="e.g.: 250" id="newInnesco"/>
				
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
		<br/>
	<?php
	$materiali = get_all_materiali();
	?>
		<div class="sidertaglio_form_wrapper">
			
			<h3>Materiali</h3>

			<?php
			if ( ! empty( $materiali ) ) {
				foreach ( $materiali as $materiale ) {
					$parent_id   = $materiale['parent_id'];
					$common_data = $materiale['common_data'];
					$children    = $materiale['children'];
					$peso        = $common_data['peso_specifico'];

					?>
					<div class="parent-token-row">
						<div class="handle" id="<?php echo esc_attr( $parent_id ); ?>">
							<ul class="closed-token">
								<li class="li-field-parent-label">
									<strong>
										<span><?php echo esc_html( $parent_id ); ?></span>
									</strong>
								</li>
								<!-- <li class="new-child-button">
									<div class="icon-plus-button">&plus;</div>
								</li> -->
							</ul>
						</div>

						<div class="child-list">
							<div class="parent-settings">
								<ul class="dropdownMenu">
									<li>
										<label for="<?php echo esc_attr( $parent_id . '_parent_id' ); ?>">Materiale:</label>
										<input readonly type="text" class="parent_id" value="<?php echo esc_attr( $parent_id ); ?>" id="<?php echo esc_attr( $parent_id . '_parent_id' ); ?>"/>
										</br>

										<label for="<?php echo esc_attr( $parent_id . '_peso' ); ?>">Peso specifico in kg/m<sup>3</sup>:</label>
										<input type="number" class="peso" value="<?php echo esc_attr( $peso ); ?>" id="<?php echo esc_attr( $parent_id . '_peso' ); ?>"/>
										</br>
									</li>
									<li class="saveButtonLi">
										<?php $nonce = wp_create_nonce( 'save_array_materiali' ); ?>
										<input type="hidden" class="saveNonce" name="_wpnonce" value="<?php echo esc_attr( $nonce ); ?>" />
										<p class="button-left">
											<input class='saveMaterialArrayButton saveButton' value="Salva" type="button" id="<?php echo esc_attr( $parent_id . '_save' ); ?>">
										</p>
										<?php $nonce = wp_create_nonce( 'delete_array_materiali' ); ?>
										<input type="hidden" class="deleteNonce" name="_wpnonce" value="<?php echo esc_attr( $nonce ); ?>" />
										<p class="button-right">
											<input class='deleteMaterialArrayButton deleteButton' value="Elimina" type="button" id="<?php echo esc_attr( $parent_id . '_delete' ); ?>">
										</p>
									</li>
								</ul>
							</div>
							<?php
							foreach ( $children as $child_id => $child_data ) {
								$prezzo   = $child_data['prezzo_ton'];
								$spessore = substr( $child_id, strlen( $parent_id ) );
								?>
								<div class="token-row">
									<div class="handle" id="<?php echo esc_attr( $child_id ); ?>">
										<ul class="closed-token">
											<li class="li-field-label">
												<strong>
													<span><?php echo esc_html( $child_id ); ?></span>
												</strong>
											</li>
										</ul>
									</div>

									<div class="settings">
										<ul class="dropdownMenu">
											<li>
												<label for="<?php echo esc_attr( $child_id . '_id' ); ?>">ID Materiale:</label>
												<input readonly type="text" class="id" value="<?php echo esc_attr( $child_id ); ?>" id="<?php echo esc_attr( $child_id . '_id' ); ?>"/>
												</br>

												<label for="<?php echo esc_attr( $child_id . '_spessore' ); ?>">Spessore:</label>
												<input readonly type="number" class="spessore" value="<?php echo esc_attr( $spessore ); ?>" id="<?php echo esc_attr( $child_id . '_spessore' ); ?>"/>
												</br>

												<label for="<?php echo esc_attr( $child_id . '_prezzo' ); ?>">Prezzo alla tonnellata:</label>
												<input type="number" class="prezzo" value="<?php echo esc_attr( $prezzo ); ?>" id="<?php echo esc_attr( $child_id . '_prezzo' ); ?>"/>
												</br>
											</li>

											<li class="saveButtonLi">
												<?php $nonce = wp_create_nonce( 'save_materiale' ); ?>
												<input type="hidden" class="saveNonce" name="_wpnonce" value="<?php echo esc_attr( $nonce ); ?>" />
												<p class="button-left">
													<input class='saveMaterialButton saveButton' value="Salva" type="button" id="<?php echo esc_attr( $child_id . '_save' ); ?>">
												</p>
												<?php $nonce = wp_create_nonce( 'delete_materiale' ); ?>
												<input type="hidden" class="deleteNonce" name="_wpnonce" value="<?php echo esc_attr( $nonce ); ?>" />
												<p class="button-right">
													<input class='deleteMaterialButton deleteButton' value="Elimina" type="button" id="<?php echo esc_attr( $child_id . '_delete' ); ?>">
												</p>
											</li>
										</ul>
									</div>
								</div>
								<?php
							}
							?>
						</div>
					</div>
					<?php
				}
			}
			?>
			<br>
			
			<!-- Dropdown Menu -->
			<ul style="display:none;" class="dropdownMenu" id="dropdownNewMaterialeMenu">

			<!-- First DropDown -->
			<li class="firstDropDown">
				<?php $nonce = wp_create_nonce( 'save_materiale' ); ?>
				<input type="hidden" id="_wpMaterialnonce" name="_wpMaterialnonce" value="<?php echo esc_attr( $nonce ); ?>" />
				<label for="newMaterialId">Codice materiale:
					<?php echo sidertaglio_help_tip( 'Inserire un codice identificativo del materiale', false ); ?>
				</label>
				<input type="text" placeholder="e.g.: S355JR..." id="newMaterialId"/>

				<br/>

				<label for="newSpessoreMateriale">Spessore del materiale:</label>
				<input type="number" placeholder="e.g.: 6" id="newSpessoreMateriale"/>
				
				<br/>

				<label for="newPeso">Peso specifico in kg/m<sup>3</sup>:</label>
				<input type="number" placeholder="e.g.: 15" id="newPeso"/>
				
				<br/>

				<label for="newPrezzo">Prezzo alla tonnellata:</label>
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
		<br/>
	<?php
	$partnerships = get_all_partnership_level();
	?>
		<div class="sidertaglio_form_wrapper">
			
				
			<h3>Livelli di partnership</h3>

			<?php
			if ( ! empty( $partnerships ) ) {
				foreach ( $partnerships as $partnership ) {
					$id          = $partnership['id'];
					$data        = $partnership['data'];
					$percentuale = $data['percentage'];
					$rottame     = $data['rottame'];
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

									<label for="<?php echo esc_attr( $id . '_rottame' ); ?>">Costo di vendita del rottame al Kg:</label>
									<input type="number" class="rottame" value="<?php echo esc_attr( $rottame ); ?>" id="<?php echo esc_attr( $id . '_rottame' ); ?>"/>
						
									<br/>
									
								</li>

								<!-- Save Button -->
								<li class="saveButtonLi">
									<?php $nonce = wp_create_nonce( 'save_partnership_level' ); ?>
									<input type="hidden" class="saveNonce" name="_wpnonce" value="<?php echo esc_attr( $nonce ); ?>" />
									<p class="button-left">
										<input class='savePartnershipButton saveButton' value="Salva" type="button" id="<?php echo esc_attr( $id . '_save' ); ?>">
									</p>
									<?php $nonce = wp_create_nonce( 'delete_partnership_level' ); ?>
									<input type="hidden" class="deleteNonce" name="_wpnonce" value="<?php echo esc_attr( $nonce ); ?>" />
									<p class="button-right">
										<input class='deletePartnershipButton deleteButton' value="Elimina" type="button"  id="<?php echo esc_attr( $id . '_delete' ); ?>">
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
			<ul style="display:none;" class="dropdownMenu" id="dropdownNewPartnershipMenu">

			<!-- First DropDown -->
			<li class="firstDropDown">
				<?php $nonce = wp_create_nonce( 'save_partnership_level' ); ?>
				<input type="hidden" id="_wpPartnershipnonce" name="_wpPartnershipnonce" value="<?php echo esc_attr( $nonce ); ?>" />
				<label for="newPartnershipId">Livello di partnership:
					<?php echo sidertaglio_help_tip( 'Inserire un nome identificativo UNICO del livello di partnership', false ); ?>
				</label>
				<input type="text" placeholder="e.g.: NORMAL" id="newPartnershipId"/>

				<br/>

				<label for="newPercentuale">Percentuale di ricarico sul totale:</label>
				<input type="number" placeholder="e.g.: 15" id="newPercentuale"/>
				
				<br/>

				<label for="newRottame">Costo di vendita del rottame al Kg:</label>
				<input type="number" placeholder="e.g.: 0.1" id="newRottame"/>
				
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
		<br/>
	<?php
	$lavorazioni = get_all_lavorazioni();
	?>
		<div class="sidertaglio_form_wrapper">
			
			<h3>Lavorazioni</h3>

			<?php
			if ( ! empty( $lavorazioni ) ) {
				foreach ( $lavorazioni as $lavorazione ) {
					$id    = $lavorazione['id'];
					$data  = $lavorazione['data'];
					$costo = $data['costo'];
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
								<label for="<?php echo esc_attr( $id . '_id' ); ?>">Lavorazione:</label>
									<input type="text" class="id" readonly value="<?php echo esc_attr( $id ); ?>" id="<?php echo esc_attr( $id . '_id' ); ?>"/>
						
									<br/>

									<label for="<?php echo esc_attr( $id . '_costo' ); ?>">Costo lavorazione:</label>
									<input type="number" class="costo" value="<?php echo esc_attr( $costo ); ?>" id="<?php echo esc_attr( $id . '_costo' ); ?>"/>
						
									<br/>
									
								</li>

								<!-- Save Button -->
								<li class="saveButtonLi">
									<?php $nonce = wp_create_nonce( 'save_lavorazione' ); ?>
									<input type="hidden" class="saveNonce" name="_wpnonce" value="<?php echo esc_attr( $nonce ); ?>" />
									<p class="button-left">
										<input class='saveLavorazioneButton saveButton' value="Salva" type="button" id="<?php echo esc_attr( $id . '_save' ); ?>">
									</p>
									<?php $nonce = wp_create_nonce( 'delete_lavorazione' ); ?>
									<input type="hidden" class="deleteNonce" name="_wpnonce" value="<?php echo esc_attr( $nonce ); ?>" />
									<p class="button-right">
										<input class='deleteLavorazioneButton deleteButton' value="Elimina" type="button"  id="<?php echo esc_attr( $id . '_delete' ); ?>">
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
			<ul style="display:none;" class="dropdownMenu" id="dropdownNewLavorazioneMenu">

			<!-- First DropDown -->
			<li class="firstDropDown">
				<?php $nonce = wp_create_nonce( 'save_lavorazione' ); ?>
				<input type="hidden" id="_wpLavorazionenonce" name="_wpLavorazionenonce" value="<?php echo esc_attr( $nonce ); ?>" />
				<label for="newPartnershipId">Lavorazione:
					<?php echo sidertaglio_help_tip( 'Inserire un nome identificativo UNICO della lavorazione', false ); ?>
				</label>
				<input type="text" placeholder="e.g.: Lapidellatura" id="newLavorazioneId"/>

				<br/>

				<label for="newCostoLavorazione">Costo di lavorazione:</label>
				<input type="number" placeholder="e.g.: 15" id="newCostoLavorazione"/>
				
				<br/>
				
			</li>

			<!-- Save Button -->
			<li class="saveButtonLi">
				<button type="submit" id="saveLavorazioneBtn">
				Salva Nuova Lavorazione
				</button>
			</li>

			</ul>

			<!-- Add New Token Button -->
			<p class="button-right">
				<input type="button" value="Crea Nuova Lavorazione" id="addLavorazioneBtn">
			</p>
		</div>
		<br/>
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
	$option_names = wp_load_alloptions();
	foreach ( $option_names as $option_name => $value ) {
		if ( preg_match( '/^sidertaglio_macchina_(.*)/', $option_name, $matches ) ) {
			$parent_id      = $matches[1];
			$data           = get_option( $option_name );
			$common_data    = $data['common'];
			$child_machines = $data['machines'];
			$saved_data[]   = array(
				'parent_id'   => $parent_id,
				'common_data' => $common_data,
				'children'    => $child_machines,
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
	$option_names = wp_load_alloptions();

	foreach ( $option_names as $option_name => $value ) {
		if ( preg_match( '/^sidertaglio_materiale_(.*)/', $option_name, $matches ) ) {
			$parent_id       = $matches[1];
			$data            = get_option( $option_name );
			$common_data     = $data['common'];
			$child_materials = $data['materials'];
			$saved_data[]    = array(
				'parent_id'   => $parent_id,
				'common_data' => $common_data,
				'children'    => $child_materials,
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
	$option_names = wp_load_alloptions();
	foreach ( $option_names as $option_name => $value ) {
		if ( preg_match( '/^sidertaglio_partnership_(.*)/', $option_name, $matches ) ) {
			$id   = $matches[1];
			$data = get_option( $option_name );
			array_push(
				$saved_data,
				array(
					'id'   => $id,
					'data' => $data,
				)
			);
		}
	}

	return $saved_data;
}

add_action( 'wp_ajax_get_all_partnership_level', 'get_all_partnership_level' );
add_action( 'wp_ajax_nopriv_get_all_partnership_level', 'get_all_partnership_level' );

/**
 * Returns all the lavorazioni created by user.
 *
 * @since 1.0.0
 * @return array
 */
function get_all_lavorazioni() {
	$saved_data   = array();
	$option_names = wp_load_alloptions();
	foreach ( $option_names as $option_name => $value ) {
		if ( preg_match( '/^sidertaglio_lavorazione_(.*)/', $option_name, $matches ) ) {
			$id   = $matches[1];
			$data = get_option( $option_name );
			array_push(
				$saved_data,
				array(
					'id'   => $id,
					'data' => $data,
				)
			);
		}
	}

	return $saved_data;
}

add_action( 'wp_ajax_get_all_lavorazioni', 'get_all_lavorazioni' );
add_action( 'wp_ajax_nopriv_get_all_lavorazioni', 'get_all_lavorazioni' );

/**
 * Deletes machine given its id.
 *
 * @since 1.0.0
 * @return void
 */
function delete_macchina() {
	check_ajax_referer( 'delete_macchina', 'security' );
	if ( isset( $_POST['parent_id'] ) && isset( $_POST['child_id'] ) ) {
		$array_id      = 'sidertaglio_macchina_' . strtoupper( sanitize_text_field( wp_unslash( $_POST['parent_id'] ) ) );
		$machine_id    = strtoupper( sanitize_text_field( wp_unslash( $_POST['child_id'] ) ) );
		$machines_info = get_option( $array_id, array() );
		if ( isset( $machines_info['machines'][ $machine_id ] ) ) {
			unset( $machines_info['machines'][ $machine_id ] );
			if ( empty( $machines_info['machines'] ) ) {
				delete_option( $array_id );
			} else {
				update_option( $array_id, $machines_info );
			}
		}
	}
	wp_die();
}

add_action( 'wp_ajax_delete_macchina', 'delete_macchina' );
add_action( 'wp_ajax_nopriv_delete_macchina', 'delete_macchina' );

/**
 * Deletes whole machine array given its id.
 *
 * @since 1.0.0
 * @return void
 */
function delete_array_macchine() {
	check_ajax_referer( 'delete_array_macchine', 'security' );
	if ( isset( $_POST['id'] ) ) {
		$id = 'sidertaglio_macchina_' . strtoupper( sanitize_text_field( wp_unslash( $_POST['id'] ) ) );
		delete_option( $id );
	}
	wp_die();
}

add_action( 'wp_ajax_delete_array_macchine', 'delete_array_macchine' );
add_action( 'wp_ajax_nopriv_delete_array_macchine', 'delete_array_macchine' );


/**
 * Deletes material given its id.
 *
 * @since 1.0.0
 * @return void
 */
function delete_materiale() {
	check_ajax_referer( 'delete_materiale', 'security' );
	if ( isset( $_POST['parent_id'] ) && isset( $_POST['child_id'] ) ) {
		$array_id       = 'sidertaglio_materiale_' . strtolower( sanitize_text_field( wp_unslash( $_POST['parent_id'] ) ) );
		$material_id    = strtolower( sanitize_text_field( wp_unslash( $_POST['child_id'] ) ) );
		$materials_info = get_option( $array_id, array() );
		if ( isset( $materials_info['materials'][ $material_id ] ) ) {
			unset( $materials_info['materials'][ $material_id ] );
			if ( empty( $materials_info['materials'] ) ) {
				delete_option( $array_id );
			} else {
				update_option( $array_id, $materials_info );
			}
		}
	}
	wp_die();
}

add_action( 'wp_ajax_delete_materiale', 'delete_materiale' );
add_action( 'wp_ajax_nopriv_delete_materiale', 'delete_materiale' );

/**
 * Deletes material array given its id.
 *
 * @since 1.0.0
 * @return void
 */
function delete_array_materiali() {
	check_ajax_referer( 'delete_array_materiali', 'security' );
	if ( isset( $_POST['id'] ) ) {
		$id = 'sidertaglio_materiale_' . strtoupper( sanitize_text_field( wp_unslash( $_POST['id'] ) ) );
		delete_option( $id );
	}
	wp_die();
}

add_action( 'wp_ajax_delete_array_materiali', 'delete_array_materiali' );
add_action( 'wp_ajax_nopriv_delete_array_materiali', 'delete_array_materiali' );

/**
 * Deletes partnership level given its id.
 *
 * @since 1.0.0
 * @return void
 */
function delete_partnership_level() {
	check_ajax_referer( 'delete_partnership_level', 'security' );
	if ( isset( $_POST['id'] ) ) {
		$id = 'sidertaglio_partnership_' . strtolower( sanitize_text_field( wp_unslash( $_POST['id'] ) ) );
		delete_option( $id );
	}
	wp_die();
}

add_action( 'wp_ajax_delete_partnership_level', 'delete_partnership_level' );
add_action( 'wp_ajax_nopriv_delete_partnership_level', 'delete_partnership_level' );

/**
 * Deletes lavorazione given its id.
 *
 * @since 1.0.0
 * @return void
 */
function delete_lavorazione() {
	check_ajax_referer( 'delete_lavorazione', 'security' );
	if ( isset( $_POST['id'] ) ) {
		$id = 'sidertaglio_lavorazione_' . strtolower( sanitize_text_field( wp_unslash( $_POST['id'] ) ) );
		delete_option( $id );
	}
	wp_die();
}

add_action( 'wp_ajax_delete_lavorazione', 'delete_lavorazione' );
add_action( 'wp_ajax_nopriv_delete_lavorazione', 'delete_lavorazione' );

/**
 * Stores a machine array as option.
 *
 * @since 1.0.0
 * @return void
 */
function save_array_macchine() {
	check_ajax_referer( 'save_array_macchine', 'security' );
	if ( isset( $_POST['id'] ) && isset( $_POST['name'] ) && isset( $_POST['spessore_max'] ) && isset( $_POST['numero_di_canne'] ) ) {
		$array_id      = 'sidertaglio_macchina_' . strtoupper( sanitize_text_field( wp_unslash( $_POST['id'] ) ) );
		$machines_info = get_option( $array_id );
		if ( false === $machines_info ) {
			$machines_info = array(
				'common'   => array(),
				'machines' => array(),
			);
		}
		$machines_info['common'] = array(
			'name'            => sanitize_text_field( wp_unslash( $_POST['name'] ) ),
			'spessore_max'    => sanitize_text_field( wp_unslash( $_POST['spessore_max'] ) ),
			'numero_di_canne' => sanitize_text_field( wp_unslash( $_POST['numero_di_canne'] ) ),
		);
		update_option( $array_id, $machines_info );
	}
	wp_die();
}

add_action( 'wp_ajax_save_array_macchine', 'save_array_macchine' );
add_action( 'wp_ajax_nopriv_save_array_macchine', 'save_array_macchine' );

/**
 * Stores a new machine as option.
 *
 * @since 1.0.0
 * @return void
 */
function save_macchina() {
	check_ajax_referer( 'save_macchina', 'security' );
	if ( isset( $_POST['id'] ) && isset( $_POST['name'] ) && isset( $_POST['offset'] ) && isset( $_POST['spessore'] ) && isset( $_POST['spessore_max'] ) && isset( $_POST['v_taglio'] ) && isset( $_POST['costo_orario'] ) && isset( $_POST['numero_di_canne'] ) && isset( $_POST['innesco'] ) ) {
		$array_id      = 'sidertaglio_macchina_' . strtoupper( sanitize_text_field( wp_unslash( $_POST['id'] ) ) );
		$machine_id    = strtoupper( sanitize_text_field( wp_unslash( $_POST['id'] ) ) ) . sanitize_text_field( wp_unslash( $_POST['spessore'] ) );
		$machines_info = get_option( $array_id );
		if ( false === $machines_info ) {
			$machines_info = array(
				'common'   => array(),
				'machines' => array(),
			);
		}
		$machines_info['common']                  = array(
			'name'            => sanitize_text_field( wp_unslash( $_POST['name'] ) ),
			'spessore_max'    => sanitize_text_field( wp_unslash( $_POST['spessore_max'] ) ),
			'numero_di_canne' => sanitize_text_field( wp_unslash( $_POST['numero_di_canne'] ) ),
		);
		$machines_info['machines'][ $machine_id ] = array(
			'offset'       => sanitize_text_field( wp_unslash( $_POST['offset'] ) ),
			'spessore'     => sanitize_text_field( wp_unslash( $_POST['spessore'] ) ),
			'v_taglio'     => sanitize_text_field( wp_unslash( $_POST['v_taglio'] ) ),
			'costo_orario' => sanitize_text_field( wp_unslash( $_POST['costo_orario'] ) ),
			'innesco'      => sanitize_text_field( wp_unslash( $_POST['innesco'] ) ),
		);

		update_option( $array_id, $machines_info );
	}
	wp_die();
}

add_action( 'wp_ajax_save_macchina', 'save_macchina' );
add_action( 'wp_ajax_nopriv_save_macchina', 'save_macchina' );

/**
 * Stores a material array as option.
 *
 * @since 1.0.0
 * @return void
 */
function save_array_materiale() {
	check_ajax_referer( 'save_array_materiale', 'security' );
	if ( isset( $_POST['id'] ) && isset( $_POST['spessore'] ) && isset( $_POST['peso_specifico'] ) ) {
		$array_id       = 'sidertaglio_materiale_' . strtolower( sanitize_text_field( wp_unslash( $_POST['id'] ) ) );
		$material_id    = strtolower( sanitize_text_field( wp_unslash( $_POST['id'] ) ) ) . sanitize_text_field( wp_unslash( $_POST['spessore'] ) );
		$materials_info = get_option( $array_id );
		if ( false === $materials_info ) {
			$materials_info = array(
				'common'    => array(),
				'materials' => array(),
			);
		}
		$materials_info['common'] = array(
			'peso_specifico' => sanitize_text_field( wp_unslash( $_POST['peso_specifico'] ) ),
		);
		update_option( $array_id, $materials_info );
	}
	wp_die();
}

add_action( 'wp_ajax_save_array_materiale', 'save_array_materiale' );
add_action( 'wp_ajax_nopriv_save_array_materiale', 'save_array_materiale' );

/**
 * Stores a new material as option.
 *
 * @since 1.0.0
 * @return void
 */
function save_materiale() {
	check_ajax_referer( 'save_materiale', 'security' );
	if ( isset( $_POST['id'] ) && isset( $_POST['spessore'] ) && isset( $_POST['peso_specifico'] ) && isset( $_POST['prezzo_ton'] ) ) {
		$array_id       = 'sidertaglio_materiale_' . strtolower( sanitize_text_field( wp_unslash( $_POST['id'] ) ) );
		$material_id    = strtolower( sanitize_text_field( wp_unslash( $_POST['id'] ) ) ) . sanitize_text_field( wp_unslash( $_POST['spessore'] ) );
		$materials_info = get_option( $array_id );
		if ( false === $materials_info ) {
			$materials_info = array(
				'common'    => array(),
				'materials' => array(),
			);
		}
		$materials_info['common']                    = array(
			'peso_specifico' => sanitize_text_field( wp_unslash( $_POST['peso_specifico'] ) ),
		);
		$materials_info['materials'][ $material_id ] = array(
			'prezzo_ton' => sanitize_text_field( wp_unslash( $_POST['prezzo_ton'] ) ),
		);
		update_option( $array_id, $materials_info );
	}
	wp_die();
}

add_action( 'wp_ajax_save_materiale', 'save_materiale' );
add_action( 'wp_ajax_nopriv_save_materiale', 'save_materiale' );

/**
 * Stores a new partnership level as option.
 *
 * @since 1.0.0
 * @return void
 */
function save_partnership_level() {
	check_ajax_referer( 'save_partnership_level', 'security' );
	if ( isset( $_POST['id'] ) && isset( $_POST['percentage'] ) && isset( $_POST['rottame'] ) ) {
		$id = 'sidertaglio_partnership_' . strtolower( sanitize_text_field( wp_unslash( $_POST['id'] ) ) );
		update_option(
			$id,
			array(
				'percentage' => sanitize_text_field( wp_unslash( $_POST['percentage'] ) ),
				'rottame'    => sanitize_text_field( wp_unslash( $_POST['rottame'] ) ),
			)
		);
	}
	wp_die();
}

add_action( 'wp_ajax_save_partnership_level', 'save_partnership_level' );
add_action( 'wp_ajax_nopriv_save_partnership_level', 'save_partnership_level' );

/**
 * Stores a new lavorazione as option.
 *
 * @since 1.0.0
 * @return void
 */
function save_lavorazione() {
	check_ajax_referer( 'save_lavorazione', 'security' );
	if ( isset( $_POST['id'] ) && isset( $_POST['costo'] ) ) {
		$id = 'sidertaglio_lavorazione_' . strtolower( sanitize_text_field( wp_unslash( $_POST['id'] ) ) );
		update_option(
			$id,
			array(
				'costo' => sanitize_text_field( wp_unslash( $_POST['costo'] ) ),
			)
		);
	}
	wp_die();
}

add_action( 'wp_ajax_save_lavorazione', 'save_lavorazione' );
add_action( 'wp_ajax_nopriv_save_lavorazione', 'save_lavorazione' );

/**
 * Retrieves the parameter of the best fitting machine.
 *
 * @since 1.0.0
 * @return void
 */
function retrieve_machine_parameters() {
	check_ajax_referer( 'retrieve_machine_parameters', 'security' );
	if ( isset( $_POST['materiale'] ) && isset( $_POST['spessore'] ) ) {

		$materiale = strtoupper( sanitize_text_field( wp_unslash( $_POST['materiale'] ) ) );
		$spessore  = sanitize_text_field( wp_unslash( $_POST['spessore'] ) );

		$machines         = get_all_macchine();
		$suitable_machine = null;

		foreach ( $machines as $machine ) {
			$children  = $machine['children'];
			$parent_id = $materiale['parent_id'];
			if ( $machine['common_data']['spessore_max'] >= $spessore ) {
				foreach ( $children as $child_id => $child_data ) {
					$offset       = $child_data['offset'];
					$spessore     = $child_data['spessore'];
					$v_taglio     = $child_data['v_taglio'];
					$costo_orario = $child_data['costo_orario'];
					if ( is_null( $suitable_machine ) || ( $suitable_machine['child_data']['spessore'] > $spessore_child && $spessore_child >= $spessore ) ) {
						$suitable_machine = array(
							'id'          => $child_id,
							'parent_id'   => $machine['parent_id'],
							'common_data' => $machine['common_data'],
							'child_data'  => $child_data,
						);
					}
				}
			}
		}

		if ( $suitable_machine ) {
			wp_send_json_success( array( 'machine' => $suitable_machine ) );
		} else {
			wp_send_json_error( 'No suitable machine found.' );
		}
	}
}
add_action( 'wp_ajax_retrieve_machine_parameters', 'retrieve_machine_parameters' );
add_action( 'wp_ajax_nopriv_retrieve_machine_parameters', 'retrieve_machine_parameters' );


/**
 * Generate a PDF with the estimate of cost for a metal cut.
 *
 * @since 1.0.0
 */
function genera_preventivo() {
	check_ajax_referer( 'genera_preventivo', 'security' );
	if ( isset( $_POST['materiale'] ) && isset( $_POST['spessore'] ) && isset( $_POST['dim_x'] ) && isset( $_POST['dim_y'] ) && isset( $_POST['quantita'] ) && isset( $_POST['superfice'] ) && isset( $_POST['perimetro'] ) && isset( $_POST['p_reale'] ) && isset( $_POST['nested'] ) && isset( $_POST['lavorazioni'] ) && isset( $_POST['forma'] ) && isset( $_POST['machine_id'] ) && isset( $_POST['machine_parent_id'] ) && isset( $_POST['machine_offset'] ) && isset( $_POST['machine_name'] ) && isset( $_POST['machine_v_taglio'] ) && isset( $_POST['machine_costo_orario'] ) && isset( $_POST['machine_numero_canne'] ) && isset( $_POST['machine_innesco'] ) ) {
		/**
		 * Initialize variables from web
		 */
		$perimetro            = sanitize_text_field( wp_unslash( $_POST['perimetro'] ) );
		$superfice            = sanitize_text_field( wp_unslash( $_POST['superfice'] ) );
		$materiale            = sanitize_text_field( wp_unslash( $_POST['materiale'] ) );
		$spessore             = sanitize_text_field( wp_unslash( $_POST['spessore'] ) );
		$dim_x                = sanitize_text_field( wp_unslash( $_POST['dim_x'] ) );
		$dim_y                = sanitize_text_field( wp_unslash( $_POST['dim_y'] ) );
		$quantita             = sanitize_text_field( wp_unslash( $_POST['quantita'] ) );
		$p_reale              = sanitize_text_field( wp_unslash( $_POST['p_reale'] ) );
		$child_machine_id     = sanitize_text_field( wp_unslash( $_POST['machine_id'] ) );
		$machine_parent_id    = sanitize_text_field( wp_unslash( $_POST['machine_parent_id'] ) );
		$machine_name         = sanitize_text_field( wp_unslash( $_POST['machine_name'] ) );
		$machine_offset       = sanitize_text_field( wp_unslash( $_POST['machine_offset'] ) );
		$machine_v_taglio     = sanitize_text_field( wp_unslash( $_POST['machine_v_taglio'] ) );
		$machine_costo_orario = sanitize_text_field( wp_unslash( $_POST['machine_costo_orario'] ) );
		$machine_innesco      = sanitize_text_field( wp_unslash( $_POST['machine_innesco'] ) );
		$numero_canne         = sanitize_text_field( wp_unslash( $_POST['machine_numero_canne'] ) );
		$nested               = rest_sanitize_boolean( sanitize_text_field( wp_unslash( $_POST['nested'] ) ) );
		$forma                = sanitize_text_field( wp_unslash( $_POST['forma'] ) );
		$altri_costi          = 0;
		$pezzi_grezzi         = 0;
		$k3                   = true;
		$k4                   = true;

		$machine_offset_percentuale = get_sidertaglio_preventivi_automatici_setting( 'offset_percentuale' );
		$ricarico_materiale         = get_sidertaglio_preventivi_automatici_setting( 'ricarico_materiale_globale' );

		if ( isset( $_POST['lavorazioni'] ) && is_array( $_POST['lavorazioni'] ) ) {
			$lavorazioni_richieste = array();
			foreach ( $_POST['lavorazioni'] as $key => $value ) {
				$sanitized_key                           = sanitize_key( $key );
				$sanitized_value                         = rest_sanitize_boolean( $value );
				$lavorazioni_richieste[ $sanitized_key ] = $sanitized_value;
			}
		}
		/**
		 * Checks if user is logged in
		 */
		if ( ! is_user_logged_in() ) {
			wp_send_json_error( 'User not logged in' );
		}

		/**
		 * Retrieves users priviligies by role given
		 */
		$user_id              = get_current_user_id();
		$partnership_level    = get_user_meta( $user_id, 'partnership_level', true );
		$partnership_id       = 'sidertaglio_partnership_' . strtolower( $partnership_level );
		$partnership_details  = get_option( $partnership_id );
		$ricarico_partnership = $partnership_details['percentage'];
		$prezzo_rottame_al_kg = $partnership_details['rottame'];

		/**
		 * Retrieves billing information
		 */
		$firstname   = wp_get_current_user()->user_firstname;
		$address     = get_user_meta( $user_id, 'address', true );
		$city        = get_user_meta( $user_id, 'city', true );
		$postalcode  = get_user_meta( $user_id, 'postalcode', true );
		$vatcode     = get_user_meta( $user_id, 'vatcode', true );
		$country     = get_user_meta( $user_id, 'country', true );
		$phonenumber = get_user_meta( $user_id, 'phonenumber', true );

		/**
		 * Retrieves material details
		 */
		$materiale_id = 'sidertaglio_materiale_' . $materiale;
		$materiali    = get_option( $materiale_id );
		if ( ! empty( $materiali ) ) {
			$materiale_children_details = $materiali['materials'][ $materiale . $spessore ];
			$materiale_parent_details   = $materiali['common'];
			$peso_specifico             = $materiale_parent_details['peso_specifico'];
			$prezzo_materiale_al_kg     = $materiale_children_details['prezzo_ton'];
			$prezzo_materiale_al_kg     = $prezzo_materiale_al_kg/1000;
		}
		$peso_specifico = 7.86;

		/**
		 * Retrieves lavorazioni details
		 */
		$lavorazioni      = get_all_lavorazioni();
		$lavorazioni_temp = array();
		if ( ! empty( $lavorazioni ) ) {
			foreach ( $lavorazioni as $lavorazione ) {
				$lavorazioni_temp[ strtolower( $lavorazione['id'] ) ] = $lavorazione['data']['costo'];
			}
		}
		$lavorazioni = $lavorazioni_temp;

		/**
		 * Calculates wheights used by calculations
		 */
		$dim_x               = $dim_x + 2 * $machine_offset;
		$dim_y               = $dim_y + 2 * $machine_offset;
		$p_reale             = ( $p_reale / 1e6 ) * $peso_specifico;
		$p_quadrotto         = ( $dim_x * $dim_y * $spessore / 1e6 ) * $peso_specifico;
		$p_esterno           = $p_reale;
		$p_rottame           = $p_quadrotto - ( $p_reale + ( $p_reale * $machine_offset_percentuale / 100 ) );
		$p_quadrotto_plus_10 = ( ( $dim_x + 10 ) * ( $dim_y + 10 ) * $spessore / 1e6 ) * $peso_specifico;

		/**
		 * Chooses which wheight to use in calculations
		 */
		if ( $p_quadrotto < 0 ) {
			if ( $p_reale / $p_quadrotto <= 0.30 ) {
				$p_utilizzato = $p_reale;
			} elseif ( $p_reale / $p_quadrotto <= 0.60 ) {
				$p_utilizzato = $p_esterno;
			} else {
				$p_utilizzato = $p_quadrotto;
			}
		} else {
			$p_utilizzato = $p_quadrotto;
		}

		/**
		 * Calculates the price for material for each piece
		 */
		if ( $nested ) {
			$costo_materiale = ( ( $p_reale + ( $p_reale * $machine_offset_percentuale / 100 ) ) * ( $prezzo_materiale_al_kg + ( $prezzo_materiale_al_kg * $ricarico_materiale / 100 ) ) + ( $p_rottame * ( ( $prezzo_materiale_al_kg + ( $prezzo_materiale_al_kg * $ricarico_materiale / 100 ) ) - $prezzo_rottame_al_kg ) ) );
			$numero_canne    = 1;
		} else {
			$costo_materiale = ( ( $p_quadrotto * ( $prezzo_materiale_al_kg + $prezzo_materiale_al_kg / 100 * $ricarico_materiale ) ) - ( $p_rottame * $prezzo_rottame_al_kg ) );
		}

		/**
		 * Calculates the price of usage for the machine
		 */
		$tempo_di_taglio = $perimetro / $machine_v_taglio;
		$tempo_di_taglio = $tempo_di_taglio + ($machine_innesco * 2);

		if ( 0 === strcmp( $machine_name, 'OSSIT' ) ) {
			if ( 0 === ( $quantita % $numero_canne ) ) {
				$costo_di_taglio = ( ( $tempo_di_taglio * ( $machine_costo_orario / 60 ) ) * intdiv( $quantita, $numero_canne ) ) / $quantita;
			} else {
				$costo_di_taglio = ( ( $tempo_di_taglio * ( $machine_costo_orario / 60 ) ) * ( intdiv( $quantita, $numero_canne ) + 1 ) ) / $quantita;
			}
		} else {
			$costo_di_taglio = $tempo_di_taglio * ( $machine_costo_orario / 3600 );
		}

		/**
		 * Calculates the price of lavorazioni
		 */
		$costo_lavorazioni = 0;
		foreach ( $lavorazioni_richieste as $lavorazione_richiesta_key => $lavorazione_richiesta_bool ) {
			if ( $lavorazione_richiesta_bool ) {
				$costo_lavorazioni += $p_reale * $lavorazioni[ $lavorazione_richiesta_key ];
			}
		}

		/**
		 * Calculates total price
		 */
		$total = 0;
		if ( 0 !== $pezzi_grezzi ) {
			$pezzi_grezzi * $p_quadrotto + ( $costo_lavorazioni * 1.1 );
		} else {
			if ( 0 === strcmp( $partnership_level, strtolower( 'Gold' ) ) || 0 === strcmp( $partnership_level, strtolower( 'Iper' ) ) ) {
				$total += ( $costo_materiale + $costo_di_taglio + $altri_costi ) + ( ( $costo_materiale + $costo_di_taglio ) / 100 * $ricarico_partnership ) + 10;
			} else {
				$total += ( $costo_materiale + $costo_materiale / 100 * $ricarico_partnership ) + ( $costo_di_taglio + $altri_costi );
				if ( $p_reale <= 20 ) {
					$total += ( $costo_di_taglio + $altri_costi ) / 2;
				} else {
					$total += ( $costo_di_taglio + $altri_costi );
				}
			}
			$total += ( ( $costo_lavorazioni ) + ( ( $costo_lavorazioni ) / 100 * 10 ) );

			if ( $k3 ) {
				$total *= 1.025;
			}

			if ( $k4 ) {
				$total *= 1.025;
			}
		}

		if ( $total - round( $total, 0, PHP_ROUND_HALF_DOWN ) > 0.5 ) {
			$total = round( $total, 0, PHP_ROUND_HALF_DOWN ) + 1;
		} else {
			$total = round( $total, 0, PHP_ROUND_HALF_DOWN ) + 0.5;
		}
		$total_each = $total;
		$total     *= $quantita;

		$pdf = new MYPDF( PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false );
		// set document information.
		$pdf->SetCreator( PDF_CREATOR );
		$pdf->SetAuthor( 'Sidertaglio' );
		$pdf->SetTitle( 'Preventivo' );
		$pdf->SetSubject( 'Preventivo' );
		$pdf->SetKeywords( 'Preventivo' );
		// set default header data.
		$pdf->SetHeaderData( PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE . ' 001', PDF_HEADER_STRING, array( 0, 64, 255 ), array( 0, 64, 128 ) );
		$pdf->setFooterData( array( 0, 64, 0 ), array( 0, 64, 128 ) );

		// set header and footer fonts.
		$pdf->setHeaderFont( array( PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN ) );
		$pdf->setFooterFont( array( PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA ) );
		// set default monospaced font.
		$pdf->SetDefaultMonospacedFont( PDF_FONT_MONOSPACED );
		// set margins.
		$pdf->SetMargins( PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT );
		$pdf->SetHeaderMargin( PDF_MARGIN_HEADER );
		$pdf->SetFooterMargin( PDF_MARGIN_FOOTER );
		$pdf->SetAutoPageBreak( true, PDF_MARGIN_BOTTOM );
		$pdf->setImageScale( PDF_IMAGE_SCALE_RATIO );
		$pdf->AddPage( 'L', 'A3' );

		$html = '
			<style>
				table {
					width: 100%;
					border-collapse: collapse;
					display: table;
					box-sizing: border-box;
					text-indent: initial;
					border-color: gray;
				}
				th, td {
					border: 1px solid #999;
					padding: 0px 8px 8px 8px;
					text-align: left;
				}
				th {
					background-color: #BBB;
					font-size: 12px;
					font-weight: 700;
				}
				.text-center {
					text-align: center;
				}
				td {
				border: 1px solid #000;
				}
				span{
				float: left;
				}
			</style>
			<table>
				<tbody>
				<tr>
					<th style="width:33%">SPETT.LE DITTA / <I>MESSRS</I></th>
					<th style="width:33%">DESTINAZIONE MERCE / <I>GOODS DESTINATION</I></th>
					<th style="padding-top:8px; width:33%; text-align:center; font-size:20px; border: 2px solid #000" colspan="2">OFFERTA CLIENTE / <I>OFFER</I></th>
				</tr>
				<tr>
					<td rowspan="2">
					<STRONG>' . strtoupper( $firstname ) . '</STRONG>
					<br>
					' . strtoupper( $address ) . '
					<br>
					' . strtoupper( $postalcode ) . ' ' . strtoupper( $city ) . ' ' . strtoupper( $country ) . '
					<br>
					<br>
					Tel.: ' . strtoupper( $phonenumber ) . '
					<BR>
					</td>
					<td>
					<STRONG>' . strtoupper( $firstname ) . '</STRONG>
					<br>
					' . strtoupper( $address ) . '
					<br>
					' . strtoupper( $postalcode ) . ' ' . strtoupper( $city ) . ' ' . strtoupper( $country ) . '
					<BR>
					</td>
					<td style="vertical-align:top; text-align:center; font-size: 25px;">
					<span style="font-weight: 700; font-size: 12px; background-color: #f2f2f2; width:100%;">NR. DOCUMENTO / <I>DOC NO.</I></span>
					<BR>
					<STRONG>11439</STRONG>
					</td>
					<td style="vertical-align:top; text-align:center; font-size: 25px;">
					<span style="font-weight: 700; font-size: 12px; background-color: #f2f2f2; width:100%;">DATA / <I>DATE</I></span>
					<BR>
					<STRONG>' . date( 'd/m/Y' ) . '</STRONG>
					</td>
				</tr>
				<tr>
					<td>
					<span style="padding-top:2px; font-weight: 300; font-size: 12px; width:100%;">CODICE FISCALE - PARTITA IVA / <I>FISCAL CODE - VATNUMBER</I></span>
					<BR>
					' . strtoupper( $vatcode ) . ' - ' . strtoupper( $vatcode ) . '
					</td>
					<td colspan="2">
					<span style="padding-top:2px; font-weight: 300; font-size: 12px; width:100%;">NS. PERSONA DI RIFERIMENTO / <I>OUR REPRESENTATIVE</I></span>
					<br>
					Yana Rybalko
					</td>
				</tr>
				</tbody>
			</table>
			<br><br>
			<table>
				<tbody>
				<tr>
					<td style="width:20%; vertical-align:top;">
					<span style="padding-top:2px; font-weight: 300; font-size: 12px; width:100%;">MODALITA\' DI PAGAMENTO / <I>PAYMENT</I></span>
					<br>
					RI.BA 60 GG F.M
					</td>
					<td style="width:20%; vertical-align:top;">
					<span style="padding-top:2px; font-weight: 300; font-size: 12px; width:100%;">BANCA DI APPOGGIO / <I>BANK</I></span>
					<br>
					</td>
					<td style="width:20%; vertical-align:top;">
					<span style="padding-top:2px; font-weight: 300; font-size: 12px; width:100%;">CODICE IBAN / <I>IBAN CODE</I></span>
					<br>
					</td>
					<td style="width:6%; vertical-align:top; text-align: center;">
					<span style="padding-top:2px; font-weight: 300; font-size: 12px; width:100%;">VALUTA / <I>CURRENCY</I></span>
					<BR>
					EURO
					</td>
					<td style="width:33%; vertical-align:top;">
					<span style="padding-top:2px; font-weight: 300; font-size: 12px; width:100%;">VS. PERSONA DI RIFERIMENTO / <I>YOUR REPRESENTATIVE</I></span>
					<BR>
					</td>
				</tr>
				</tbody>
			</table>
			<br><br>
			<table>
				<tbody >
				<tr>
					<th style="width:5%; text-align:center; border: 2px solid #000">POS. <BR> <I>ITEM</I></th>
					<th style="width:10%; text-align:center; border: 2px solid #000">CODICE PRODOTTO <BR> <I>REF. NUMBER</I></th>
					<th style="width:40%; text-align:center; border: 2px solid #000">DESCRIZIONE PRODOTTO <BR> <I>DESCRIPTION</I></th>
					<th style="width:5%; text-align:center; border: 2px solid #000">UM <BR> <I>PECK</I></th>
					<th style="width:8%; text-align:center; border: 2px solid #000">QUANTITA\' <BR> <I>QUANTITY</I></th>
					<th style="width:8%; text-align:center; border: 2px solid #000">PREZZO UNITARIO <BR> <I>UNIT PRICE</I></th>
					<th style="width:8%; text-align:center; border: 2px solid #000">IMPORTO <BR> <I>NET PRICE</I></th>
					<th style="width:10%; text-align:center; border: 2px solid #000">CONSEGNA <BR> <I>DELIVERY</I></th>
					<th style="width:6%; text-align:center; border: 2px solid #000">IVA <BR> <I>VAT</I></th>
				</tr>
				<tr style="border: 1px solid #000;">
					
					<td>
					10
					</td>
					<td>
					<STRONG>D1000001150</STRONG>
					</td>
					<td>
					<BR>
					<STRONG>VS. RICHIESTA/YOUR REQUEST R.D.O ' . strtoupper( $firstname ) . ' - ' . date( 'd/m' ) . '</STRONG>
					<br>
					<STRONG>DEL/OF ' . date( 'd/m/Y' ) . '</STRONG>
					<br>
					<br>
					' . $forma . ' ' . $dim_x . 'x' . $dim_y . 'x' . $spessore . ' - ' . $materiale . '
					<br>
					<br>
					<br>
					<br>
					<br>
					<br>
					<br>
					<strong>
					Addebito costo per certificato € 3.50<br>
					Validità offerta 5 gg.<br>
					Imballo a Vs. carico con addebito €13.00/cad.<br>
					Reso merce Franco Ns. stabilimento<br>
					Prezzi validi solo per fornitura completa<br>
					Tolleranze generali di taglio in accordo<br>
					con la UNI EN ISO 9013:2017 più ns.<br>
					condizione di fornitura<br>
					Salvo il venduto<br>
					</strong>
					</td>
					<td>
					NR
					</td>
					<td>
					' . $quantita . '
					</td>
					<td>
					' . $total_each . '
					</td>
					<td>
					' . $total . '
					</td>
					<td>
					' . date( 'd/m/Y', time() + 86400 * 30 ) . '
					</td>
					<td>
					022
					</td>
				</tr>
				</tbody>
			</table>
			<br><br>
			<table>
				<tbody>
					<tr>
						<td style="width:21%">
						</td>
						<td style="width:21%; text-align: right; vertical-align:top;">
							<span style="text-align: left;padding-top:2px; font-weight: 300; font-size: 12px; width:100%;">SPESE TRASPORTO / <I>TRANSPORT EXP.</I></span>
							<BR>
							' . $altri_costi . ' €
						</td>
						<td style="width:21%; text-align: right; vertical-align:top;">
							<span style="text-align: left;padding-top:2px; font-weight: 300; font-size: 12px; width:100%;">SPESE IMBALLAGGIO / <I>PACKING EXP.</I></span>
							<BR>
							0.00 €
						</td>
						<td rowspan="2" style="width:5%; vertical-align:top;">
						</td>
						<td rowspan="2" style="width:11%; vertical-align:top;">
						</td>
						<td rowspan="2" style="width:2%; vertical-align:top;">
						</td>
						<td rowspan="2" style="width:7%; vertical-align:top;">
						</td>
						<td style="width:12%; vertical-align:top;">
						</td>
					</tr>
					<tr>
						<td style="text-align: right; vertical-align:top;">
							<span style="text-align: left;padding-top:2px; font-weight: 300; font-size: 12px; width:100%;">RESA / <I>SHIPMENT</I></span>
						</td>
						<td style="text-align: right; vertical-align:top;">
							<span style="text-align: left;padding-top:2px; font-weight: 300; font-size: 12px; width:100%;">TRASPORTO A CURA DEL / <I>SHIPMENT EFFECTED BY</I></span>
						</td>
						<td style="text-align: right; vertical-align:top;">
							<span style="text-align: left;padding-top:2px; font-weight: 300; font-size: 12px; width:100%;">PESO MERCE / <I>GOODS WEIGHT</I></span>
							<br>
							' . $p_reale * $quantita. ' Kg
						</td>
						<td style="vertical-align:top;">
						</td>
					</tr>
					<tr>
						<td colspan="6" style="text-align:center; width:67%; vertical-align:top;">
							<span style="padding-top:5px; font-weight: 700; font-size: 12px; width:100%;">SIDERTAGLIO SRL - Yana Rybalko</span>
						</td>
						<td colspan="2" style="text-align:right; width:33%; background-color: #BBB; vertical-align:top;">
							<span style="text-align:center; padding-top:2px; font-weight: 600; font-size: 12px; width:100%;">TOTALE IMPONIBILE / <I>TOTAL VAT TAXABLE AMOUNT</I></span>
							<br>
							<strong style="font-size:30px;">' . $total . '</strong>
						</td>
					</tr>
				</tbody>
			</table>';

		// output the HTML content.
		$pdf->writeHTML( $html, true, false, true, false, '' );

		$pdf->SetAutoPageBreak( true, 0 );
		$pathto = 'wp-content/uploads/2024/preventivo_' . $forma . '_' . $materiale . '_' . $spessore . '_' . $dim_x . '_' . $dim_y . '.pdf';
		$pdf->Output( ABSPATH . $pathto, 'F' );

		$response_data = array(
			'tempo_taglio'      => $tempo_di_taglio,
			'perimetro'         => $perimetro,
			'v_taglio'          => $machine_v_taglio,
			'nome_macchina'     => $machine_name,
			'partnership'       => $partnership_level,
			'p_reale'           => $p_reale,
			'p_quadrotto'       => $p_quadrotto,
			'p_utilizzato'      => $p_utilizzato,
			'p_rottame'         => $p_rottame,
			'costo_materiale'   => $costo_materiale,
			'costo_di_taglio'   => $costo_di_taglio,
			'costo_lavorazioni' => $costo_lavorazioni,
			'altri_costi'       => $altri_costi,
			'total'             => $total,
			'dim_x'             => $dim_x,
			'dim_y'             => $dim_y,
			'peso_specifico'    => $peso_specifico,
			'prezzo_materiale_al_kg' => $prezzo_materiale_al_kg,
			'prezzo_rottame_al_kg' => $prezzo_rottame_al_kg,
			'ricarico_materiale' => $ricarico_materiale,
			'pathto'            => $pathto,
		);
		wp_send_json_success( $response_data );
	} else {
		wp_send_json_error( 'Not all params are set correctly' );
	}
}

add_action( 'wp_ajax_genera_preventivo', 'genera_preventivo' );
add_action( 'wp_ajax_nopriv_genera_preventivo', 'genera_preventivo' );


add_action( 'admin_menu', 'register_sidertaglio_preventivi_automatici_settings' );
/**
 * Adds menu and submenu for plugin settings.
 *
 * @since 1.0.0
 * @return void
 */
function register_sidertaglio_preventivi_automatici_settings() {
	add_menu_page( esc_html__( 'AI Preventivi Automatici Settings', 'sidertaglio-preventivi-automatici-settings' ), esc_html__( 'AI Preventivi Automatici Settings', 'sidertaglio-preventivi-automatici-settings' ), 'manage_options', 'sidertaglio-preventivi-automatici-settings', 'sidertaglio_settings_form', '', 900 );
}
add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'sidertaglio_preventivi_automatici_settings_link' );

/**
 * Generates link for menu and submenu plugin's settings page in Plugins admin page.
 *
 * @since 1.0.0
 *
 * @param array $actions Actions.
 * @return array
 */
function sidertaglio_preventivi_automatici_settings_link( $actions ) {
	$actions[] = '<a href="' . admin_url( 'admin.php?page=sidertaglio-preventivi-automatici-settings' ) . '">Impostazioni</a>';
	return $actions;
}

/**
 * Initialize settings for sidertaglio preventivi automatici plugin
 *
 * @since 1.0.0
 * @return void
 */
function sidertaglio_preventivi_automatici_settings_init() {
	register_setting( 'sidertaglio_preventivi_automatici_options_group', 'sidertaglio_preventivi_automatici_options', 'sidertaglio_preventivi_automatici_sanitize_options' );

	add_settings_section(
		'sidertaglio_preventivi_automatici_settings_section',
		'Global Settings',
		'sidertaglio_preventivi_automatici_settings_section_callback',
		'sidertaglio_preventivi_automatici_settings'
	);

	add_settings_field(
		'offset_percentuale',
		'Offset percentuale',
		'sidertaglio_preventivi_automatici_offset_percentuale_callback',
		'sidertaglio_preventivi_automatici_settings',
		'sidertaglio_preventivi_automatici_settings_section',
		array( 'label_for' => 'offset_percentuale' )
	);

	add_settings_field(
		'ricarico_materiale_globale',
		'Ricarico materiale globale',
		'sidertaglio_preventivi_automatici_ricarico_materiale_callback',
		'sidertaglio_preventivi_automatici_settings',
		'sidertaglio_preventivi_automatici_settings_section',
		array( 'label_for' => 'ricarico_materiale_globale' )
	);
}
add_action( 'admin_init', 'sidertaglio_preventivi_automatici_settings_init' );

/**
 * Callback to plugin's settings section
 *
 * @since 1.0.0
 * @return void
 */
function sidertaglio_preventivi_automatici_settings_section_callback() {
	echo '<p>Configure the global settings for Sidertaglio Preventivi Automatici.</p>';
}

/**
 * Callback to plugin's settings "offset percentuale" section
 *
 * @since 1.0.0
 * @param array $args Args.
 * @return void
 */
function sidertaglio_preventivi_automatici_offset_percentuale_callback( $args ) {
	$options = get_option( 'sidertaglio_preventivi_automatici_options' );
	?>
	<input id="<?php echo esc_attr( $args['label_for'] ); ?>"
			name="sidertaglio_preventivi_automatici_options[<?php echo esc_attr( $args['label_for'] ); ?>]"
			type="number"
			value="<?php echo isset( $options[ $args['label_for'] ] ) ? intval( $options[ $args['label_for'] ] ) : ''; ?>"
			min="0"
			step="0.01">
	<?php
}

/**
 * Callback to plugin's settings "ricarico materiale" section
 *
 * @since 1.0.0
 * @param array $args Args.
 * @return void
 */
function sidertaglio_preventivi_automatici_ricarico_materiale_callback( $args ) {
	$options = get_option( 'sidertaglio_preventivi_automatici_options' );
	?>
	<input id="<?php echo esc_attr( $args['label_for'] ); ?>"
			name="sidertaglio_preventivi_automatici_options[<?php echo esc_attr( $args['label_for'] ); ?>]"
			type="number"
			value="<?php echo isset( $options[ $args['label_for'] ] ) ? intval( $options[ $args['label_for'] ] ) : ''; ?>"
			min="0"
			step="0.01">
	<?php
}

/**
 * Sanitize plugins setting's options
 *
 * @since 1.0.0
 * @param array $options Options.
 * @return array
 */
function sidertaglio_preventivi_automatici_sanitize_options( $options ) {
	$sanitized_options = array();

	if ( isset( $options['offset_percentuale'] ) ) {
		$sanitized_options['offset_percentuale'] = absint( $options['offset_percentuale'] );
	}

	if ( isset( $options['ricarico_materiale_globale'] ) ) {
		$sanitized_options['ricarico_materiale_globale'] = absint( $options['ricarico_materiale_globale'] );
	}

	return $sanitized_options;
}

/**
 * Retrieve plugin's settings
 *
 * @since 1.0.0
 * @param array $setting_name Setting name.
 * @return array
 */
function get_sidertaglio_preventivi_automatici_setting( $setting_name ) {
	$options = get_option( 'sidertaglio_preventivi_automatici_options' );
	return isset( $options[ $setting_name ] ) ? $options[ $setting_name ] : null;
}




/**
 * Display a Sidertaglio help tip.
 *
 * @since 1.0.0
 *
 * @param string $tip Help tip text.
 * @param bool   $allow_html Allow sanitized HTML if true or escape.
 * @return array
 */
function sidertaglio_help_tip( $tip, $allow_html = false ) {
	if ( $allow_html ) {
		$sanitized_tip = htmlspecialchars(
			wp_kses(
				html_entity_decode( $tip ?? '' ),
				array(
					'br'     => array(),
					'em'     => array(),
					'strong' => array(),
					'small'  => array(),
					'span'   => array(),
					'ul'     => array(),
					'li'     => array(),
					'ol'     => array(),
					'p'      => array(),
				)
			)
		);
	} else {
		$sanitized_tip = esc_attr( $tip );
	}

	return '<span class="sidertaglio-help-tip" tabindex="0" aria-label="' . $sanitized_tip . '" data-tip="' . $sanitized_tip . '"></span>';
}
?>