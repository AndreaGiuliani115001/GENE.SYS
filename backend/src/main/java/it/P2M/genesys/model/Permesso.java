package it.P2M.genesys.model;

import org.springframework.data.annotation.Id;
import org.springframework.data.mongodb.core.mapping.Document;

@Document(collection = "permessi")
public class Permesso {

    @Id
    private String id;

    private String azione;
    private String entita;

    public Permesso() {
    }

    // Costruttori, getter, e setter
    public Permesso(String azione, String entita) {
        this.azione = azione;
        this.entita = entita;
    }

    public String getId() {
        return id;
    }

    public String getAzione() {
        return azione;
    }

    public void setAzione(String azione) {
        this.azione = azione;
    }

    public String getEntita() {
        return entita;
    }

    public void setEntita(String entita) {
        this.entita = entita;
    }

}

