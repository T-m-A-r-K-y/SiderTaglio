# app.py

from flask import Flask, request, send_file, jsonify
from reportlab.lib.pagesizes import letter
from reportlab.pdfgen import canvas
from flask_sqlalchemy import SQLAlchemy
from flask_jwt_extended import JWTManager, jwt_required, create_access_token, get_jwt_identity
from flask.cli import with_appcontext
import time
import click
import os

app = Flask(__name__)
app.config['JWT_SECRET_KEY'] = os.getenv('JWT_SECRET_KEY')
app.config['SQLALCHEMY_DATABASE_URI'] = 'postgresql://sidertaglio:admin@localhost/sideraglio'
db = SQLAlchemy(app)
jwt = JWTManager(app)

@click.command('create-token')
@with_appcontext
def create_token_command():
    access_token = create_access_token(identity='it_user')
    click.echo(f'Token: {access_token}')

app.cli.add_command(create_token_command)

class Macchina(db.Model):
    id = db.Column(db.Integer, primary_key=True)
    nome = db.Column(db.String(255), nullable=False)
    offset = db.Column(db.Float, nullable=False)

    def to_dict(self):
        return {"id": self.id, "nome": self.nome, "offset": self.offset}

class Materiale(db.Model):
    codice_materiale = db.Column(db.String(255), primary_key=True)
    peso_specifico = db.Column(db.Float, nullable=False)

    def to_dict(self):
        return {"codice_materiale": self.codice_materiale, "peso_specifico": self.peso_specifico}

class LivelloPartnership(db.Model):
    livello = db.Column(db.String(255), primary_key=True)
    sovrapprezzo_percentuale = db.Column(db.Float, nullable=False)

    def to_dict(self):
        return {"livello": self.livello, "sovrapprezzo_percentuale": self.sovrapprezzo_percentuale}


def connect_to_database():
    for _ in range(5):  # Riprova per 5 volte
        try:
            db.create_all()  # Crea le tabelle se non esistono
            break
        except Exception as e:
            print("Attesa per il database...")
            time.sleep(5)  # Aspetta 5 secondi prima di riprovare

connect_to_database()

@app.route('/add_macchina', methods=['POST'])
@jwt_required()
def add_macchina():
    data = request.get_json()
    macchina = Macchina(nome=data['nome'], offset=data['offset'])
    db.session.add(macchina)
    db.session.commit()
    return jsonify({'message': 'Macchina aggiunta con successo'}), 201

@app.route('/add_materiale', methods=['POST'])
@jwt_required()
def add_materiale():
    data = request.get_json()
    materiale = Materiale(codice_materiale=data['codice_materiale'], peso_specifico=data['peso_specifico'])
    db.session.add(materiale)
    db.session.commit()
    return jsonify({'message': 'Materiale aggiunta con successo'}), 201

@app.route('/add_livello_partnership', methods=['POST'])
@jwt_required()
def add_livello_partnership():
    data = request.get_json()
    livello_partnership = LivelloPartnership(livello=data['livello'],sovrapprezzo_percentuale=data['sovrapprezzo_percentuale'])
    db.session.add(livello_partnership)
    db.session.commit()
    return jsonify({'message': 'Livello di partnership aggiunto con successo'}), 201

@app.route('/macchine', methods=['GET'])
@jwt_required()
def get_macchine():
    macchine = Macchina.query.all()
    return jsonify([macchina.to_dict() for macchina in macchine])

@app.route('/materiale', methods=['GET'])
@jwt_required()
def get_materiale():
    materiale = Materiale.query.all()
    return jsonify([materiale.to_dict() for materiale in materiale])

@app.route('/livello_partnership', methods=['GET'])
@jwt_required()
def get_livello_partnership():
    livello_partnership = LivelloPartnership.query.all()
    return jsonify([livello_partnership.to_dict() for livello_partnership in livello_partnership])


@app.route('/update_macchina/<int:id>', methods=['PUT'])
@jwt_required()
def update_macchina(id):
    macchina = Macchina.query.get_or_404(id)
    data = request.get_json()
    macchina.nome = data['nome']
    macchina.offset = data['offset']
    db.session.commit()
    return jsonify({'message': 'Macchina aggiornata con successo'})

@app.route('/update_materiale/<int:id>', methods=['PUT'])
@jwt_required()
def update_materiale(id):
    materiale = Materiale.query.get_or_404(id)
    data = request.get_json()
    materiale.codice_materiale = data['codice_materiale']
    materiale.peso_specifico = data['peso_specifico']
    db.session.commit()
    return jsonify({'message': 'Materiale aggiornato con successo'})

@app.route('/update_livello_partnership/<int:id>', methods=['PUT'])
@jwt_required()
def update_livello_partnership(id):
    livello_partnership = LivelloPartnership.query.get_or_404(id)
    data = request.get_json()
    livello_partnership.livello = data['livello']
    livello_partnership.sovrapprezzo_percentuale = data['sovrapprezzo_percentuale']
    db.session.commit()
    return jsonify({'message': 'Livello Partnership aggiornato con successo'})

@app.route('/delete_macchina/<int:id>', methods=['DELETE'])
@jwt_required()
def delete_macchina(id):
    macchina = Macchina.query.get_or_404(id)
    db.session.delete(macchina)
    db.session.commit()
    return jsonify({'message': 'Macchina eliminata con successo'})

@app.route('/delete_materiale/<int:id>', methods=['DELETE'])
@jwt_required()
def delete_materiale(id):
    materiale = Materiale.query.get_or_404(id)
    db.session.delete(materiale)
    db.session.commit()
    return jsonify({'message': 'Materiale eliminato con successo'})

@app.route('/delete_livello_partnership/<int:id>', methods=['DELETE'])
@jwt_required()
def delete_livello_partnership(id):
    livello_partnership = LivelloPartnership.query.get_or_404(id)
    db.session.delete(livello_partnership)
    db.session.commit()
    return jsonify({'message': 'Livello Partnership eliminato con successo'})

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
