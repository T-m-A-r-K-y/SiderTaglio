jQuery(document).ready(function () {

    jQuery('#forma').change(function() {
        jQuery('.campoDimensioni').hide();
        const formaSelezionata = jQuery(this).val();
        switch(formaSelezionata) {
            case 'quadrato':
                jQuery('#dimensioniQuadrato').show();
                break;
            case 'rettangolo':
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

    jQuery('#materiale').change(function() {
        var selectedMaterial = jQuery('#materiale').val();
        var spessoreDropdown = jQuery('#spessore');
        spessoreDropdown.prop('disabled', false);

        // Hide all options and then show only the relevant ones
        jQuery('#spessore option').hide().filter('.' + selectedMaterial).show();

        // Reset the spessore value
        spessoreDropdown.val('');
    });

    jQuery("#generaPreventivo").click(async function () {
        // Get values from input fields
        var forma = jQuery("#forma").val();
        let svg,p_reale;
        var materiale = jQuery("#materiale").val();
        var spessore = jQuery("#spessore").val();
        let dimX, dimY, um, superfice, perimetro;
        var quantita = jQuery("#quantità").val();
        var lavorazioniSelected = {};
        
        // var newPercentuale = jQuery("#newPercentuale").val();
        var nonce = jQuery("#_wpnonce").val();

        switch(forma) {
            case 'quadrato':
                const lato = parseFloat(jQuery("#dimensioniQuadrato #lato").val());
                dimX = lato;
                dimY = lato;
                perimetro = lato * 4;
                superfice = lato * lato; // Area of a square = side^2
                svg = creaQuadratoSVG(lato);
                break;
            case 'rettangolo':
                const larghezza = parseFloat(jQuery("#dimensioniRettangolo #larghezza").val());
                const altezza = parseFloat(jQuery("#dimensioniRettangolo #altezza").val());
                dimX = larghezza;
                dimY = altezza;
                perimetro = altezza * 2 + larghezza * 2;
                superfice = larghezza * altezza; // Area of a rectangle = width * height
                svg = creaRettangoloSVG(larghezza, altezza);
                break;
            case 'cerchio':
                const raggio = parseFloat(jQuery("#dimensioniCerchio #raggio").val());
                dimX = 2 * raggio;
                dimY = 2 * raggio;
                perimetro = raggio * Math.PI * 2;
                superfice = Math.PI * raggio * raggio; // Area of a circle = π * radius^2
                svg = creaCerchioSVG(raggio);
                break;
            case 'crescente':
                const raggioGrande = parseFloat(jQuery("#dimensioniCrescente #raggioGrande").val());
                const raggioPiccolo = parseFloat(jQuery("#dimensioniCrescente #raggioPiccolo").val());
                const posizionePiccolo = parseFloat(jQuery("#dimensioniCrescente #posizionePiccolo").val());
                // Calculate the area of a crescent shape
                // Area = π * (R^2 - r^2), where R = raggioGrande, r = raggioPiccolo
                superfice = Math.PI * (raggioGrande * raggioGrande - raggioPiccolo * raggioPiccolo);
                svg = creaMezzalunaSVG(raggioGrande, raggioPiccolo, posizionePiccolo);
                break;
            default:
                console.log('Forma non riconosciuta');
                superfice = 0;
                break;
        }

        var lavorazioniSelected = {};
        jQuery('#lavorazioni-options input[type="checkbox"]').each(function() {
            lavorazioniSelected[this.name] = this.checked;
        });

        
        // Assuming spessore is defined elsewhere
        p_reale = superfice * spessore;        


        // Check if all required fields are filled
        if (!forma || !materiale || !spessore || !quantita ) {
            alert("Please fill in all required fields.");
            return;
        }
        // Create an object to store the data
        var tokenData = {
            action: 'genera_preventivo',
            materiale: materiale,
            spessore: spessore,
            dim_x: dimX,
            dim_y: dimY,
            quantita: quantita,
            superfice: superfice,
            perimetro: perimetro,
            p_reale: p_reale,
            nested: false,
            lavorazioni: lavorazioniSelected,
            forma: forma,
            security: nonce
        };
        const ajaxurl = '/wp-admin/admin-ajax.php';

        // Save the data (you can customize this part to send the data to your server or store it in your desired format)
        console.log("Token Data:", tokenData);

        //Clear the input fields and hide the second dropdown
        await jQuery.ajax({
            url: ajaxurl, // WordPress AJAX endpoint
            type: 'POST',
            data: tokenData,
            success: function(response) {
                var pdfUrl = window.location.origin + '/' + response.data.pathto;
                // Open the PDF in a new tab
                window.open(pdfUrl, '_blank');
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