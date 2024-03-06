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
        jQuery("#dropdownMenu").slideToggle(300);
    });
    
    jQuery("#addMaterialBtn").click(function (){
        jQuery("#dropdownMenu").slideToggle(300);
    });

    jQuery("#addPartnershipBtn").click(function (){
        jQuery("#dropdownMenu").slideToggle(300);
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
        Query("#newName").val("");
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
});