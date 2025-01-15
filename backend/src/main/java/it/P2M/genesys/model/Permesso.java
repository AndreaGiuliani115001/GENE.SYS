package it.P2M.genesys.model;

import org.springframework.data.annotation.Id;
import org.springframework.data.mongodb.core.mapping.Document;

/**
 * Rappresenta un permesso nel sistema.
 * <p>
 * Ogni permesso definisce un'azione consentita su una determinata entità.
 * Questa classe è mappata nella collezione `permessi` del database MongoDB.
 */
@Document(collection = "permessi")
public class Permesso {

    @Id
    private String id; // Identificativo univoco del permesso

    private String azione; // Azione consentita (es. "modifica", "elimina", "visualizza")
    private String entita; // Entità su cui l'azione è consentita (es. "azienda", "utente")

    /**
     * Costruttore predefinito per la classe `Permesso`.
     */
    public Permesso() {
    }

    /**
     * Costruttore completo per la classe `Permesso`.
     *
     * @param azione Azione consentita dal permesso.
     * @param entita Entità su cui l'azione è consentita.
     */
    public Permesso(String azione, String entita) {
        this.azione = azione;
        this.entita = entita;
    }

    /**
     * Restituisce l'identificativo univoco del permesso.
     *
     * @return L'ID del permesso.
     */
    public String getId() {
        return id;
    }

    /**
     * Restituisce l'azione consentita dal permesso.
     *
     * @return L'azione del permesso.
     */
    public String getAzione() {
        return azione;
    }

    /**
     * Imposta l'azione consentita dal permesso.
     *
     * @param azione L'azione da impostare (es. "modifica").
     */
    public void setAzione(String azione) {
        this.azione = azione;
    }

    /**
     * Restituisce l'entità su cui il permesso si applica.
     *
     * @return L'entità del permesso.
     */
    public String getEntita() {
        return entita;
    }

    /**
     * Imposta l'entità su cui il permesso si applica.
     *
     * @param entita L'entità da impostare (es. "azienda").
     */
    public void setEntita(String entita) {
        this.entita = entita;
    }
}
