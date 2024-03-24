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
define( 'SPA_VERSION', '1.3' );
define( 'SPA_FILE', __FILE__ );
define( 'SPA_PATH', plugin_dir_path( SPA_FILE ) );
define( 'SPA_URL', plugin_dir_url( SPA_FILE ) );

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
			<input type="text" name="country" id="country" value="<?php echo esc_attr( get_the_author_meta( 'vatcode', $user->ID ) ); ?>" class="regular-text" /><br />
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
			<form class="login-form" action="<?php echo esc_url( wp_login_url( $redirect_url) ); ?>" method="post">
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
		<h3 style="text-align:center;">Ricevi il tuo preventivo in tempo reale</h3>
		<!-- Dropdown Menu -->
		<ul class="dropdownMenu" id="dropdownMenu">

			<!-- First DropDown -->
			<li class="firstDropDown">
				<?php $nonce = wp_create_nonce( 'generate_pdf' ); ?>
				<input type="hidden" id="_wpnonce" name="_wpnonce" value="<?php echo esc_attr( $nonce ); ?>" />
				<div class="shapeWrapper">
				<div class="selectForma">
					<label for="forma">Scegli una forma tra le seguenti:</label>
					<select name="forma" id="forma">
					<option value="">Seleziona una forma...</option>
					<option value="quadrato">Quadrato</option>
					<option value="rettangolare">Rettangolare</option>
					<option value="cerchio">Cerchio</option>
					</select>
				</div>
				<div class="verticalDivider">
					<div class="dashedDivider"> </div>
					<span>OPPURE</span>
					<div class="dashedDivider"> </div>
				</div>
				<div class="uploadForma">
					<label for="uploadSVG">Carica il file SVG della tua figura:</label>
					<input type="file" id="uploadSVG" name="uploadSVG">
				</div>
				</div>
				
				<div id="dimensioniQuadrato" class="campoDimensioni">
					<label for="lato">Lato:</label>
					<input type="number" id="lato" name="lato">
					<br/>
				</div>

				<div id="dimensioniRettangolo" class="campoDimensioni">
					<label for="larghezza">Larghezza:</label>
					<input type="number" id="larghezza" name="larghezza">
					<label for="altezza">Altezza:</label>
					<input type="number" id="altezza" name="altezza">
					<br/>
				</div>

				<div id="dimensioniCerchio" class="campoDimensioni">
					<label for="raggio">Raggio:</label>
					<input type="number" id="raggio" name="raggio">
					<br/>
				</div>

				<!-- <div id="dimensioniCrescente" class="campoDimensioni">
					<label for="raggioGrande">Raggio del cerchio grande (per la mezzaluna):</label>
					<input type="number" id="raggioGrande" name="raggioGrande">

					<label for="raggioPiccolo">Raggio del cerchio piccolo (per la mezzaluna):</label>
					<input type="number" id="raggioPiccolo" name="raggioPiccolo">

					<label for="posizionePiccolo">Posizione del cerchio piccolo (da 0 a 1):</label>
					<input type="number" id="posizionePiccolo" name="posizionePiccolo" step="0.1" min="0" max="1">
				</div>
				<br/> -->

				<label for="materiale">Materiale:</label>
				<br/>
				<select name="materiale" id="materiale">
					<option value="">Seleziona un materiale</option>
					<?php
					foreach ( $materiali as $materiale ) {
						?>
						<option value="<?php echo esc_attr( $materiale['parent_id'] ); ?>">
							<?php echo esc_html( strtoupper( $materiale['parent_id'] ) ); ?>
						</option>
						<?php
					}
					?>
				</select>
				<br/>

				<label for="spessore">Spessore:</label>
				<select name="spessore" id="spessore" disabled>
					<option value="">Seleziona uno spessore</option>
					<?php
					foreach ( $materiali as $materiale ) {
						foreach ( $materiale['children'] as $child_id => $child_data ) {
							?>
							<?php $spessore = substr( $child_id, strlen( $materiale['parent_id'] ) ); ?>
							<option class="<?php echo esc_attr( $materiale['parent_id'] ); ?>" value="<?php echo esc_attr( $spessore ); ?>" style="display: none;">
								<?php echo esc_html( $spessore ); ?>
							</option>
							<?php
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

				<label for="quantità">Quantità:</label>
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
		
			<h3>Macchine da taglio</h3>

			<?php
			if ( ! empty( $macchine ) ) {
				foreach ( $macchine as $macchina ) {
					$id                 = $macchina['id'];
					$data               = $macchina['data'];
					$name               = $data['name'];
					$offset             = $data['offset'];
					$offset_percentuale = $data['offset_percentuale'];
					$spessore           = $data['spessore'];
					$v_taglio           = $data['v_taglio'];
					$costo_orario       = $data['costo_orario'];
					$numero_di_canne    = $data['numero_di_canne'];
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

									<label for="<?php echo esc_attr( $id . '_offset_percentuale' ); ?>">Percentuale di materiale da lasciare:</label>
									<input type="number" class="offset_percentuale" value="<?php echo esc_attr( $offset_percentuale ); ?>" id="<?php echo esc_attr( $id . '_offset_percentuale' ); ?>"/>
						
									<br/>
									
									<label for="<?php echo esc_attr( $id . '_spessore' ); ?>">Spessore massimo tagliabile in millimetri:</label>
									<input type="number" class="spessore" value="<?php echo esc_attr( $spessore ); ?>" id="<?php echo esc_attr( $id . '_spessore' ); ?>"/>
						
									<br/>

									<label for="<?php echo esc_attr( $id . '_v_taglio' ); ?>">Velocità di taglio in millimetri/secondo:</label>
									<input type="number" class="v_taglio" value="<?php echo esc_attr( $v_taglio ); ?>" id="<?php echo esc_attr( $id . '_v_taglio' ); ?>"/>
						
									<br/> 

									<label for="<?php echo esc_attr( $id . '_costo_orario' ); ?>">Costo orario in €/ora:</label>
									<input type="number" class="costo_orario" value="<?php echo esc_attr( $costo_orario ); ?>" id="<?php echo esc_attr( $id . '_costo_orario' ); ?>"/>
						
									<br/>

									<label for="<?php echo esc_attr( $id . '_numero_di_canne' ); ?>">Numero di canne disponibili:</label>
									<input type="number" class="numero_di_canne" value="<?php echo esc_attr( $numero_di_canne ); ?>" id="<?php echo esc_attr( $id . '_numero_di_canne' ); ?>"/>
						
									<br/>
									
								</li>

								<!-- Save Button -->
								<li class="saveButtonLi">
									<?php $nonce = wp_create_nonce( 'save_macchina' ); ?>
									<input type="hidden" class="saveNonce" name="_wpnonce" value="<?php echo esc_attr( $nonce ); ?>" />
									<p class="button-left">
										<input class='saveMachineButton' value="Salva" type="button" id="<?php echo esc_attr( $id . '_save' ); ?>">
									</p>
									<?php $nonce = wp_create_nonce( 'delete_macchina' ); ?>
									<input type="hidden" class="deleteNonce" name="_wpnonce" value="<?php echo esc_attr( $nonce ); ?>" />
									<p class="button-right">
										<input class='deleteMachineButton' value="Elimina" type="button"  id="<?php echo esc_attr( $id . '_delete' ); ?>">
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

				<label for="newOffset">Larghezza del taglio in millimetri:</label>
				<input type="number" placeholder="e.g.: 15" id="newOffset"/>
				
				<br/>

				<label for="newOffsetPercentuale">Percentuale di materiale da lasciare:</label>
				<input type="number" placeholder="e.g.: 3" id="newOffsetPercentuale"/>
				
				<br/>

				<label for="newSpessore">Spessore massimo tagliabile in millimetri:</label>
				<input type="number" placeholder="e.g.: 100" id="newSpessore"/>
				
				<br/>

				<label for="newVTaglio">Velocità di taglio in millimetri/secondo:</label>
				<input type="number" placeholder="e.g.: 50" id="newVTaglio"/>
				
				<br/> 

				<label for="newCostoOrario">Costo orario in €/ora:</label>
				<input type="number" placeholder="e.g.: 250" id="newCostoOrario"/>
				
				<br/>

				<label for="newNumeroDiCanne">Numero di canne disponibili:</label>
				<input type="number" placeholder="e.g.: 6" id="newNumeroDiCanne"/>
				
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
					$parent_id = $materiale['parent_id'];
					$children  = $materiale['children'];
					?>
					<div class="parent-token-row">
						<div class="handle" id="<?php echo esc_attr( $parent_id ); ?>">
							<ul class="closed-token">
								<li class="li-field-parent-label">
									<strong>
										<span><?php echo esc_html( $parent_id ); ?></span>
									</strong>
								</li>
							</ul>
						</div>

						<div class="child-list">
							<?php
							foreach ( $children as $child_id => $child_data ) {
								$peso     = $child_data['peso_specifico'];
								$prezzo   = $child_data['prezzo_kilo'];
								$spessore = substr( $child_id, strlen( $parent_id ) );
								$ricarico = $child_data['ricarico_materiale'];
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
												<label for="<?php echo esc_attr( $child_id . '_id' ); ?>">Materiale:</label>
												<input readonly type="text" class="id" value="<?php echo esc_attr( $parent_id ); ?>" id="<?php echo esc_attr( $child_id . '_id' ); ?>"/>
												</br>

												<label for="<?php echo esc_attr( $child_id . '_spessore' ); ?>">Spessore:</label>
												<input readonly type="number" class="spessore" value="<?php echo esc_attr( $spessore ); ?>" id="<?php echo esc_attr( $child_id . '_spessore' ); ?>"/>
												</br>

												<label for="<?php echo esc_attr( $child_id . '_peso' ); ?>">Peso specifico in kg/m<sup>3</sup>:</label>
												<input type="number" class="peso" value="<?php echo esc_attr( $peso ); ?>" id="<?php echo esc_attr( $child_id . '_peso' ); ?>"/>
												</br>

												<label for="<?php echo esc_attr( $child_id . '_prezzo' ); ?>">Prezzo al kg:</label>
												<input type="number" class="prezzo" value="<?php echo esc_attr( $prezzo ); ?>" id="<?php echo esc_attr( $child_id . '_prezzo' ); ?>"/>
												</br>

												<label for="<?php echo esc_attr( $child_id . '_ricarico' ); ?>">Ricarico percentuale sul prezzo:</label>
												<input type="number" class="ricarico" value="<?php echo esc_attr( $ricarico ); ?>" id="<?php echo esc_attr( $child_id . '_ricarico' ); ?>"/>
												
												<br/>
											</li>

											<li class="saveButtonLi">
												<?php $nonce = wp_create_nonce( 'save_materiale' ); ?>
												<input type="hidden" class="saveNonce" name="_wpnonce" value="<?php echo esc_attr( $nonce ); ?>" />
												<p class="button-left">
													<input class='saveMaterialButton' value="Salva" type="button" id="<?php echo esc_attr( $child_id . '_save' ); ?>">
												</p>
												<?php $nonce = wp_create_nonce( 'delete_materiale' ); ?>
												<input type="hidden" class="deleteNonce" name="_wpnonce" value="<?php echo esc_attr( $nonce ); ?>" />
												<p class="button-right">
													<input class='deleteMaterialButton' value="Elimina" type="button" id="<?php echo esc_attr( $child_id . '_delete' ); ?>">
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

				<label for="newPrezzo">Prezzo al kg:</label>
				<input type="number" placeholder="e.g.: 20" id="newPrezzo"/>
				
				<br/>

				<label for="newRicarico">Ricarico percentuale sul prezzo:</label>
				<input type="number" placeholder="e.g.: 20" id="newRicarico"/>
				
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
										<input class='savePartnershipButton' value="Salva" type="button" id="<?php echo esc_attr( $id . '_save' ); ?>">
									</p>
									<?php $nonce = wp_create_nonce( 'delete_partnership_level' ); ?>
									<input type="hidden" class="deleteNonce" name="_wpnonce" value="<?php echo esc_attr( $nonce ); ?>" />
									<p class="button-right">
										<input class='deletePartnershipButton' value="Elimina" type="button"  id="<?php echo esc_attr( $id . '_delete' ); ?>">
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
					$id          = $lavorazione['id'];
					$data        = $lavorazione['data'];
					$percentuale = $lavorazione['costo'];
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
										<input class='saveLavorazioneButton' value="Salva" type="button" id="<?php echo esc_attr( $id . '_save' ); ?>">
									</p>
									<?php $nonce = wp_create_nonce( 'delete_lavorazione' ); ?>
									<input type="hidden" class="deleteNonce" name="_wpnonce" value="<?php echo esc_attr( $nonce ); ?>" />
									<p class="button-right">
										<input class='deleteLavorazioneButton' value="Elimina" type="button"  id="<?php echo esc_attr( $id . '_delete' ); ?>">
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
			$child_materials = get_option( $option_name );
			$saved_data[]    = array(
				'parent_id' => $parent_id,
				'children'  => $child_materials,
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
	if ( isset( $_POST['id'] ) && isset( $_POST['spessore'] ) ) {

		$array_id    = 'sidertaglio_materiale_' . sanitize_text_field( wp_unslash( $_POST['id'] ) );
		$material_id = sanitize_text_field( wp_unslash( $_POST['id'] ) ) . sanitize_text_field( wp_unslash( $_POST['spessore'] ) );
		$materials   = get_option( $array_id, array() );
		if ( isset( $materials[ $material_id ] ) ) {
			unset( $materials[ $material_id ] );
			if ( empty( $materials ) ) {
				delete_option( $array_id );
			} else {
				update_option( $array_id, $materials );
			}
		}
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
 * Deletes lavorazione given its id.
 *
 * @since 1.0.0
 * @return void
 */
function delete_lavorazione() {
	check_ajax_referer( 'delete_lavorazione', 'security' );
	if ( isset( $_POST['id'] ) ) {
		$id = 'sidertaglio_lavorazione_' . sanitize_text_field( wp_unslash( $_POST['id'] ) );
		delete_option( $id );
	}
	wp_die();
}

add_action( 'wp_ajax_delete_lavorazione', 'delete_lavorazione' );
add_action( 'wp_ajax_nopriv_delete_lavorazione', 'delete_lavorazione' );

/**
 * Stores a new machine as option.
 *
 * @since 1.0.0
 * @return void
 */
function save_macchina() {
	check_ajax_referer( 'save_macchina', 'security' );
	if ( isset( $_POST['id'] ) && isset( $_POST['name'] ) && isset( $_POST['offset'] ) && isset( $_POST['offset_percentuale'] ) && isset( $_POST['spessore'] ) && isset( $_POST['v_taglio'] ) && isset( $_POST['costo_orario'] ) && isset( $_POST['numero_di_canne'] ) ) {
		$id = 'sidertaglio_macchina_' . strtoupper( sanitize_text_field( wp_unslash( $_POST['id'] ) ) );
		update_option(
			$id,
			array(
				'name'               => sanitize_text_field( wp_unslash( $_POST['name'] ) ),
				'offset'             => sanitize_text_field( wp_unslash( $_POST['offset'] ) ),
				'offset_percentuale' => sanitize_text_field( wp_unslash( $_POST['offset_percentuale'] ) ),
				'spessore'           => sanitize_text_field( wp_unslash( $_POST['spessore'] ) ),
				'v_taglio'           => sanitize_text_field( wp_unslash( $_POST['v_taglio'] ) ),
				'costo_orario'       => sanitize_text_field( wp_unslash( $_POST['costo_orario'] ) ),
				'numero_di_canne'    => sanitize_text_field( wp_unslash( $_POST['numero_di_canne'] ) ),
			)
		);
	}
	wp_die();
}

add_action( 'wp_ajax_save_macchina', 'save_macchina' );
add_action( 'wp_ajax_nopriv_save_macchina', 'save_macchina' );

/**
 * Stores a new material as option.
 *
 * @since 1.0.0
 * @return void
 */
function save_materiale() {
	check_ajax_referer( 'save_materiale', 'security' );
	if ( isset( $_POST['id'] ) && isset( $_POST['spessore'] ) && isset( $_POST['peso_specifico'] ) && isset( $_POST['prezzo_kilo'] ) && isset( $_POST['ricarico_materiale'] ) ) {
		$array_id    = 'sidertaglio_materiale_' . sanitize_text_field( wp_unslash( $_POST['id'] ) );
		$material_id = strtolower( sanitize_text_field( wp_unslash( $_POST['id'] ) ) ) . sanitize_text_field( wp_unslash( $_POST['spessore'] ) );
		$materials   = get_option( $array_id );
		if ( false === $materials ) {
			$materials = array();
		}
		$materials[ $material_id ] = array(
			'peso_specifico'     => sanitize_text_field( wp_unslash( $_POST['peso_specifico'] ) ),
			'prezzo_kilo'        => sanitize_text_field( wp_unslash( $_POST['prezzo_kilo'] ) ),
			'ricarico_materiale' => sanitize_text_field( wp_unslash( $_POST['ricarico_materiale'] ) ),
		);
		update_option( $array_id, $materials );
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
 * Generate a PDF with the estimate of cost for a metal cut.
 *
 * @since 1.0.0
 * @return FILE
 */
function genera_preventivo() {
	check_ajax_referer( 'genera_preventivo', 'security' );
	if ( isset( $_POST['materiale'] ) && isset( $_POST['spessore'] ) && isset( $_POST['dim_x'] ) && isset( $_POST['dim_y'] ) && isset( $_POST['quantita'] ) && isset( $_POST['superfice'] ) && isset( $_POST['perimetro'] ) && isset( $_POST['p_reale'] ) && isset( $_POST['nested'] ) && isset( $_POST['lavorazioni'] ) ) {
		/**
		 * Initialize variables from web
		 */
		$perimetro                    = sanitize_text_field( wp_unslash( $_POST['perimetro'] ) );
		$superfice                    = sanitize_text_field( wp_unslash( $_POST['superfice'] ) );
		$materiale                    = sanitize_text_field( wp_unslash( $_POST['materiale'] ) );
		$spessore                     = sanitize_text_field( wp_unslash( $_POST['spessore'] ) );
		$dim_x                        = sanitize_text_field( wp_unslash( $_POST['dim_x'] ) );
		$dim_y                        = sanitize_text_field( wp_unslash( $_POST['dim_y'] ) );
		$quantita                     = sanitize_text_field( wp_unslash( $_POST['quantita'] ) );
		$p_reale                      = sanitize_text_field( wp_unslash( $_POST['p_reale'] ) );
		$nested                       = rest_sanitize_boolean( sanitize_text_field( wp_unslash( $_POST['nested'] ) ) );
		$lavorazioni_richieste_keys   = array_keys( sanitize_text_field( wp_unslash( $_POST['lavorazioni'] ) ) );
		$lavorazioni_richieste_keys   = array_map( 'sanitize_key', $lavorazioni_richieste_keys );
		$lavorazioni_richieste_values = array_values( sanitize_text_field( wp_unslash( $_POST['lavorazioni'] ) ) );
		$lavorazioni_richieste_values = array_map( 'rest_sanitize_boolean', $lavorazioni_richieste );
		$lavorazioni_richieste        = array_combine( $lavorazioni_richieste_keys, $lavorazioni_richieste_values );
		$altri_costi                  = 0;
		$pezzi_grezzi                 = 0;
		$k3                           = true;
		$k4                           = false;

		/**
		 * Checks if user is logged in
		 */
		if ( ! is_user_logged_in() ) {
			wp_send_json_error( 'User not logged in' );
		}

		/**
		 * Chooses the right machine for the job
		 */
		$macchine = get_all_macchine();
		if ( ! empty( $macchine ) ) {
			foreach ( $macchine as $macchina ) {
				$id                = $macchina['id'];
				$data              = $macchina['data'];
				$name              = $data['name'];
				$offset            = $data['offset'];
				$spessore_macchina = $data['spessore'];

				if ( $spessore <= $spessore_macchina ) {
					if ( ! isset( $macchina_scelta ) ) {
						if ( $macchina_scelta['data']['offset'] > $offset ) {
							$macchina_scelta = $macchina;
						}
					} else {
						$macchina_scelta = $macchina;
					}
				}
			}
		}
		$id                         = $macchina_scelta['id'];
		$machine_data               = $macchina_scelta['data'];
		$machine_name               = $machine_data['name'];
		$machine_offset             = $machine_data['offset'];
		$machine_offset_percentuale = $machine_data['offset_percentuale'];
		$machine_v_taglio           = $machine_data['v_taglio'];
		$machine_costo_orario       = $machine_data['costo_orario'];
		$numero_canne               = $machine_data['numero_canne'];

		/**
		 * Retrieves users priviligies by role given
		 */
		$user_id              = get_current_user_id();
		$partnership_level    = get_user_meta( $user_id, 'partnership_level', true );
		$partnership_id       = 'sidertaglio_materiale_' . strtolower( $partnership_level );
		$partnership_details  = get_option( $partnership_id );
		$ricarico_partnership = $partnership_details['percentage'];
		$prezzo_rottame_al_kg = $partnership_details['rottame'];

		/**
		 * Retrieves material details
		 */
		$materiale_id      = 'sidertaglio_materiale_' . $materiale;
		$materiale_details = get_option( $materiale_id );
		if ( ! empty( $materiale_details ) ) {
			$children = $materiale_details['children'];
			foreach ( $children as $child_id => $child_data ) {
				if ( 0 === strcmp( $child_id, $materiale . $spessore ) ) {
					$peso_specifico         = $child_data['peso_specifico'];
					$prezzo_materiale_al_kg = $child_data['prezzo_kilo'];
					$ricarico_materiale     = $child_data['ricarico_materiale'];
				}
			}
		}

		/**
		 * Retrieves lavorazioni details
		 */
		$lavorazioni      = get_all_lavorazioni();
		$lavorazioni_temp = array();
		if ( ! empty( $lavorazioni ) ) {
			foreach ( $lavorazioni as $lavorazione ) {
				$lavorazioni_temp[ strtolower( $lavorazione['id'] ) ] = $lavorazione['data'];
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
		if ( $p_quadrotto > 0 ) {
			if ( $p_reale / $p_quadrotto <= 30 ) {
				$p_utilizzato = $p_reale;
			} elseif ( $p_reale / $p_quadrotto <= 60 ) {
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
			$costo_materiale = ( ( $p_quadrotto_plus_10 * ( $prezzo_materiale_al_kg + $prezzo_materiale_al_kg / 100 * $ricarico_materiale ) ) - ( $p_rottame * $prezzo_rottame_al_kg ) );
		}

		/**
		 * Calculates the price of usage for the machine
		 */
		$tempo_di_taglio = $perimetro / $machine_v_taglio;
		if ( 0 === strcmp( $machine_name, 'OSSIT' ) ) {
			if ( 0 === ( $quantita % $numero_canne ) ) {
				$costo_di_taglio = ( ( $tempo_di_taglio * ( $machine_costo_orario / 60 ) ) * intdiv( $quantita, $numero_canne ) ) / $quantita;
			} else {
				$costo_di_taglio = ( ( $tempo_di_taglio * ( $machine_costo_orario / 60 ) ) * ( intdiv( $quantita, $numero_canne ) + 1 ) ) / $quantita;
			}
		} else {
			$costo_di_taglio = $tempo_di_taglio * ( $machine_costo_orario / 60 );
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
				$total += ( $costo_materiale + $costo_di_taglio + $altri_costi ) + ( ( $costo_materiale + $costo_di_taglio ) / 100 * $ricarico_partnership );
			} elseif ( $p_reale <= 20 ) {
				$total += ( $costo_materiale + $costo_materiale / 100 * $ricarico_partnership ) + ( $costo_di_taglio + $altri_costi ) + ( $costo_di_taglio + $altri_costi ) / 2 + ( ( $costo_lavorazioni ) + ( ( $costo_lavorazioni ) / 100 * 10 ) );
			} else {
				$total += ( $costo_materiale + $costo_materiale / 100 * $ricarico_partnership ) + ( $costo_di_taglio + $altri_costi ) + ( $costo_di_taglio + $altri_costi ) + ( ( $costo_lavorazioni ) + ( ( $costo_lavorazioni ) / 100 * 10 ) );
			}

			if ( $k3 ) {
				$total *= 1.025;
			}

			if ( $k4 ) {
				$total *= 1.02;
			}
		}
		if ( $total - round( $total, 0, PHP_ROUND_HALF_DOWN ) > 0.5 ) {
			$total = round( $total, 0, PHP_ROUND_HALF_DOWN ) + 1;
		} else {
			$total = round( $total, 0, PHP_ROUND_HALF_DOWN ) + 0.5;
		}
		$total *= $quantita;
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
	add_menu_page( esc_html__( 'Preventivi Automatici Settings', 'sidertaglio-preventivi-automatici-settings' ), esc_html__( 'Preventivi Automatici Settings', 'sidertaglio-preventivi-automatici-settings' ), 'manage_options', 'sidertaglio-preventivi-automatici-settings', 'sidertaglio_settings_form', '', 900 );
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