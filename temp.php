<?php
	$materiali = get_all_materiali();
	?>
		<div class="woocommerce_form_wrapper">
			
			<h3>Materiali</h3>

			<?php
			if ( ! empty( $materiali ) ) {
				foreach ( $materiali as $materiale ) {
					$id     = $materiale['id'];
					$data   = $materiale['data'];
					$peso   = $data['peso_specifico'];
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
										<input class='saveMaterialButton' value="Salva" type="button" id="<?php echo esc_attr( $id . '_save' ); ?>">
									</p>
									<?php $nonce = wp_create_nonce( 'delete_materiale' ); ?>
									<input type="hidden" class="deleteNonce" name="_wpnonce" value="<?php echo esc_attr( $nonce ); ?>" />
									<p class="button-right">
										<input class='deleteMaterialButton' value="Elimina" type="button"  id="<?php echo esc_attr( $id . '_delete' ); ?>">
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
			<ul style="display:none;" class="dropdownMenu" id="dropdownNewMaterialeMenu">

			<!-- First DropDown -->
			<li class="firstDropDown">
				<?php $nonce = wp_create_nonce( 'save_materiale' ); ?>
				<input type="hidden" id="_wpMaterialnonce" name="_wpMaterialnonce" value="<?php echo esc_attr( $nonce ); ?>" />
				<label for="newMaterialId">Codice materiale:
					<?php echo wc_help_tip( 'Inserire un codice identificativo del materiale', false ); ?>
				</label>
				<input type="text" placeholder="e.g.: A1B2C3..." id="newMaterialId"/>

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