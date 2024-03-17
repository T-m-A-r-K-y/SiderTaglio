jQuery(document).ready(function () {

    jQuery('.woocommerce-help-tip').tipTip({
        'attribute': 'data-tip',
        'fadeIn':    50,
        'fadeOut':   50,
        'delay':     200,
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

    jQuery("#saveMachineBtn").click(async function () {
        // Get values from input fields
        var newId = jQuery("#newId").val();
        var newName = jQuery("#newName").val();
        var newOffset = jQuery("#newOffset").val();
        var newSpessore = jQuery("#newSpessore").val();
        var nonce = jQuery("#_wpnonce").val();

        // Check if all required fields are filled
        if (!newId || !newName || !newOffset || !newSpessore) {
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
            security: nonce
        };
        const ajaxurl = '/wp-admin/admin-ajax.php';

        // Save the data (you can customize this part to send the data to your server or store it in your desired format)
        console.log("Token Data:", tokenData);

        // Clear the input fields and hide the second dropdown
        jQuery("#newId").val("");
        jQuery("#newName").val("");
        jQuery("#newOffset").val("");
        jQuery("#newSpessore").val("");
        jQuery("#dropdownMenu").slideToggle(300);
        await jQuery.ajax({
            url: ajaxurl, // WordPress AJAX endpoint
            type: 'POST',
            data: tokenData,
            success: function(response) {
                // Handle the success response here
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
        var newId = jQuery("#newId").val();
        var newPeso = jQuery("#newPeso").val();
        var newPrezzo = jQuery("#newPrezzo").val();
        var nonce = jQuery("#_wpnonce").val();

        // Check if all required fields are filled
        if (!newId || !newPeso || !newPrezzo) {
            alert("Please fill in all required fields.");
            return;
        }

        // Create an object to store the data
        var tokenData = {
            action: 'save_materiale',
            id: newId,
            peso_specifico: newPeso,
            prezzo_kilo: newPrezzo,
            security: nonce
        };
        const ajaxurl = '/wp-admin/admin-ajax.php';

        // Save the data (you can customize this part to send the data to your server or store it in your desired format)
        console.log("Token Data:", tokenData);

        // Clear the input fields and hide the second dropdown
        jQuery("#newId").val("");
        jQuery("#newPeso").val("");
        jQuery("#newPrezzo").val("");
        jQuery("#dropdownMenu").slideToggle(300);
        await jQuery.ajax({
            url: ajaxurl, // WordPress AJAX endpoint
            type: 'POST',
            data: tokenData,
            success: function(response) {
                // Handle the success response here
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
        var newId = jQuery("#newId").val();
        var newPercentuale = jQuery("#newPercentuale").val();
        var nonce = jQuery("#_wpnonce").val();

        // Check if all required fields are filled
        if (!newId || !newPercentuale ) {
            alert("Please fill in all required fields.");
            return;
        }

        // Create an object to store the data
        var tokenData = {
            action: 'save_partnership_level',
            id: newAddress,
            name: newName,
            percentage: chainSelector,
            security: nonce
        };
        const ajaxurl = '/wp-admin/admin-ajax.php';

        // Save the data (you can customize this part to send the data to your server or store it in your desired format)
        console.log("Token Data:", tokenData);

        // Clear the input fields and hide the second dropdown
        jQuery("#newId").val("");
        jQuery("#newPercentuale").val("");
        jQuery("#dropdownMenu").slideToggle(300);
        await jQuery.ajax({
            url: ajaxurl, // WordPress AJAX endpoint
            type: 'POST',
            data: tokenData,
            success: function(response) {
                // Handle the success response here
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
        var id = jQuery(this).closest(".token-row").find(".id").val();
        var name = jQuery(this).closest(".token-row").find(".name").val();
        var offset = jQuery(this).closest(".token-row").find(".offset").val();
        var spessore = jQuery(this).closest(".token-row").find(".spessore").val();
        var nonce = jQuery(this).closest(".token-row").find(".saveNonce").val();

        // Check if all required fields are filled
        if (!id || !name || !offset || !spessore) {
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
            security: nonce
        };
        const ajaxurl = '/wp-admin/admin-ajax.php';

        // Save the data (you can customize this part to send the data to your server or store it in your desired format)
        console.log("Token Data:", tokenData);

        // Clear the input fields and hide the second dropdown
        var settings = jQuery(this).closest(".token-row").find(".settings");
        settings.slideToggle(300)
        await jQuery.ajax({
            url: ajaxurl, // WordPress AJAX endpoint
            type: 'POST',
            data: tokenData,
            success: function(response) {
                // Handle the success response here
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
        // Get values from input fields
        var id = jQuery(this).closest(".token-row").find(".id").val();
        var peso = jQuery(this).closest(".token-row").find(".peso").val();
        var prezzo = jQuery(this).closest(".token-row").find(".prezzo").val();
        var nonce = jQuery(this).closest(".token-row").find(".saveNonce").val();

        // Check if all required fields are filled
        if (!id || !peso || !prezzo) {
            alert("Please fill in all required fields.");
            return;
        }

        // Create an object to store the data
        var tokenData = {
            action: 'save_materiale',
            id: newId,
            peso_specifico: newPeso,
            prezzo_kilo: newPrezzo,
            security: nonce
        };
        const ajaxurl = '/wp-admin/admin-ajax.php';

        // Save the data (you can customize this part to send the data to your server or store it in your desired format)
        console.log("Token Data:", tokenData);

        // Clear the input fields and hide the second dropdown
        var settings = jQuery(this).closest(".token-row").find(".settings");
        settings.slideToggle(300)
        await jQuery.ajax({
            url: ajaxurl, // WordPress AJAX endpoint
            type: 'POST',
            data: tokenData,
            success: function(response) {
                // Handle the success response here
                console.log(response);
            },
            error: function(error) {
                // Handle the error here
                console.error(error);
            }
        });
        location.reload()

    });

    jQuery(".token-row .savePartnershipButton").click(async function () {
        // Get values from input fields
        var id = jQuery(this).closest(".token-row").find(".id").val();
        var percentuale = jQuery(this).closest(".token-row").find(".percentuale").val();
        var nonce = jQuery(this).closest(".token-row").find(".saveNonce").val();

        // Check if all required fields are filled
        if (!id || !percentuale ) {
            alert("Please fill in all required fields.");
            return;
        }

        // Create an object to store the data
        var tokenData = {
            action: 'save_partnership_level',
            id: newAddress,
            name: newName,
            percentage: chainSelector,
            security: nonce
        };
        const ajaxurl = '/wp-admin/admin-ajax.php';

        // Save the data (you can customize this part to send the data to your server or store it in your desired format)
        console.log("Token Data:", tokenData);

        // Clear the input fields and hide the second dropdown
        var settings = jQuery(this).closest(".token-row").find(".settings");
        settings.slideToggle(300)
        await jQuery.ajax({
            url: ajaxurl, // WordPress AJAX endpoint
            type: 'POST',
            data: tokenData,
            success: function(response) {
                // Handle the success response here
                console.log(response);
            },
            error: function(error) {
                // Handle the error here
                console.error(error);
            }
        });
        location.reload()

    });

    jQuery(".token-row .deleteMachineButton").click(async function () {
        // Get values from input fields
        var id = jQuery(this).closest(".token-row").find(".id").val();
        var nonce = jQuery(this).closest(".token-row").find(".deleteNonce").val();


        // Create an object to store the data
        var tokenData = {
            action: 'delete_macchina',
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
        
        await jQuery.ajax({
            url: ajaxurl, // WordPress AJAX endpoint
            type: 'POST',
            data: tokenData,
            success: function(response) {
                // Handle the success response here
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
        // Get values from input fields
        var id = jQuery(this).closest(".token-row").find(".id").val();
        var nonce = jQuery(this).closest(".token-row").find(".deleteNonce").val();


        // Create an object to store the data
        var tokenData = {
            action: 'delete_materiale',
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
        
        await jQuery.ajax({
            url: ajaxurl, // WordPress AJAX endpoint
            type: 'POST',
            data: tokenData,
            success: function(response) {
                // Handle the success response here
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
        
        await jQuery.ajax({
            url: ajaxurl, // WordPress AJAX endpoint
            type: 'POST',
            data: tokenData,
            success: function(response) {
                // Handle the success response here
                console.log(response);
            },
            error: function(error) {
                // Handle the error here
                console.error(error);
            }
        });
        location.reload()

    });

    jQuery('#forma').change(function() {
        jQuery('.campoDimensioni').hide();
        const formaSelezionata = jQuery(this).val();
        switch(formaSelezionata) {
            case 'quadrato':
                jQuery('#dimensioniQuadrato').show();
                break;
            case 'rettangolare':
                jQuery('#dimensioniRettangolo').show();
                break;
            case 'cerchio':
                jQuery('#dimensioniCerchio').show();
                break;
            case 'crescente':
                jQuery('#dimensioniCrescente').show();
                break;
        }
    });

    jQuery("#generaPreventivo").click(async function () {
        // Get values from input fields
        var forma = jQuery("#forma").val();
        let svg;
        var materiale = jQuery("#materiale").val();
        var spessore = jQuery("#spessore").val();
        let dimX, dimY, macchina, um;
        var quantita = jQuery("#quantità").val();
        let p_reale,p_quadrotto,p_esterno,p_rottame,p_quadrotto_plus_10;

        // var newPercentuale = jQuery("#newPercentuale").val();
        // var nonce = jQuery("#_wpnonce").val();

        switch(forma) {
            case 'quadrato':
                const lato = jQuery("#dimensioniQuadrato #lato").val();
                dimX = lato;
                dimY = lato;
                svg = creaQuadratoSVG(lato);
                break;
            case 'rettangolare':
                const larghezza = jQuery("#dimensioniRettangolo #larghezza").val();
                const altezza = jQuery("#dimensioniRettangolo #altezza").val();
                dimX = larghezza;
                dimY = altezza;
                svg = creaRettangoloSVG(larghezza, altezza);
                break;
            case 'cerchio':
                const raggio = jQuery("#dimensioniCerchio #raggio").val();
                dimX = 2*raggio;
                dimY = 2*raggio;
                svg = creaCerchioSVG(raggio);
                break;
            case 'crescente':
                const raggioGrande = jQuery("#dimensioniCrescente #raggioGrande").val();
                const raggioPiccolo = jQuery("#dimensioniCrescente #raggioPiccolo").val();
                const posizionePiccolo = jQuery("#dimensioniCrescente #posizionePiccolo").val();
                svg = creaMezzalunaSVG(raggioGrande, raggioPiccolo, posizionePiccolo);
                break;
            default:
                console.log('Forma non riconosciuta');
        }


        // Check if all required fields are filled
        if (!newId || !newPercentuale ) {
            alert("Please fill in all required fields.");
            return;
        }
        // Create an object to store the data
        var tokenData = {
            action: 'save_partnership_level',
            id: newAddress,
            name: newName,
            percentage: chainSelector,
            security: nonce
        };
        const ajaxurl = '/wp-admin/admin-ajax.php';

        // Save the data (you can customize this part to send the data to your server or store it in your desired format)
        console.log("Token Data:", tokenData);

        // Clear the input fields and hide the second dropdown
        jQuery("#newId").val("");
        jQuery("#newPercentuale").val("");
        jQuery("#dropdownMenu").slideToggle(300);
        await jQuery.ajax({
            url: ajaxurl, // WordPress AJAX endpoint
            type: 'POST',
            data: tokenData,
            success: function(response) {
                // Handle the success response here
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

function creaQuadratoSVG(lato) {
    return `<svg width="${lato}" height="${lato}"><rect width="${lato}" height="${lato}" style="fill:blue;"/></svg>`;
}

function creaRettangoloSVG(larghezza, altezza) {
    return `<svg width="${larghezza}" height="${altezza}"><rect width="${larghezza}" height="${altezza}" style="fill:blue;"/></svg>`;
}

function creaCerchioSVG(raggio) {
    return `<svg width="${raggio * 2}" height="${raggio * 2}"><circle cx="${raggio}" cy="${raggio}" r="${raggio}" style="fill:blue;"/></svg>`;
}

function creaMezzalunaSVG(raggioGrande, raggioPiccolo, posizionePiccolo) {
    // Calcola la posizione del cerchio piccolo
    const cxPiccolo = raggioGrande - (raggioGrande * posizionePiccolo);
    
    // SVG base
    const svgBase = `<svg width="${raggioGrande * 2}" height="${raggioGrande * 2}" viewBox="0 0 ${raggioGrande * 2} ${raggioGrande * 2}" xmlns="http://www.w3.org/2000/svg">`;

    // Disegna il cerchio grande
    const cerchioGrande = `<circle cx="${raggioGrande}" cy="${raggioGrande}" r="${raggioGrande}" fill="blue"/>`;

    // Disegna il cerchio piccolo
    const cerchioPiccolo = `<circle cx="${cxPiccolo}" cy="${raggioGrande}" r="${raggioPiccolo}" fill="white"/>`;

    // Chiudi l'SVG
    const svgClose = `</svg>`;

    return svgBase + cerchioGrande + cerchioPiccolo + svgClose;
}