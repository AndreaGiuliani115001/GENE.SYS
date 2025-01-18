package it.P2M.genesys.model;

import org.springframework.data.annotation.Id;
import org.springframework.data.mongodb.core.mapping.Document;
import org.springframework.data.mongodb.core.mapping.DBRef;

import java.util.ArrayList;
import java.util.List;

/**
 * Rappresenta un utente del sistema.
 * <p>
 * Ogni utente è associato a un ruolo e a un'azienda. Questa classe è mappata
 * nella collezione `utenti` del database MongoDB.
 */
@Document(collection = "utenti")
public class Utente {

    @Id
    private String id; // Identificativo univoco dell'utente

    private String nome; // Nome dell'utente
    private String cognome; // Cognome dell'utente
    private String email; // Email dell'utente
    private String password; // Password dell'utente (criptata)

    @DBRef
    private Ruolo ruolo; // Ruolo dell'utente (es. ROLE_ADMIN, ROLE_MASTER, ROLE_PROJECT_MANAGER, ROLE_OPERATORE)

    @DBRef
    private Azienda aziendaId; // Riferimento all'azienda associata all'utente

    @DBRef
    private List<Permesso> permessiAggiuntivi; // Permessi aggiuntivi personalizzati

    @DBRef
    private List<Permesso> permessiLimitati;  // Permessi da rimuovere rispetto al ruolo

    /**
     * Costruttore predefinito per la classe `Utente`.
     * <p>
     * Necessario per la deserializzazione da parte di MongoDB o framework come Jackson.
     */
    public Utente() {
    }

    /**
     * Costruttore completo per la classe `Utente`.
     *
     * @param nome     Nome dell'utente.
     * @param cognome  Cognome dell'utente.
     * @param email    Email dell'utente.
     * @param ruolo    Ruolo dell'utente (es. admin, operatore).
     * @param password Password dell'utente.
     */
    public Utente(String nome, String cognome, String email, Ruolo ruolo, String password) {
        this.nome = nome;
        this.cognome = cognome;
        this.email = email;
        this.ruolo = ruolo;
        this.password = password;
    }

    /**
     * Restituisce l'identificativo univoco dell'utente.
     *
     * @return L'ID dell'utente.
     */
    public String getId() {
        return id;
    }

    /**
     * Restituisce il nome dell'utente.
     *
     * @return Il nome dell'utente.
     */
    public String getNome() {
        return nome;
    }

    /**
     * Imposta il nome dell'utente.
     *
     * @param nome Il nome da impostare.
     */
    public void setNome(String nome) {
        this.nome = nome;
    }

    /**
     * Restituisce il cognome dell'utente.
     *
     * @return Il cognome dell'utente.
     */
    public String getCognome() {
        return cognome;
    }

    /**
     * Imposta il cognome dell'utente.
     *
     * @param cognome Il cognome da impostare.
     */
    public void setCognome(String cognome) {
        this.cognome = cognome;
    }

    /**
     * Restituisce l'email dell'utente.
     *
     * @return L'email dell'utente.
     */
    public String getEmail() {
        return email;
    }

    /**
     * Imposta l'email dell'utente.
     *
     * @param email L'email da impostare.
     */
    public void setEmail(String email) {
        this.email = email;
    }

    /**
     * Restituisce il ruolo dell'utente.
     *
     * @return Il ruolo dell'utente (es. admin, project manager, operatore).
     */
    public Ruolo getRuolo() {
        return ruolo;
    }

    /**
     * Imposta il ruolo dell'utente.
     *
     * @param ruolo Il ruolo da impostare.
     */
    public void setRuolo(Ruolo ruolo) {
        this.ruolo = ruolo;
    }

    /**
     * Restituisce la password dell'utente.
     *
     * @return La password dell'utente.
     */
    public String getPassword() {
        return password;
    }

    /**
     * Imposta la password dell'utente.
     *
     * @param password La password da impostare.
     */
    public void setPassword(String password) {
        this.password = password;
    }

    /**
     * Restituisce l'azienda associata all'utente.
     *
     * @return L'azienda dell'utente.
     */
    public Azienda getAziendaId() {
        return aziendaId;
    }

    /**
     * Imposta l'azienda associata all'utente.
     *
     * @param aziendaId L'azienda da impostare.
     */
    public void setAziendaId(Azienda aziendaId) {
        this.aziendaId = aziendaId;
    }

    public List<Permesso> getPermessiAggiuntivi() {
        return permessiAggiuntivi;
    }

    public void setPermessiAggiuntivi(List<Permesso> permessiAggiuntivi) {
        this.permessiAggiuntivi = permessiAggiuntivi;
    }

    public List<Permesso> getPermessiLimitati() {
        return permessiLimitati;
    }

    public void setPermessiLimitati(List<Permesso> permessiLimitati) {
        this.permessiLimitati = permessiLimitati;
    }
}
