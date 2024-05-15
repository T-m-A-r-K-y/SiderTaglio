jQuery(document).ready(function () {

    jQuery('#uploadSVG').change(function () {
        const file = this.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function (e) {
                const parser = new DOMParser();
                const svgDoc = parser.parseFromString(e.target.result, "image/svg+xml");
                const polygons = svgDoc.querySelectorAll('polygon');
    
                if (polygons.length === 1) {
                    jQuery('#formaSvg').show();
                } else {
                    alert('The SVG file must contain exactly one polygon.');
                    jQuery('#uploadSVG').val('');
                    jQuery('#formaSvg').hide();
                }
            };
            reader.readAsText(file);
        }
    });

    jQuery('#forma').change(function() {
        jQuery('.campoDimensioni').hide();
        jQuery('#formaInput').hide();
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
        var forma = jQuery("#forma").val() || jQuery("#formaSvg").val();
        var svgPoints = [];
        // Get values from input fields
        let p_reale;
        var materiale = jQuery("#materiale").val();
        var spessore = jQuery("#spessore").val();
        let dimX, dimY, superfice, perimetro;
        var quantita = jQuery("#quantità").val();
        var lavorazioniSelected = {};
        
        // var newPercentuale = jQuery("#newPercentuale").val();
        var nonce = jQuery("#_wpnonce").val();

        if (jQuery('#uploadSVG')[0].files.length > 0) {
            const file = jQuery('#uploadSVG')[0].files[0];
            const reader = new FileReader();
            reader.onload = async function (e) {
                const parser = new DOMParser();
                const svgDoc = parser.parseFromString(e.target.result, "image/svg+xml");
                const polygon = svgDoc.querySelector('polygon');
                if (polygon) {
                    svgPoints = polygon.getAttribute('points').trim().split(' ').map(pair => pair.split(',').map(Number));
                }
            };
            reader.readAsText(file);
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
        jQuery("#overlay").fadeIn(300);
        //Clear the input fields and hide the second dropdown
        await jQuery.ajax({
            url: ajaxurl, // WordPress AJAX endpoint
            type: 'POST',
            data: tokenData,
            success: function(response) {
                console.log(response);
                jQuery("#overlay").fadeOut(300);
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

    jQuery("#generaPreventivo").click(async function () {
        var forma = jQuery("#forma").val() || jQuery("#formaSvg").val();
        var svgPoints = [];
        // Get values from input fields
        var materiale = jQuery("#materiale").val();
        var spessore = jQuery("#spessore").val();
        var quantita = jQuery("#quantità").val();
        var lavorazioniSelected = {};
        // var newPercentuale = jQuery("#newPercentuale").val();
        var nonce = jQuery("#_wpnonce_machines").val();

        if (jQuery('#uploadSVG')[0].files.length > 0) {
            const file = jQuery('#uploadSVG')[0].files[0];
            const reader = new FileReader();
            reader.onload = async function (e) {
                const parser = new DOMParser();
                const svgDoc = parser.parseFromString(e.target.result, "image/svg+xml");
                const polygon = svgDoc.querySelector('polygon');
                if (polygon) {
                    svgPoints = polygon.getAttribute('points').trim().split(' ').map(pair => pair.split(',').map(Number));
                }
            };
            reader.readAsText(file);
        }

        // Check if all required fields are filled
        if (!forma || !materiale || !spessore || !quantita) {
            alert("Please fill in all required fields.");
            return;
        }
    
        // Retrieve the machine parameters before proceeding with the calculations
        jQuery.ajax({
            url: '/wp-admin/admin-ajax.php',
            type: 'POST',
            data: {
                action: 'retrieve_machine_parameters',
                materiale: materiale,
                spessore: spessore,
                security: nonce
            },
            success: function (response) {
                if (response.success) {
                    processFormSubmission(forma, response.machine, materiale, spessore, quantita, svgPoints);
                } else {
                    alert('No suitable machine found. Please adjust your selections.');
                }
            },
            error: function (error) {
                console.error('Failed to retrieve machine parameters:', error);
                alert('Failed to retrieve machine parameters.');
            }
        });
    });
    
    function processFormSubmission(forma, machine, materiale, spessore, quantita, svgPoints) {
        let dimX, dimY, superfice, perimetro, p_reale;
        let nested = false;
        var nonce = jQuery("#_wpnonce").val();
        // Existing code for handling shapes and other calculations

        var machineId = machine.id;
        var parentId = machine.parent_id;
        var commonData = machine.common_data;
        var childData = machine.child_data;
		var machine_offset = childData.offset;
        var machine_name               = commonData.name;
		var machine_v_taglio           = childData.v_taglio;
		var machine_costo_orario       = childData.costo_orario;
        var machine_innesco       = childData.innesco;
		var machine_numero_canne               = commonData.numero_canne;
    
        switch(forma) {
            case 'rettangolo':
                const larghezza = parseFloat(jQuery("#dimensioniRettangolo #larghezza").val());
                const altezza = parseFloat(jQuery("#dimensioniRettangolo #altezza").val());
                dimX = larghezza;
                dimY = altezza;
                perimetro = altezza * 2 + larghezza * 2;
                superfice = larghezza * altezza; // Area of a rectangle = width * height
                break;
            case 'cerchio':
                const raggio = parseFloat(jQuery("#dimensioniCerchio #raggio").val());
                dimX = 2 * raggio;
                dimY = 2 * raggio;
                perimetro = raggio * Math.PI * 2;
                superfice = Math.PI * raggio * raggio; // Area of a circle = π * radius^2
                break;
            case 'anello':
                const raggioExt = parseFloat(jQuery("#dimensioniAnello #raggio").val());
                const raggioInt = parseFloat(jQuery("#dimensioniAnello #raggio").val());
                dimX = 2 * raggioExt;
                dimY = 2 * raggioExt;
                perimetro = raggioExt * Math.PI * 2 + raggioInt * Math.PI * 2;
                superfice = Math.PI * raggioExt * raggioExt - (Math.PI * raggioInt * raggioInt); // Area of a circle = π * radius^2
                break;
            default:
                if (svgPoints.length !== 0){
                    let newPolygon, transformedPolygon, area, width, height, perimeter, areaUsage, symPoints, symType = findOptimalConfigurations(svgPoints,quantita,machine_offset);
                    dimX = width;
                    dimY = height;
                    superfice = area;
                    perimetro = perimeter;
                    nested = true;
                } else {
                    console.log('Forma non riconosciuta');
                    superfice = 0;
                    dimX = 0;
                    dimY = 0;
                    perimetro = 0;
                    break;
                }
                
        }
    
        var lavorazioniSelected = {};
        jQuery('#lavorazioni-options input[type="checkbox"]').each(function () {
            lavorazioniSelected[this.name] = this.checked;
        });
    
        // Additional calculations can use 'machine' details such as machine.costo_orario
        p_reale = superfice * spessore;  // Example, adjust based on actual needs



        // Create tokenData including machine parameters
        var tokenData = {
            action: 'genera_preventivo',
            forma: forma,
            materiale: materiale,
            spessore: spessore,
            dim_x: dimX,
            dim_y: dimY,
            quantita: quantita,
            superfice: superfice,
            perimetro: perimetro,
            p_reale: p_reale,
            nested: nested,
            machine_id: machineId,
            machine_parent_id: parentId,
            machine_offset: offset,
            machine_name: machine_name,
            machine_v_taglio: machine_v_taglio,
            machine_costo_orario: machine_costo_orario, 
            machine_numero_canne: machine_numero_canne,
            machine_innesco: machine_innesco,
            lavorazioni: lavorazioniSelected,
            security: nonce
        };
    
        // Continue with AJAX request to process the full data
        jQuery.ajax({
            url: '/wp-admin/admin-ajax.php',
            type: 'POST',
            data: tokenData,
            success: function(response) {
                console.log(response);
                jQuery("#overlay").fadeOut(300);
                var pdfUrl = window.location.origin + '/' + response.data.pathto;
                window.open(pdfUrl, '_blank');
            },
            error: function(error) {
                console.error(error);
            }
        });
    }
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

function translatePolygon(polygon, dx, dy) {
    // Translate a polygon by dx and dy.
    return polygon.map(([x, y]) => [x + dx, y + dy]);
}

function segmentsIntersect(seg1, seg2, polygon1, polygon2) {
    // Check if two segments intersect and determine the nature of their intersection.
    const [[x1, y1], [x2, y2]] = seg1;
    const [[x3, y3], [x4, y4]] = seg2;

    // Helper functions
    function lineCoefficients(p1, p2) {
        // Calculate line coefficients A, B, C for the line equation Ax + By = C.
        const [[x1, y1], [x2, y2]] = [p1, p2];
        const A = y2 - y1;
        const B = x1 - x2;
        const C = A * x1 + B * y1;
        return [A, B, C];
    }

    function onSegment(px, py, seg) {
        // Check if point (px, py) lies on the segment seg, including endpoints.
        const [[x1, y1], [x2, y2]] = seg;
        return Math.min(x1, x2) <= px && px <= Math.max(x1, x2) &&
               Math.min(y1, y2) <= py && py <= Math.max(y1, y2);
    }

    function isPointInsidePolygon(point, polygon) {
        // Check if the point (px, py) is inside the polygon using the ray casting algorithm.
        const [px, py] = point;
        let count = 0;
        const n = polygon.length;
        for (let i = 0; i < n; i++) {
            const [x1, y1] = polygon[i];
            const [x2, y2] = polygon[(i + 1) % n];
            if (y1 !== y2) {
                if (py <= Math.max(y1, y2) && py >= Math.min(y1, y2)) {
                    if (px <= Math.max(x1, x2)) {
                        const xinters = (py - y1) * (x2 - x1) / (y2 - y1) + x1;
                        if (x1 === x2 || px <= xinters) {
                            count++;
                        }
                    }
                }
            }
        }
        return count % 2 !== 0;
    }

    const [A1, B1, C1] = lineCoefficients([x1, y1], [x2, y2]);
    const [A2, B2, C2] = lineCoefficients([x3, y3], [x4, y4]);
    // Calculate determinant to check if lines are parallel or coincident
    const determinant = A1 * B2 - A2 * B1;
    if (determinant === 0) {
        if (A1 * C2 === A2 * C1 && B1 * C2 === B2 * C1) {
            if (onSegment(x1, y1, seg2) || onSegment(x2, y2, seg2) || onSegment(x3, y3, seg1) || onSegment(x4, y4, seg1)) {
                return "coincident";
            }
        }
        return "none";
    } else {
        const ix = (B2 * C1 - B1 * C2) / determinant;
        const iy = (A1 * C2 - A2 * C1) / determinant;
        if (onSegment(ix, iy, seg1) && onSegment(ix, iy, seg2)) {
            if ([[ix, iy], [ix, iy]].some(point => [seg1[0], seg1[1], seg2[0], seg2[1]].some(vertex => point[0] === vertex[0] && point[1] === vertex[1]))) {
                // Check if the point is a vertex and the other vertex is inside the opposite polygon
                if (ix === x1 && iy === y1) {
                    if (isPointInsidePolygon([x2, y2], polygon2)) {
                        return "crossing";
                    }
                }
                if (ix === x2 && iy === y2) {
                    if (isPointInsidePolygon([x1, y1], polygon2)) {
                        return "crossing";
                    }
                }
                if (ix === x3 && iy === y3) {
                    if (isPointInsidePolygon([x4, y4], polygon1)) {
                        return "crossing";
                    }
                }
                if (ix === x4 && iy === y4) {
                    if (isPointInsidePolygon([x3, y3], polygon1)) {
                        return "crossing";
                    }
                }
                return "none";
            }
            return "crossing";
        }
        return "none";
    }
}

function checkValidPosition(polygon1, polygon2) {
    // Check if there are any crossing or coincident positions between two polygons.
    let hasCoincident = false;
    let coincSeg1 = null;
    let coincSeg2 = null;
    if (polygon1 && polygon2) {
        const sides1 = polygon1.map((v, i, a) => [v, a[(i + 1) % a.length]]);
        sides1.forEach(seg1 => {
            const sides2 = polygon2.map((v, i, a) => [v, a[(i + 1) % a.length]]);
            sides2.forEach(seg2 => {
                const intersectionType = segmentsIntersect(seg1, seg2, polygon1, polygon2);
                if (intersectionType === "crossing") {
                    return ["crossing", null, null];  // Immediately return if any crossing is found
                } else if (intersectionType === "coincident") {
                    hasCoincident = true;
                    coincSeg1 = seg1;
                    coincSeg2 = seg2;
                }
            });
        });
    }
    return hasCoincident ? ["coincident", coincSeg1, coincSeg2] : ["none", null, null];
}

function allTranslatedPolygons(polygons, distance = 1) {
    let [polygon1, polygon2] = polygons;
    polygon1 = polygon1.map(([x, y]) => [parseFloat(x.toFixed(5)), parseFloat(y.toFixed(5))]);
    polygon2 = polygon2.map(([x, y]) => [parseFloat(x.toFixed(5)), parseFloat(y.toFixed(5))]);
    const [intersection, coincSeg1, coincSeg2] = checkValidPosition(polygon1, polygon2);
    // console.debug(`Polygons intersection ${intersection} in ${coincSeg1} and ${coincSeg2}`);
    if (intersection === "crossing") {
        return [null, null];  // Return the original polygons unmodified if crossing.
    } else if (intersection === "coincident") {
        // Perpendicular translation calculations
        const [[x1, y1], [x2, y2]] = coincSeg1;
        let dx = y2 - y1, dy = -(x2 - x1);  // Calculate perpendicular vector
        dx = dx !== 0 ? Math.sign(dx) : 0;
        dy = dy !== 0 ? Math.sign(dy) : 0;
        const translatedPolygons = [
            [translatePolygon(polygon1, dx * distance, dy * distance), polygon2],
            [translatePolygon(polygon1, -dx * distance, -dy * distance), polygon2],
            [polygon1, translatePolygon(polygon2, dx * distance, dy * distance)],
            [polygon1, translatePolygon(polygon2, -dx * distance, -dy * distance)]
        ];
        // Check all translated positions for validity and return the first valid configuration
        for (const newPolygons of translatedPolygons) {
            const [newPolygon1, newPolygon2] = newPolygons;
            if (newPolygon1.every(([x, y]) => x >= 0 && y >= 0) && newPolygon2.every(([x, y]) => x >= 0 && y >= 0)) {
                const [resPol1, resPol2] = allTranslatedPolygons(newPolygons, distance);
                if (resPol1 && resPol2) {  // Ensure that further recursive translations do not return None
                    return [resPol1, resPol2];
                }
            }
        }
    } else if (intersection === "none") {
        return [polygon1, polygon2];
    }
    return [null, null]; // Return the original polygons if no valid configuration is found.
}

function rotatePoint(x, y, theta) {
    // Rotate a point (x, y) around the origin by angle theta (in radians).
    const xNew = x * Math.cos(theta) - y * Math.sin(theta);
    const yNew = x * Math.sin(theta) + y * Math.cos(theta);
    return [xNew, yNew];
}

function shiftPolygon(polygon) {
    // Shift polygon to ensure all coordinates are non-negative and close to origin.
    const minX = Math.min(...polygon.map(p => p[0]));
    const minY = Math.min(...polygon.map(p => p[1]));
    return polygon.map(([x, y]) => [x - minX, y - minY]);
}

function polygonSides(vertices) {
    // Generate sides of the polygon as tuples of points (formatted as [[x1, y1], [x2, y2]])
    return vertices.map((vertex, i, array) => [vertex, array[(i + 1) % array.length]]);
}

function angleToHorizontal(x1, y1, x2, y2) {
    // Calculate the angle needed to rotate an edge to be horizontal.
    return Math.atan2(y2 - y1, x2 - x1);
}

function midpoint(x1, y1, x2, y2) {
    // Calculate the midpoint of two points.
    return [(x1 + x2) / 2, (y1 + y2) / 2];
}

function reflectAcrossLine(px, py, x1, y1, x2, y2) {
    // Reflects a point (px, py) across the line passing through (x1, y1) and (x2, y2).
    const dx = x2 - x1, dy = y2 - y1;
    const a = (dx * dx - dy * dy) / (dx * dx + dy * dy);
    const b = 2 * dx * dy / (dx * dx + dy * dy);
    const xNew = a * (px - x1) + b * (py - y1) + x1;
    const yNew = b * (px - x1) - a * (py - y1) + y1;
    return [xNew, yNew];
}

function reflectAcrossPoint(px, py, mx, my) {
    // Reflects a point (px, py) across the point (mx, my).
    return [2 * mx - px, 2 * my - py];
}

function calculateBoundingRectangleArea(polygon) {
    // Calculate the area of the bounding rectangle from the origin that can inscribe the polygon.
    const maxX = Math.max(...polygon.map(p => p[0]));
    const maxY = Math.max(...polygon.map(p => p[1]));
    return [maxX * maxY, maxY];
}

function boundingBoxArea(polygon) {
    // Calculate the area of the rectangle containing the polygon.
    const minX = Math.min(...polygon.map(p => p[0]));
    const maxX = Math.max(...polygon.map(p => p[0]));
    const minY = Math.min(...polygon.map(p => p[1]));
    const maxY = Math.max(...polygon.map(p => p[1]));
    return [(maxX - minX) * (maxY - minY), maxX - minX, maxY - minY];
}

function polygonArea(vertices) {
    // Calculate the area of a polygon given its vertices.
    const n = vertices.length;
    let area = 0.0;
    for (let i = 0; i < n; i++) {
        const [x1, y1] = vertices[i];
        const [x2, y2] = vertices[(i + 1) % n];
        const crossProduct = x1 * y2 - y1 * x2;
        area += crossProduct;
    }
    return Math.abs(area) / 2.0;
}

function polygonPerimeter(vertices) {
    // Calculate the perimeter of a polygon given its vertices.
    const n = vertices.length;
    let perimeter = 0.0;
    for (let i = 0; i < n; i++) {
        const [x1, y1] = vertices[i];
        const [x2, y2] = vertices[(i + 1) % n];  // Wrap around to the first vertex
        const distance = Math.sqrt(Math.pow(x2 - x1, 2) + Math.pow(y2 - y1, 2));
        perimeter += distance;
    }
    return perimeter;
}


function processPolygon(vertices, numberOfPolygons, maxHeight = 9999) {
    const sides = polygonSides(vertices);
    let rotations = [];
    let firstValidRotation = null;

    for (const [[x1, y1], [x2, y2]] of sides) {
        // Calculate angle to align this side horizontally
        const theta = -angleToHorizontal(x1, y1, x2, y2);
        
        // Rotate all points by this angle
        const rotatedPolygon = vertices.map(([x, y]) => rotatePoint(x, y, theta));
        
        // Shift the rotated polygon to be close to the origin
        const shiftedPolygon = shiftPolygon(rotatedPolygon);

        // Calculate the area and the height of the bounding rectangle
        const [area, height] = calculateBoundingRectangleArea(shiftedPolygon);

        // Calculate total height for multiple polygons
        const totalHeight = height * numberOfPolygons;

        // Check if this configuration is valid within the max height constraint
        if (totalHeight <= maxHeight && firstValidRotation === null) {
            firstValidRotation = [theta, shiftedPolygon];
        }

        // Store the results
        rotations.push([area, theta, shiftedPolygon, totalHeight]);
    }

    // Find minimum area among valid rotations
    const minArea = Math.min(...rotations.filter(([_, __, ___, totalHeight]) => totalHeight <= maxHeight).map(([area]) => area));
    
    // Filter results for optimal rotations
    const optimalRotations = rotations.filter(([area, angle, polygon, totalHeight]) => area === minArea && totalHeight <= maxHeight).map(([_, angle, polygon]) => [angle, polygon]);

    // If no valid rotation meets the area criteria within the height constraint, include the first valid rotation
    if (optimalRotations.length === 0 && firstValidRotation) {
        optimalRotations.push(firstValidRotation);
    }

    return optimalRotations;
}

function findOptimalConfigurations(vertices, numberOfPolygons, distance) {
    if (numberOfPolygons <= 1) {
        return [];
    }

    const originalConfigurations = processPolygon(vertices, 1);
    let optimalConfiguration = [];
    let minArea = Infinity;

    for (const [angle, polygon] of originalConfigurations) {
        const sides = polygonSides(polygon);

        for (let i = 0; i < sides.length; i++) {
            const [[x1, y1], [x2, y2]] = sides[i];
            const [mx, my] = midpoint(x1, y1, x2, y2);
            const symmetricPolygons = [];
            
            symmetricPolygons.push([polygon.map(([x, y]) => reflectAcrossLine(x, y, x1, y1, x2, y2)), [[x1, y1], [x2, y2]], "Lato"]);

            symmetricPolygons.push([polygon.map(([x, y]) => reflectAcrossPoint(x, y, mx, my)), [[mx, my]], "Punto medio"]);

            for (const [symPolygon, symPoints, symType] of symmetricPolygons) {
                if (symPolygon.every(([x, y]) => x >= 0 && y >= 0)) {
                    const [newPolygon, transformedPolygon] = allTranslatedPolygons([polygon, symPolygon], 2);
                    if (transformedPolygon && newPolygon) {
                        const combinedPolygon = newPolygon.concat(transformedPolygon);
                        const [area, width, height] = boundingBoxArea(combinedPolygon);
                        const areaUsage = (polygonArea(newPolygon) + polygonArea(transformedPolygon)) / area;
                        const perimeter = 2 * polygonPerimeter(newPolygon);
                        if (area < minArea) {
                            minArea = area;
                            optimalConfiguration = [newPolygon, transformedPolygon, area, width, height, perimeter, areaUsage, symPoints, symType];
                        }
                    }
                }
            }
        }
    }

    return optimalConfiguration;
}
