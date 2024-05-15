jQuery(document).ready(function () {

    jQuery('.sidertaglio-help-tip').tipTip({
        'attribute': 'data-tip',
        'fadeIn':    50,
        'fadeOut':   50,
        'delay':     200,
    });

    jQuery(".parent-token-row .li-field-parent-label").click(function () {
        // Find the corresponding settings div for the clicked label
        var children = jQuery(this).closest(".parent-token-row").find(".child-list");

        // Toggle the display of the settings
        children.slideToggle(300);
        jQuery(this).toggleClass("active");
    });

    jQuery(".token-row .li-field-label").click(function () {
        // Find the corresponding settings div for the clicked label
        var settings = jQuery(this).closest(".token-row").find(".settings");

        // Toggle the display of the settings
        settings.slideToggle(300);
        jQuery(this).toggleClass("active");
    });

    jQuery("#addMachineBtn").click(function (){
        jQuery("#dropdownNewMacchinaMenu").slideToggle(300);
    });
    
    jQuery("#addMaterialBtn").click(function (){
        jQuery("#dropdownNewMaterialeMenu").slideToggle(300);
    });

    jQuery("#addPartnershipBtn").click(function (){
        jQuery("#dropdownNewPartnershipMenu").slideToggle(300);
    });

    jQuery("#addLavorazioneBtn").click(function (){
        jQuery("#dropdownNewLavorazioneMenu").slideToggle(300);
    });

    jQuery("#saveMachineBtn").click(async function () {
        // Get values from input fields
        var newId = jQuery("#newMachineId").val();
        var newName = jQuery("#newName").val();
        var newOffset = jQuery("#newOffset").val();
        var newSpessore = jQuery("#newSpessore").val();
        var newSpessoreMax = jQuery("#newSpessoreMax").val();
        var newVTaglio = jQuery("#newVTaglio").val();
        var newCostoOrario = jQuery("#newCostoOrario").val();
        var newNumeroDiCanne = jQuery("#newNumeroDiCanne").val();
        var newInnesco = jQuery("#newInnesco").val();
        var nonce = jQuery("#_wpMachinenonce").val();

        // Check if all required fields are filled
        if (!newId || !newName || !newOffset || !newSpessore || !newCostoOrario || !newVTaglio || !newNumeroDiCanne || !newInnesco ) {
            alert("Please fill in all required fields.");
            return;
        }

        
        // Create an object to store the data
        var tokenData = {
            action: 'save_macchina',
            id: newId,
            name: newName,
            offset: newOffset,
            spessore: newSpessore,
            spessore_max: newSpessoreMax,
            v_taglio: newVTaglio,
            costo_orario: newCostoOrario,
            numero_di_canne: newNumeroDiCanne,
            innesco: newInnesco,
            security: nonce
        };
        const ajaxurl = '/wp-admin/admin-ajax.php';

        // Save the data (you can customize this part to send the data to your server or store it in your desired format)
        console.log("Token Data:", tokenData);

        // Clear the input fields and hide the second dropdown
        jQuery("#newMachineId").val("");
        jQuery("#newName").val("");
        jQuery("#newOffset").val("");
        jQuery("#newSpessore").val("");
        jQuery("#newSpessoreMax").val("");
        jQuery("#newVTaglio").val("");
        jQuery("#newCostoOrario").val("");
        jQuery("#newNumeroDiCanne").val("");
        jQuery("#newInnesco").val("");
        jQuery("#dropdownNewMacchinaMenu").slideToggle(300);
        jQuery("#overlay").fadeIn(300);
        await jQuery.ajax({
            url: ajaxurl, // WordPress AJAX endpoint
            type: 'POST',
            data: tokenData,
            success: function(response) {
                jQuery("#overlay").fadeOut(300);
                console.log(response);
            },
            error: function(error) {
                // Handle the error here
                console.error(error);
            }
        });
        location.reload()

    });

    jQuery("#saveMaterialBtn").click(async function () {
        // Get values from input fields
        var newId = jQuery("#newMaterialId").val();
        var newSpessoreMateriale = jQuery("#newSpessoreMateriale").val();
        var newPeso = jQuery("#newPeso").val();
        var newPrezzo = jQuery("#newPrezzo").val();
        var nonce = jQuery("#_wpMaterialnonce").val();

        // Check if all required fields are filled
        if (!newId || !newPeso || !newPrezzo || !newSpessoreMateriale) {
            alert("Please fill in all required fields.");
            return;
        }

        fullId = newId + newSpessoreMateriale;
        // Create an object to store the data
        var tokenData = {
            action: 'save_materiale',
            id: newId,
            spessore: newSpessoreMateriale,
            peso_specifico: newPeso,
            prezzo_ton: newPrezzo,
            security: nonce
        };
        const ajaxurl = '/wp-admin/admin-ajax.php';

        // Save the data (you can customize this part to send the data to your server or store it in your desired format)
        console.log("Token Data:", tokenData);

        // Clear the input fields and hide the second dropdown
        jQuery("#newMaterialId").val("");
        jQuery("#newPeso").val("");
        jQuery("#newPrezzo").val("");
        jQuery("#dropdownNewMaterialeMenu").slideToggle(300);
        jQuery("#overlay").fadeIn(300);
        await jQuery.ajax({
            url: ajaxurl, // WordPress AJAX endpoint
            type: 'POST',
            data: tokenData,
            success: function(response) {
                // Handle the success response here
                jQuery("#overlay").fadeOut(300);
                console.log(response);
            },
            error: function(error) {
                // Handle the error here
                console.error(error);
            }
        });
        location.reload()

    });

    jQuery("#savePartnershipBtn").click(async function () {
        // Get values from input fields
        var newId = jQuery("#newPartnershipId").val();
        var newPercentuale = jQuery("#newPercentuale").val();
        var newRottame = jQuery("#newRottame").val();
        var nonce = jQuery("#_wpPartnershipnonce").val();

        // Check if all required fields are filled
        if (!newId || !newPercentuale || !newRottame) {
            alert("Please fill in all required fields.");
            return;
        }

        // Create an object to store the data
        var tokenData = {
            action: 'save_partnership_level',
            id: newId,
            percentage: newPercentuale,
            rottame: newRottame,
            security: nonce
        };
        const ajaxurl = '/wp-admin/admin-ajax.php';

        // Save the data (you can customize this part to send the data to your server or store it in your desired format)
        console.log("Token Data:", tokenData);

        // Clear the input fields and hide the second dropdown
        jQuery("#newPartnershipId").val("");
        jQuery("#newPercentuale").val("");
        jQuery("#newRottame").val("");
        jQuery("#dropdownNewPartnershipMenu").slideToggle(300);
        jQuery("#overlay").fadeIn(300);
        await jQuery.ajax({
            url: ajaxurl, // WordPress AJAX endpoint
            type: 'POST',
            data: tokenData,
            success: function(response) {
                // Handle the success response here
                jQuery("#overlay").fadeOut(300);
                console.log(response);
            },
            error: function(error) {
                // Handle the error here
                console.error(error);
            }
        });
        location.reload()

    });

    jQuery("#saveLavorazioneBtn").click(async function () {
        // Get values from input fields
        var newId = jQuery("#newLavorazioneId").val();
        var newCostoLavorazione = jQuery("#newCostoLavorazione").val();
        var nonce = jQuery("#_wpLavorazionenonce").val();

        // Check if all required fields are filled
        if (!newId || !newCostoLavorazione ) {
            alert("Please fill in all required fields.");
            return;
        }

        // Create an object to store the data
        var tokenData = {
            action: 'save_lavorazione',
            id: newId,
            costo: newCostoLavorazione,
            security: nonce
        };
        const ajaxurl = '/wp-admin/admin-ajax.php';

        // Save the data (you can customize this part to send the data to your server or store it in your desired format)
        console.log("Token Data:", tokenData);

        // Clear the input fields and hide the second dropdown
        jQuery("#newLavorazioneId").val("");
        jQuery("#newCostoLavorazione").val("");
        jQuery("#dropdownNewLavorazioneMenu").slideToggle(300);
        jQuery("#overlay").fadeIn(300);
        await jQuery.ajax({
            url: ajaxurl, // WordPress AJAX endpoint
            type: 'POST',
            data: tokenData,
            success: function(response) {
                // Handle the success response here
                jQuery("#overlay").fadeOut(300);
                console.log(response);
            },
            error: function(error) {
                // Handle the error here
                console.error(error);
            }
        });
        location.reload()

    });

    jQuery(".token-row .saveMachineButton").click(async function () {
        // Get values from input fields
        var id = jQuery(this).closest(".child-list").find(".parent_id").val();
        var name = jQuery(this).closest(".child-list").find(".name").val();
        var offset = jQuery(this).closest(".token-row").find(".offset").val();
        var spessore = jQuery(this).closest(".token-row").find(".spessore").val();
        var spessore_max = jQuery(this).closest(".child-list").find(".spessore_max").val();
        var v_taglio = jQuery(this).closest(".token-row").find(".v_taglio").val();
        var costo_orario = jQuery(this).closest(".token-row").find(".costo_orario").val();
        var numero_di_canne = jQuery(this).closest(".child-list").find(".numero_di_canne").val();
        var innesco = jQuery(this).closest(".token-row").find(".innesco").val();
        var nonce = jQuery(this).closest(".token-row").find(".saveNonce").val();
        var tokenData = {
            action: 'save_macchina',
            id: id,
            name: name,
            offset: offset,
            spessore: spessore,
            spessore_max: spessore_max,
            v_taglio: v_taglio,
            costo_orario: costo_orario,
            numero_di_canne: numero_di_canne,
            innesco: innesco,
            security: nonce
        }; 
        console.log("Token Data:", tokenData);
        // Check if all required fields are filled
        if (!id || !name || !offset || !spessore || !spessore_max ||!v_taglio ||!costo_orario ||!numero_di_canne ||!innesco) {
            alert("Please fill in all required fields.");
            return;
        }

        // Create an object to store the data
        var tokenData = {
            action: 'save_macchina',
            id: id,
            name: name,
            offset: offset,
            spessore: spessore,
            spessore_max: spessore_max,
            v_taglio: v_taglio,
            costo_orario: costo_orario,
            numero_di_canne: numero_di_canne,
            innesco: innesco,
            security: nonce
        };
        const ajaxurl = '/wp-admin/admin-ajax.php';

        // Save the data (you can customize this part to send the data to your server or store it in your desired format)
        console.log("Token Data:", tokenData);

        // Clear the input fields and hide the second dropdown
        var settings = jQuery(this).closest(".token-row").find(".settings");
        settings.slideToggle(300)
        jQuery("#overlay").fadeIn(300);
        await jQuery.ajax({
            url: ajaxurl, // WordPress AJAX endpoint
            type: 'POST',
            data: tokenData,
            success: function(response) {
                // Handle the success response here
                jQuery("#overlay").fadeOut(300);
                console.log(response);
            },
            error: function(error) {
                // Handle the error here
                console.error(error);
            }
        });
        location.reload()

    });

    jQuery(".token-row .saveMaterialButton").click(async function () {
        var id = jQuery(this).closest(".child-list").find(".parent_id").val();
        var spessore = jQuery(this).closest(".token-row").find(".spessore").val();
        var peso = jQuery(this).closest(".child-list").find(".peso").val();
        var prezzo = jQuery(this).closest(".token-row").find(".prezzo").val();
        var nonce = jQuery(this).closest(".token-row").find(".saveNonce").val();
    
        if (!id || !spessore || !peso || !prezzo) {
            alert("Please fill in all required fields.");
            return;
        }
    
        var tokenData = {
            action: 'save_materiale',
            id: id,
            spessore: spessore,
            peso_specifico: peso,
            prezzo_ton: prezzo,
            security: nonce
        };
        const ajaxurl = '/wp-admin/admin-ajax.php';
    
        console.log("Token Data:", tokenData);
    
        var settings = jQuery(this).closest(".token-row").find(".settings");
        settings.slideToggle(300);
        jQuery("#overlay").fadeIn(300);
        await jQuery.ajax({
            url: ajaxurl,
            type: 'POST',
            data: tokenData,
            success: function(response) {
                jQuery("#overlay").fadeOut(300);
                console.log(response);
            },
            error: function(error) {
                console.error(error);
            }
        });
        location.reload();
    });

    jQuery(".token-row .savePartnershipButton").click(async function () {
        // Get values from input fields
        var id = jQuery(this).closest(".token-row").find(".id").val();
        var percentuale = jQuery(this).closest(".token-row").find(".percentuale").val();
        var rottame = jQuery(this).closest(".token-row").find(".rottame").val();
        var nonce = jQuery(this).closest(".token-row").find(".saveNonce").val();

        // Check if all required fields are filled
        if (!id || !percentuale || !rottame) {
            alert("Please fill in all required fields.");
            return;
        }

        // Create an object to store the data
        var tokenData = {
            action: 'save_partnership_level',
            id: id,
            percentage: percentuale,
            rottame: rottame,
            security: nonce
        };
        const ajaxurl = '/wp-admin/admin-ajax.php';

        // Save the data (you can customize this part to send the data to your server or store it in your desired format)
        console.log("Token Data:", tokenData);

        // Clear the input fields and hide the second dropdown
        var settings = jQuery(this).closest(".token-row").find(".settings");
        settings.slideToggle(300)
        jQuery("#overlay").fadeIn(300);
        await jQuery.ajax({
            url: ajaxurl, // WordPress AJAX endpoint
            type: 'POST',
            data: tokenData,
            success: function(response) {
                // Handle the success response here
                jQuery("#overlay").fadeOut(300);
                console.log(response);
            },
            error: function(error) {
                // Handle the error here
                console.error(error);
            }
        });
        location.reload()

    });

    jQuery(".token-row .saveLavorazioneButton").click(async function () {
        // Get values from input fields
        var id = jQuery(this).closest(".token-row").find(".id").val();
        var costo = jQuery(this).closest(".token-row").find(".costo").val();
        var nonce = jQuery(this).closest(".token-row").find(".saveNonce").val();

        // Check if all required fields are filled
        if (!id || !costo) {
            alert("Please fill in all required fields.");
            return;
        }

        // Create an object to store the data
        var tokenData = {
            action: 'save_lavorazione',
            id: id,
            costo: costo,
            security: nonce
        };
        const ajaxurl = '/wp-admin/admin-ajax.php';

        // Save the data (you can customize this part to send the data to your server or store it in your desired format)
        console.log("Token Data:", tokenData);

        // Clear the input fields and hide the second dropdown
        var settings = jQuery(this).closest(".token-row").find(".settings");
        settings.slideToggle(300);
        jQuery("#overlay").fadeIn(300);
        await jQuery.ajax({
            url: ajaxurl, // WordPress AJAX endpoint
            type: 'POST',
            data: tokenData,
            success: function(response) {
                // Handle the success response here
                jQuery("#overlay").fadeOut(300);
                console.log(response);
            },
            error: function(error) {
                // Handle the error here
                console.error(error);
            }
        });
        location.reload()

    });

    jQuery(".parent-settings .saveMachineArrayButton").click(async function () {
        // Get values from input fields
        var id = jQuery(this).closest(".parent-settings").find(".parent_id").val();
        var name = jQuery(this).closest(".parent-settings").find(".name").val();
        var spessore_max = jQuery(this).closest(".parent-settings").find(".spessore_max").val();
        var numero_di_canne = jQuery(this).closest(".parent-settings").find(".numero_di_canne").val();
        var nonce = jQuery(this).closest(".parent-settings").find(".saveNonce").val();

        // Check if all required fields are filled
        if (!id || !name || !spessore_max || !numero_di_canne) {
            alert("Please fill in all required fields.");
            return;
        }

        // Create an object to store the data
        var tokenData = {
            action: 'save_array_macchine',
            id: id,
            name: name,
            spessore_max: spessore_max,
            numero_di_canne: numero_di_canne,
            security: nonce
        };
        const ajaxurl = '/wp-admin/admin-ajax.php';

        // Save the data (you can customize this part to send the data to your server or store it in your desired format)
        console.log("Token Data:", tokenData);

        // Clear the input fields and hide the second dropdown
        jQuery("#overlay").fadeIn(300);
        await jQuery.ajax({
            url: ajaxurl, // WordPress AJAX endpoint
            type: 'POST',
            data: tokenData,
            success: function(response) {
                // Handle the success response here
                jQuery("#overlay").fadeOut(300);
                console.log(response);
            },
            error: function(error) {
                // Handle the error here
                console.error(error);
            }
        });
        location.reload()

    });

    jQuery(".parent-settings .saveMaterialArrayButton").click(async function () {
        var id = jQuery(this).closest(".parent-settings").find(".parent_id").val();
        var peso = jQuery(this).closest(".parent-settings").find(".peso").val();
        var nonce = jQuery(this).closest(".parent-settings").find(".saveNonce").val();
    
        if (!id || !peso ) {
            alert("Please fill in all required fields.");
            return;
        }
    
        var tokenData = {
            action: 'save_array_materiali',
            id: id,
            peso_specifico: peso,
            security: nonce
        };
        const ajaxurl = '/wp-admin/admin-ajax.php';
    
        console.log("Token Data:", tokenData);

        jQuery("#overlay").fadeIn(300);
        await jQuery.ajax({
            url: ajaxurl,
            type: 'POST',
            data: tokenData,
            success: function(response) {
                jQuery("#overlay").fadeOut(300);
                console.log(response);
            },
            error: function(error) {
                console.error(error);
            }
        });
        location.reload();
    });

    jQuery(".token-row .deleteMachineButton").click(async function () {
        // Get values from input fields
        var parent_id = jQuery(this).closest(".child-list").find(".parent_id").val();
        var child_id = jQuery(this).closest(".token-row").find(".id").val();
        var nonce = jQuery(this).closest(".token-row").find(".deleteNonce").val();


        // Create an object to store the data
        var tokenData = {
            action: 'delete_macchina',
            parent_id: parent_id,
            child_id: child_id,
            security: nonce
        };
        const ajaxurl = '/wp-admin/admin-ajax.php';

        // Save the data (you can customize this part to send the data to your server or store it in your desired format)
        console.log("Token Data:", tokenData);

        // Clear the input fields and hide the second dropdown
        var settings = jQuery(this).closest(".token-row").find(".settings");
        settings.slideToggle(300);
        var row = jQuery(this).closest(".token-row").find(".li-field-label");
        row.toggleClass('active');
        jQuery("#overlay").fadeIn(300);
        await jQuery.ajax({
            url: ajaxurl, // WordPress AJAX endpoint
            type: 'POST',
            data: tokenData,
            success: function(response) {
                // Handle the success response here
                jQuery("#overlay").fadeOut(300);
                console.log(response);
            },
            error: function(error) {
                // Handle the error here
                console.error(error);
            }
        });
        location.reload()

    });

    jQuery(".token-row .deleteMaterialButton").click(async function () {
        var parent_id = jQuery(this).closest(".child-list").find(".parent_id").val();
        var child_id = jQuery(this).closest(".token-row").find(".spessore").val();
        var nonce = jQuery(this).closest(".token-row").find(".deleteNonce").val();


        // Create an object to store the data
        var tokenData = {
            action: 'delete_materiale',
            parent_id: parent_id,
            child_id: child_id,
            security: nonce
        };
        const ajaxurl = '/wp-admin/admin-ajax.php';

        // Save the data (you can customize this part to send the data to your server or store it in your desired format)
        console.log("Token Data:", tokenData);

        // Clear the input fields and hide the second dropdown
        var settings = jQuery(this).closest(".token-row").find(".settings");
        settings.slideToggle(300);
        var row = jQuery(this).closest(".token-row").find(".li-field-label");
        row.toggleClass('active');
        jQuery("#overlay").fadeIn(300);
        await jQuery.ajax({
            url: ajaxurl, // WordPress AJAX endpoint
            type: 'POST',
            data: tokenData,
            success: function(response) {
                // Handle the success response here
                jQuery("#overlay").fadeOut(300);
                console.log(response);
            },
            error: function(error) {
                // Handle the error here
                console.error(error);
            }
        });
        location.reload()

    });

    jQuery(".parent-settings .deleteMachineArrayButton").click(async function () {
        // Get values from input fields
        var id = jQuery(this).closest(".parent-settings").find(".parent_id").val();
        var nonce = jQuery(this).closest(".parent-settings").find(".deleteNonce").val();


        // Create an object to store the data
        var tokenData = {
            action: 'delete_array_macchine',
            id: id,
            security: nonce
        };
        const ajaxurl = '/wp-admin/admin-ajax.php';

        // Save the data (you can customize this part to send the data to your server or store it in your desired format)
        console.log("Token Data:", tokenData);

        // Clear the input fields and hide the second dropdown
        jQuery("#overlay").fadeIn(300);
        await jQuery.ajax({
            url: ajaxurl, // WordPress AJAX endpoint
            type: 'POST',
            data: tokenData,
            success: function(response) {
                // Handle the success response here
                jQuery("#overlay").fadeOut(300);
                console.log(response);
            },
            error: function(error) {
                // Handle the error here
                console.error(error);
            }
        });
        location.reload()

    });

    jQuery(".parent-settings .deleteMaterialArrayButton").click(async function () {
        var id = jQuery(this).closest(".parent-settings").find(".parent_id").val();
        var nonce = jQuery(this).closest(".parent-settings").find(".deleteNonce").val();


        // Create an object to store the data
        var tokenData = {
            action: 'delete_array_materiali',
            id: id,
            spessore: spessore,
            security: nonce
        };
        const ajaxurl = '/wp-admin/admin-ajax.php';

        // Save the data (you can customize this part to send the data to your server or store it in your desired format)
        console.log("Token Data:", tokenData);

        // Clear the input fields and hide the second dropdown
        jQuery("#overlay").fadeIn(300);
        await jQuery.ajax({
            url: ajaxurl, // WordPress AJAX endpoint
            type: 'POST',
            data: tokenData,
            success: function(response) {
                // Handle the success response here
                jQuery("#overlay").fadeOut(300);
                console.log(response);
            },
            error: function(error) {
                // Handle the error here
                console.error(error);
            }
        });
        location.reload()

    });

    jQuery(".token-row .deletePartnershipButton").click(async function () {
        // Get values from input fields
        var id = jQuery(this).closest(".token-row").find(".id").val();
        var nonce = jQuery(this).closest(".token-row").find(".deleteNonce").val();


        // Create an object to store the data
        var tokenData = {
            action: 'delete_partnership_level',
            id: id,
            security: nonce
        };
        const ajaxurl = '/wp-admin/admin-ajax.php';

        // Save the data (you can customize this part to send the data to your server or store it in your desired format)
        console.log("Token Data:", tokenData);

        // Clear the input fields and hide the second dropdown
        var settings = jQuery(this).closest(".token-row").find(".settings");
        settings.slideToggle(300);
        var row = jQuery(this).closest(".token-row").find(".li-field-label");
        row.toggleClass('active');
        jQuery("#overlay").fadeIn(300);
        await jQuery.ajax({
            url: ajaxurl, // WordPress AJAX endpoint
            type: 'POST',
            data: tokenData,
            success: function(response) {
                // Handle the success response here
                jQuery("#overlay").fadeOut(300);
                console.log(response);
            },
            error: function(error) {
                // Handle the error here
                console.error(error);
            }
        });
        location.reload()

    });

    jQuery(".token-row .deleteLavorazioneButton").click(async function () {
        // Get values from input fields
        var id = jQuery(this).closest(".token-row").find(".id").val();
        var nonce = jQuery(this).closest(".token-row").find(".deleteNonce").val();


        // Create an object to store the data
        var tokenData = {
            action: 'delete_lavorazione',
            id: id,
            security: nonce
        };
        const ajaxurl = '/wp-admin/admin-ajax.php';

        // Save the data (you can customize this part to send the data to your server or store it in your desired format)
        console.log("Token Data:", tokenData);

        // Clear the input fields and hide the second dropdown
        var settings = jQuery(this).closest(".token-row").find(".settings");
        settings.slideToggle(300);
        var row = jQuery(this).closest(".token-row").find(".li-field-label");
        row.toggleClass('active');
        jQuery("#overlay").fadeIn(300);
        await jQuery.ajax({
            url: ajaxurl, // WordPress AJAX endpoint
            type: 'POST',
            data: tokenData,
            success: function(response) {
                // Handle the success response here
                jQuery("#overlay").fadeOut(300);
                console.log(response);
            },
            error: function(error) {
                // Handle the error here
                console.error(error);
            }
        });
        location.reload()

    });

});