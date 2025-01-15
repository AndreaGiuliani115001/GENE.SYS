package it.P2M.genesys.model;

import org.springframework.security.core.GrantedAuthority;
import org.springframework.security.core.userdetails.User;

import java.util.Collection;

/**
 * Classe personalizzata per rappresentare i dettagli di un utente autenticato.
 * <p>
 * Estende la classe {@link User} di Spring Security, aggiungendo attributi personalizzati come
 * `nome`, `cognome` e `aziendaId`.
 */
public class CustomUserDetails extends User {

    private String nome; // Nome dell'utente
    private String cognome; // Cognome dell'utente
    private String aziendaId; // ID dell'azienda associata all'utente

    /**
     * Costruttore completo per la classe `CustomUserDetails`.
     *
     * @param username   Nome utente (email o altro identificativo).
     * @param password   Password dell'utente.
     * @param authorities Elenco delle autorità (ruoli o permessi) assegnate all'utente.
     * @param nome        Nome dell'utente.
     * @param cognome     Cognome dell'utente.
     * @param aziendaId   ID dell'azienda associata all'utente.
     */
    public CustomUserDetails(String username, String password, Collection<? extends GrantedAuthority> authorities,
                             String nome, String cognome, String aziendaId) {
        super(username, password, authorities); // Inizializza i campi della classe padre
        this.nome = nome;
        this.cognome = cognome;
        this.aziendaId = aziendaId;
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
     * Restituisce il cognome dell'utente.
     *
     * @return Il cognome dell'utente.
     */
    public String getCognome() {
        return cognome;
    }

    /**
     * Restituisce l'ID dell'azienda associata all'utente.
     *
     * @return L'ID dell'azienda associata.
     */
    public String getAziendaId() {
        return aziendaId;
    }
}
