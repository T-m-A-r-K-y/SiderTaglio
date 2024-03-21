jQuery(document).ready(function () {

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
        let svg,p_reale;
        var materiale = jQuery("#materiale").val();
        var spessore = jQuery("#spessore").val();
        let dimX, dimY, um;
        var quantita = jQuery("#quantità").val();

        // var newPercentuale = jQuery("#newPercentuale").val();
        var nonce = jQuery("#_wpnonce").val();

        switch(forma) {
            case 'quadrato':
                const lato = jQuery("#dimensioniQuadrato #lato").val();
                dimX = lato;
                dimY = lato;
                p_reale = dimX * dimY * spessore;
                svg = creaQuadratoSVG(lato);
                break;
            case 'rettangolare':
                const larghezza = jQuery("#dimensioniRettangolo #larghezza").val();
                const altezza = jQuery("#dimensioniRettangolo #altezza").val();
                dimX = larghezza;
                dimY = altezza;
                p_reale = dimX * dimY * spessore;
                svg = creaRettangoloSVG(larghezza, altezza);
                break;
            case 'cerchio':
                const raggio = jQuery("#dimensioniCerchio #raggio").val();
                dimX = 2*raggio;
                dimY = 2*raggio;
                p_reale = raggio * raggio * 3.14 * spessore;
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
            action: 'genera_preventivo',
            materiale: materiale,
            spessore: spessore,
            dimX: dimX,
            dimY: dimY,
            quantita: quantita,
            p_reale: p_reale,
            security: nonce
        };
        const ajaxurl = '/wp-admin/admin-ajax.php';

        // Save the data (you can customize this part to send the data to your server or store it in your desired format)
        console.log("Token Data:", tokenData);

        // Clear the input fields and hide the second dropdown
        jQuery("#forma").val("");
        jQuery("#materiale").val("");
        jQuery("#spessore").val("");
        jQuery("#quantità").val("");
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