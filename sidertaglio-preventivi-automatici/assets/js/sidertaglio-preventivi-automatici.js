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

    jQuery("#saveTokenBtn").click(async function () {
        // Get values from input fields
        var newAddress = jQuery("#newAddress").val();
        var newName = jQuery("#newName").val();
        var chainSelector = jQuery("#chain_selector").val();
        var standardErcCheckbox = jQuery("#standardErcCheckbox").prop("checked");
        var nonce = jQuery("#_wpnonce").val();

        

        // Check if ABI file is uploaded
        var abiFileContent = null;
        if (!standardErcCheckbox) {
            abiFileContent = jQuery("#abiUpload").val();
        }

        // Check if all required fields are filled
        if (!newAddress || !newName || !chainSelector) {
            alert("Please fill in all required fields.");
            return;
        }

        //Check if address is a valid address
        var regex = /^0x[0-9a-fA-F]{40}$/;
        if(!regex.test(newAddress)){
            alert("The address you have entered is not a valid address.");
            return;
        }

        // Create an object to store the data
        var tokenData = {
            action: 'save_custom_token',
            address: newAddress,
            name: newName,
            chain: chainSelector,
            erc20: standardErcCheckbox,
            abifile: abiFileContent,
            security: nonce
        };
        const ajaxurl = '/wp-admin/admin-ajax.php';

        // Save the data (you can customize this part to send the data to your server or store it in your desired format)
        console.log("Token Data:", tokenData);

        // Clear the input fields and hide the second dropdown
        jQuery("#newAddress").val("");
        jQuery("#newName").val("");
        jQuery("#chain_selector").val("");
        jQuery("#standardErcCheckbox").prop("checked", true);
        jQuery("#abiUpload").val("");
        jQuery(".secondDropDown").hide();
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

    jQuery(".token-row .saveButton").click(async function () {
        // Get values from input fields
        var address = jQuery(this).closest(".token-row").find(".address").val();
        var name = jQuery(this).closest(".token-row").find(".name").val();
        var chain = jQuery(this).closest(".token-row").find(".chain").val();
        var standardErcCheckbox = jQuery(this).closest(".token-row").find(".erc20").prop("checked");
        var nonce = jQuery(this).closest(".token-row").find(".saveNonce").val();

        // Check if ABI file is uploaded
        var abiFileContent = null;
        if (!standardErcCheckbox) {
            abiFileContent = jQuery(this).closest(".token-row").find(".abi").val();
        }

        // Check if all required fields are filled
        if (!address || !name || !chain) {
            alert("Please fill in all required fields.");
            return;
        }

        // Create an object to store the data
        var tokenData = {
            action: 'save_custom_token',
            address: address,
            name: name,
            chain: chain,
            erc20: standardErcCheckbox,
            abifile: abiFileContent,
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

    jQuery(".token-row .deleteButton").click(async function () {
        // Get values from input fields
        var address = jQuery(this).closest(".token-row").find(".address").val();
        var chain = jQuery(this).closest(".token-row").find(".chain").val();
        var nonce = jQuery(this).closest(".token-row").find(".deleteNonce").val();


        // Create an object to store the data
        var tokenData = {
            action: 'delete_custom_token',
            address: address,
            chain: chain,
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