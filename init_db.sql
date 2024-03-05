CREATE TABLE IF NOT EXISTS macchine (
    id SERIAL PRIMARY KEY,
    nome VARCHAR(255) NOT NULL,
    offset FLOAT NOT NULL
);

CREATE TABLE IF NOT EXISTS materiali (
    codice_materiale VARCHAR(255) PRIMARY KEY,
    peso_specifico FLOAT NOT NULL
);

CREATE TABLE IF NOT EXISTS livelli_partnership (
    livello VARCHAR(255) PRIMARY KEY,
    sovrapprezzo_percentuale FLOAT NOT NULL
);

-- Aggiungi qui eventuali altri comandi di inizializzazione.
