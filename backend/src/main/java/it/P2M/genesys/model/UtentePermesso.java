package it.P2M.genesys.model;

import org.springframework.data.annotation.Id;
import org.springframework.data.mongodb.core.mapping.Document;

@Document(collection = "utenti_permessi")
public class UtentePermesso {

    @Id
    private String id;

    private String utenteId;  // Riferimento all'utente
    private String permessoId; // Riferimento al permesso

    // Costruttore vuoto
    public UtentePermesso() {}

    // Costruttore con parametri
    public UtentePermesso(String utenteId, String permessoId) {
        this.utenteId = utenteId;
        this.permessoId = permessoId;
    }

    // Getter e setter
    public String getId() {
        return id;
    }

    public String getUtenteId() {
        return utenteId;
    }

    public void setUtenteId(String utenteId) {
        this.utenteId = utenteId;
    }

    public String getPermessoId() {
        return permessoId;
    }

    public void setPermessoId(String permessoId) {
        this.permessoId = permessoId;
    }
}
