# Uso di un'immagine base Python
FROM python:3.9

# Impostazione della directory di lavoro
WORKDIR /app

# Copia dei file dei requisiti e installazione delle dipendenze
COPY requirements.txt .
RUN pip install -r requirements.txt

# Copia del codice sorgente dell'applicazione nella directory di lavoro
COPY . /app

# Esposizione della porta su cui l'applicazione Flask sarà in ascolto
EXPOSE 5000

# Comando per avviare l'applicazione
CMD ["python", "app.py"]
