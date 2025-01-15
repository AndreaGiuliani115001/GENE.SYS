package it.P2M.genesys.model;

import org.springframework.data.annotation.Id;
import org.springframework.data.mongodb.core.mapping.Document;

/**
 * Rappresenta l'associazione tra un utente e un permesso.
 * <p>
 * Questa classe è mappata nella collezione `utenti_permessi` del database MongoDB.
 * Ogni documento rappresenta un collegamento tra un utente e un permesso specifico.
 */
@Document(collection = "utenti_permessi")
public class UtentePermesso {

    @Id
    private String id; // Identificativo univoco dell'associazione

    private String utenteId;  // ID dell'utente associato
    private String permessoId; // ID del permesso associato

    /**
     * Costruttore predefinito per la classe `UtentePermesso`.
     * <p>
     * Necessario per la deserializzazione da parte di MongoDB o framework come Jackson.
     */
    public UtentePermesso() {}

    /**
     * Costruttore completo per la classe `UtentePermesso`.
     *
     * @param utenteId  ID dell'utente.
     * @param permessoId ID del permesso.
     */
    public UtentePermesso(String utenteId, String permessoId) {
        this.utenteId = utenteId;
        this.permessoId = permessoId;
    }

    /**
     * Restituisce l'identificativo univoco dell'associazione.
     *
     * @return L'ID dell'associazione.
     */
    public String getId() {
        return id;
    }

    /**
     * Restituisce l'ID dell'utente associato.
     *
     * @return L'ID dell'utente.
     */
    public String getUtenteId() {
        return utenteId;
    }

    /**
     * Imposta l'ID dell'utente associato.
     *
     * @param utenteId L'ID dell'utente.
     */
    public void setUtenteId(String utenteId) {
        this.utenteId = utenteId;
    }

    /**
     * Restituisce l'ID del permesso associato.
     *
     * @return L'ID del permesso.
     */
    public String getPermessoId() {
        return permessoId;
    }

    /**
     * Imposta l'ID del permesso associato.
     *
     * @param permessoId L'ID del permesso.
     */
    public void setPermessoId(String permessoId) {
        this.permessoId = permessoId;
    }
}
