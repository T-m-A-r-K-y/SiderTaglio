import math
import numpy as np
import logging

# Configure logging
logging.basicConfig(filename='debug.log', filemode='w', level=logging.DEBUG, format='%(asctime)s - %(levelname)s - %(message)s')

def translate_polygon(polygon, dx, dy):
    """Translate a polygon by dx and dy."""
    return [(x + dx, y + dy) for x, y in polygon]

def segments_intersect(seg1, seg2, polygon1, polygon2):
    """Check if two segments intersect and determine the nature of their intersection."""
    (x1, y1), (x2, y2) = seg1
    (x3, y3), (x4, y4) = seg2

    # Helper functions
    def line_coefficients(p1, p2):
        """Calculate line coefficients A, B, C for the line equation Ax + By = C."""
        (x1, y1), (x2, y2) = p1, p2
        A = y1 - y2
        B = x2 - x1
        C = A * x1 + B * y1
        return A, B, C
    
    def on_segment(px, py, seg):
        """Check if point (px, py) lies on the segment seg, including endpoints."""
        (x1, y1), (x2, y2) = seg
        return min(x1, x2) <= px <= max(x1, x2) and min(y1, y2) <= py <= max(y1, y2)

    def is_point_inside_polygon(point, polygon):
        """Check if the point (px, py) is inside the polygon using the ray casting algorithm."""
        px, py = point
        count = 0
        n = len(polygon)
        for i in range(n):
            x1, y1 = polygon[i]
            x2, y2 = polygon[(i + 1) % n]
            if y1 != y2:
                if py <= max(y1, y2):
                    if py >= min(y1, y2):
                        if px <= max(x1, x2):
                            xinters = (py - y1) * (x2 - x1) / (y2 - y1) + x1
                            if x1 == x2 or px <= xinters:
                                count += 1
        return count % 2 != 0


    A1, B1, C1 = line_coefficients((x1, y1), (x2, y2))
    A2, B2, C2 = line_coefficients((x3, y3), (x4, y4))
    # Calculate determinant to check if lines are parallel or coincident
    determinant = A1 * B2 - A2 * B1
    if determinant == 0:
        if A1 * C2 == A2 * C1 and B1 * C2 == B2 * C1:
            if on_segment(x1, y1, seg2) or on_segment(x2, y2, seg2) or on_segment(x3, y3, seg1) or on_segment(x4, y4, seg1) or ((x1,y1) in [(x3,y3),(x4,y4)] and (x2,y2) in [(x3,y3),(x4,y4)]):
                return "coincident"
        return "none"
    else:
        ix = (B2 * C1 - B1 * C2) / determinant
        iy = (A1 * C2 - A2 * C1) / determinant
        # logging.debug(f'intersection in {ix} {iy}')
        if ((ix,iy) in [(x1,y1),(x2,y2)] and (ix,iy) in [(x3,y3),(x4,y4)]):
            return "none"
        if on_segment(ix, iy, seg1) and on_segment(ix, iy, seg2):
            if (ix, iy) in [seg1[0], seg1[1], seg2[0], seg2[1]]:
                # Check if the point is a vertex and the other vertex is inside the opposite polygon
                if (ix,iy) == (x1, y1):
                    if is_point_inside_polygon((x2, y2), polygon2):
                        return "crossing"
                if (ix,iy) == (x2, y2):
                    if is_point_inside_polygon((x1, y1), polygon2):
                        return "crossing"
                if (ix,iy) == (x3, y3):
                    if is_point_inside_polygon((x4, y4), polygon1):
                        return "crossing"
                if (ix,iy) == (x4, y4):
                    if is_point_inside_polygon((x3, y3), polygon1):
                        return "crossing"
                return "none"
            return "crossing"
        return "none"

def check_valid_position(polygon1, polygon2):
    """Check if there are any crossing or coincident positions between two polygons."""
    has_coincident = False
    coinc_seg1 = ()
    coinc_seg2 = ()
    # logging.debug((polygon1,polygon2))
    if polygon1 and polygon2:
        sides1 = polygon_sides(polygon1)
        for i, ((x1, y1), (x2, y2)) in enumerate(sides1):
            seg1 = ((x1, y1), (x2, y2))
            sides2 = polygon_sides(polygon2)
            for i, ((x3, y3), (x4, y4)) in enumerate(sides2):
                seg2 = ((x3, y3), (x4, y4))
                intersection_type = segments_intersect(seg1, seg2, polygon1, polygon2)
                # logging.debug(f'segments {seg1} and {seg2} intersection {intersection_type}')
                if intersection_type == "crossing":
                    return ("crossing",None,None)  # Immediately return if any crossing is found
                elif intersection_type == "coincident":
                    has_coincident = True
                    coinc_seg1 = seg1
                    coinc_seg2 = seg2
    return ("coincident",coinc_seg1,coinc_seg2) if has_coincident else ("none",None,None)

def all_translated_polygons(polygons, distance=1):
    polygon1, polygon2 = polygons
    polygon1 = [(round(x,5),round(y,5)) for x,y in polygon1]
    polygon2 = [(round(x,5),round(y,5)) for x,y in polygon2]
    intersection, coinc_seg1, coinc_seg2 = check_valid_position(polygon1, polygon2)
    # logging.debug(f'polygons intersection {intersection} in {coinc_seg1} and {coinc_seg2}')
    if intersection == "crossing":
        return None,None  # Return the original polygons unmodified if crossing.
    elif intersection == "coincident":
        # Perpendicular translation calculations
        (x1, y1), (x2, y2) = coinc_seg1
        dx, dy = (y2 - y1), -(x2 - x1)  # Calculate perpendicular vector
        if dx!=0:
            dx = np.sign(dx) 
        if dy!=0:
            dy = np.sign(dy)
        translated_polygons = [
            (translate_polygon(polygon1, dx * distance, dy * distance), polygon2),
            (translate_polygon(polygon1, -dx * distance, -dy * distance), polygon2),
            (polygon1, translate_polygon(polygon2, dx * distance, dy * distance)),
            (polygon1, translate_polygon(polygon2, -dx * distance, -dy * distance))
        ]
        # Check all translated positions for validity and return the first valid configuration
        for new_polygons in translated_polygons:
            new_polygon1, new_polygon2 = new_polygons
            if all(x >= 0 and y >= 0 for x, y in new_polygon1) and all(x >= 0 and y >= 0 for x, y in new_polygon2):
                res_pol1,res_pol2 = all_translated_polygons(new_polygons, distance)
                if res_pol1 and res_pol2:  # Ensure that further recursive translations do not return None
                    return res_pol1,res_pol2
    elif intersection == "none":
        return polygon1,polygon2  
    return None,None # Return the original polygons if no valid configuration is found.

def calculate_additional_symmetries(sides):
    """Calculate additional symmetrical positions using perpendicular bisectors."""
    additional_symmetries = []
    for ((x1, y1), (x2, y2)) in sides:
        mx, my = midpoint(x1, y1, x2, y2)  # Midpoint of the side
        
        if x1 == x2:  # Vertical side, perpendicular bisector is horizontal
            additional_symmetries.append(('horizontal', my))
        elif y1 == y2:  # Horizontal side, perpendicular bisector is vertical
            additional_symmetries.append(('vertical', mx))
        else:
            # Normal case: calculate the slope of the perpendicular bisector
            slope = -(x2 - x1) / (y2 - y1)
            intercept = my - slope * mx
            additional_symmetries.append((slope, intercept))

    # Find intersections of perpendicular bisectors
    intersections = []
    for i, line1 in enumerate(additional_symmetries):
        for j, line2 in enumerate(additional_symmetries):
            if i < j:
                if line1[0] == 'vertical' and line2[0] != 'vertical' and line2[0] != 'horizontal':
                    ix = line1[1]  # x-coordinate of the intersection
                    iy = line2[0] * ix + line2[1]  # y = slope * x + intercept
                elif line1[0] != 'vertical' and line1[0] != 'horizontal' and line2[0] == 'vertical' :
                    ix = line2[1]
                    iy = line1[0] * ix + line1[1]
                elif line1[0] == 'horizontal' and line2[0] != 'horizontal' and line2[0] != 'vertical':
                    iy = line1[1]  # y-coordinate of the intersection
                    ix = (iy - line2[1]) / line2[0]  # x = (y - intercept) / slope
                elif line1[0] != 'horizontal' and line1[0] != 'vertical' and line2[0] == 'horizontal':
                    iy = line2[1]
                    ix = (iy - line1[1]) / line1[0]
                elif line1[0] != 'vertical' and line1[0] != 'horizontal' and \
                     line2[0] != 'vertical' and line2[0] != 'horizontal':
                    # Solve linear equations
                    a1, b1 = line1
                    a2, b2 = line2
                    denom = a1 - a2
                    if denom != 0:
                        ix = (b2 - b1) / denom
                        iy = a1 * ix + b1
                    else:
                        continue  # Lines are parallel, no intersection
                else:
                    continue  # Undefined or unsupported case
                
                intersections.append((ix, iy))

    return intersections

def rotate_point(x, y, theta):
    """Rotate a point (x, y) around the origin by angle theta (in radians)."""
    x_new = x * math.cos(theta) - y * math.sin(theta)
    y_new = x * math.sin(theta) + y * math.cos(theta)
    return (x_new, y_new)

def shift_polygon(polygon):
    """Shift polygon to ensure all coordinates are non-negative and close to origin."""
    min_x = min(p[0] for p in polygon)
    min_y = min(p[1] for p in polygon)
    return [(x - min_x, y - min_y) for x, y in polygon]

def polygon_sides(vertices):
    """Generate sides of the polygon as tuples of points (formatted as [(x1, y1), (x2, y2)])"""
    return [((vertices[i][0], vertices[i][1]), (vertices[(i + 1) % len(vertices)][0], vertices[(i + 1) % len(vertices)][1])) for i in range(len(vertices))]

def angle_to_horizontal(x1, y1, x2, y2):
    """Calculate the angle needed to rotate an edge to be horizontal."""
    return math.atan2(y2 - y1, x2 - x1)

def midpoint(x1, y1, x2, y2):
    """Calcola il punto medio di due punti."""
    return (x1 + x2) / 2, (y1 + y2) / 2

def reflect_across_line(px, py, x1, y1, x2, y2):
    """Riflette un punto (px, py) rispetto alla linea passante per (x1, y1) e (x2, y2)."""
    dx, dy = x2 - x1, y2 - y1
    a = (dx * dx - dy * dy) / (dx * dx + dy * dy)
    b = 2 * dx * dy / (dx * dx + dy * dy)
    x_new = a * (px - x1) + b * (py - y1) + x1
    y_new = b * (px - x1) - a * (py - y1) + y1
    return (x_new, y_new)

def reflect_across_point(px, py, mx, my):
    """Riflette un punto (px, py) rispetto al punto (mx, my)."""
    return (2 * mx - px, 2 * my - py)

def calculate_bounding_rectangle_area(polygon):
    """Calculate the area of the bounding rectangle from the origin that can inscribe the polygon."""
    max_x = max(p[0] for p in polygon)
    max_y = max(p[1] for p in polygon)
    return max_x * max_y, max_y

def bounding_box_area(polygon):
    """Calcola l'area del rettangolo contenente il poligono."""
    min_x = min(p[0] for p in polygon)
    max_x = max(p[0] for p in polygon)
    min_y = min(p[1] for p in polygon)
    max_y = max(p[1] for p in polygon)
    return (max_x - min_x) * (max_y - min_y), max_x - min_x, max_y - min_y

def polygon_area(vertices):
    """Calculate the area of a polygon given its vertices."""
    n = len(vertices)
    area = 0.0
    for i in range(n):
        x1, y1 = vertices[i]
        x2, y2 = vertices[(i + 1) % n]
        cross_product = x1 * y2 - y1 * x2
        area += cross_product
    return abs(area) / 2.0

def process_polygon(vertices, number_of_polygons, max_height=9999):
    sides = polygon_sides(vertices)
    rotations = []
    first_valid_rotation = None

    for (x1, y1), (x2, y2) in sides:
        # Calculate angle to align this side horizontally
        theta = -angle_to_horizontal(x1, y1, x2, y2)
        
        # Rotate all points by this angle
        rotated_polygon = [rotate_point(x, y, theta) for x, y in vertices]
        
        # Shift the rotated polygon to be close to the origin
        shifted_polygon = shift_polygon(rotated_polygon)

        # Calculate the area and the height of the bounding rectangle
        area, height = calculate_bounding_rectangle_area(shifted_polygon)

        # Calculate total height for multiple polygons
        total_height = height * number_of_polygons

        # Check if this configuration is valid within the max height constraint
        if total_height <= max_height and first_valid_rotation is None:
            first_valid_rotation = (theta, shifted_polygon)

        # Store the results
        rotations.append((area, theta, shifted_polygon, total_height))

    # Find minimum area among valid rotations
    min_area = min(area for area, _, _, total_height in rotations if total_height <= max_height)
    
    # Filter results for optimal rotations
    optimal_rotations = [(angle, polygon) for area, angle, polygon, total_height in rotations if area == min_area and total_height <= max_height]

    if len(sides) == 3:
        optimal_rotations = [(angle, polygon) for area, angle, polygon, total_height in rotations if total_height <= max_height]
    # If no valid rotation meets the area criteria within the height constraint, include the first valid rotation
    if not optimal_rotations and first_valid_rotation:
        optimal_rotations.append(first_valid_rotation)

    return optimal_rotations

def find_optimal_configurations(vertices, number_of_polygons, distance):

    if number_of_polygons < 1:
        return []
    
    original_configurations = process_polygon(vertices, 1)
    optimal_configurations = []
    if number_of_polygons == 1:
        for angle, polygon in original_configurations:
            area, width, height = bounding_box_area(polygon)
            area_usage = polygon_area(polygon) / area
            optimal_configurations.append((polygon, None, area,  width, height, area_usage))
        return optimal_configurations

    
    min_area = float('inf')

    for angle, polygon in original_configurations:
        sides = polygon_sides(polygon)
        symmetric_polygons = []
        for i, ((x1, y1), (x2, y2)) in enumerate(sides):
            mx, my = midpoint(x1, y1, x2, y2)
            
            
            symmetric_polygons.append(([reflect_across_line(x, y, x1, y1, x2, y2) for x,y in polygon],[(x1,y1),(x2,y2)],"Lato"))

            symmetric_polygons.append(([reflect_across_point(x,y, mx, my) for x,y in polygon],[(mx,my)],"Punto medio"))

            # Additional symmetries using perpendicular bisectors
            # additional_symmetry_points = list(calculate_additional_symmetries(sides))
            # for symmetry_point in additional_symmetry_points:
            #     symmetric_polygons.append((reflect_and_translate_polygon_across_point(polygon, symmetry_point[0], symmetry_point[1], distance),[(symmetry_point[0], symmetry_point[1])],"Bisettrici perpendicolari"))
                # symmetric_polygons.append(([reflect_across_point(x,y, symmetry_point[0], symmetry_point[1]) for x,y in polygon],[(symmetry_point[0], symmetry_point[1])],"Bisettrici perpendicolari"))


        for sym_polygon_tup in symmetric_polygons:
            sym_polygon, sym_points, sym_type = sym_polygon_tup
            if all(x >= 0 and y >= 0 for x, y in sym_polygon):
                new_polygon,sym_polygon = all_translated_polygons((polygon,sym_polygon),distance)
                if sym_polygon and new_polygon:
                    combined_polygon = new_polygon + sym_polygon
                    area, width, height = bounding_box_area(combined_polygon)
                    area_usage = (polygon_area(new_polygon) + polygon_area(sym_polygon)) / area
                    optimal_configurations.append((new_polygon, sym_polygon, area, width, height, area_usage))
                    if area < min_area:
                        min_area = area
                        optimal_configurations = [(new_polygon, sym_polygon, area, width, height, area_usage)]

    return optimal_configurations

def generate_svg(polygons, width=500, height=500, padding=10):
    """Generate an SVG string for displaying a list of polygons."""
    svg_header = f'<svg width="{width}" height="{height}" xmlns="http://www.w3.org/2000/svg">'
    svg_content = ""
    svg_footer = "</svg>"

    # Define colors for visibility
    colors = ['red', 'blue', 'green', 'purple', 'orange', 'cyan']

    for index, polygon in enumerate(polygons):
        points = " ".join(f"{x + padding},{y + padding}" for x, y in polygon)
        color = colors[index % len(colors)]
        svg_content += f'<polygon points="{points}" fill="none" stroke="{color}" stroke-width="1" />'

    return svg_header + svg_content + svg_footer

# Example of usage
# 0,0 11.1,0 8.9,5.9 1.2, 8.9
vertices = [(0,0), (200,0), (200,75), (75,75), (75,300), (0,300)]
# vertices = [(1,0), (1,100), (101,100)]
number_of_polygons = 2
optimal_results = find_optimal_configurations(vertices, number_of_polygons,20)
# print(generate_svg([vertices],37,37,0))
for original, symmetric, area,  width, height, area_usage in optimal_results:
    print("Original Polygon:", original)
    print("Its area:", polygon_area(original))
    print(f'Square x: {width} , y: {height}')
    print("Area usage:", area_usage * 100, "%")
    print("Area Used:", area)
    if symmetric is None:
        print(generate_svg([original],500,500,0))
    else:
        print("Symmetric Polygon:", symmetric)
        print("Its area:", polygon_area(symmetric))
        print(generate_svg([original,symmetric],500,500,0))

    # print("Type of symmetry:",sym_type)
    # for point in sym_points:
    #     print("Points of symmetry:", point)

# polygon1 = [(20,20), (20,215), (90,215), (90,90), (145,90), (145,20)]
# polygon2 = [(140,0), (140,195), (70,195), (70,70), (15,70), (15,0)]

# new_polygon,sym_polygon = all_translated_polygons((polygon1,polygon2),2)
# print(generate_svg((polygon1,polygon2),500,500,0))

# polygon1 = polygon1 = [(0,0), (0,15), (5,15), (5,5), (10,5), (10,0)]
# polygon2 = [(10,0), (10,15), (5,15), (5,5), (0,5), (0,0)]
# intersection, coinc_seg1, coinc_seg2 = check_valid_position(polygon1, polygon2)
# print(generate_svg((polygon1,polygon2),37,37,0))
# logging.debug(f'polygons intersection {intersection} in {coinc_seg1} and {coinc_seg2}')