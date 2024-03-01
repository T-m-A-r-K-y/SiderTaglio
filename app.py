# app.py

from flask import Flask, request, send_file
from reportlab.lib.pagesizes import letter
from reportlab.pdfgen import canvas

app = Flask(__name__)

@app.route('/update', methods=['POST'])
def update_data():
    # Update database based on input data
    # Example:
    # data = request.get_json()
    # Update database with data
    return "Data updated successfully", 200

@app.route('/generate-pdf', methods=['POST'])
def generate_pdf():
    # Generate PDF file modified based on input data
    # Example:
    # data = request.get_json()
    # Modify PDF file based on data
    pdf_path = 'modified_pdf.pdf'  # Example path
    c = canvas.Canvas(pdf_path, pagesize=letter)
    c.drawString(100, 750, "Modified PDF Content")
    c.save()
    return send_file(pdf_path, as_attachment=True), 200

if __name__ == '__main__':
    app.run(host='0.0.0.0', port=5000)
