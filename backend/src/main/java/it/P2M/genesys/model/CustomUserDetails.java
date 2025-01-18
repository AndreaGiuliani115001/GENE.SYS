package it.P2M.genesys.model;

import org.springframework.security.core.GrantedAuthority;
import org.springframework.security.core.userdetails.User;

import java.util.Collection;
import java.util.List;

/**
 * Classe personalizzata per rappresentare i dettagli di un utente autenticato.
 * <p>
 * Estende la classe {@link User} di Spring Security per includere informazioni aggiuntive
 * come nome, aziendaId e permessi.
 */
public class CustomUserDetails extends User {

    private String nome; // Nome dell'utente
    private String aziendaId; // ID dell'azienda associata all'utente
    private Collection<String> permessi; // Permessi effettivi dell'utente

    /**
     * Costruttore completo per la classe `CustomUserDetails`.
     *
     * @param username   Email o identificativo dell'utente.
     * @param password   Password dell'utente (può essere null in questa implementazione).
     * @param authorities Elenco delle autorità (ruoli o permessi) assegnati all'utente.
     * @param nome        Nome dell'utente.
     * @param aziendaId   ID dell'azienda associata all'utente.
     * @param permessi    Lista di permessi effettivi assegnati all'utente.
     */
    public CustomUserDetails(String username, String password, Collection<? extends GrantedAuthority> authorities,
                             String nome, String aziendaId, Collection<String> permessi) {
        super(
                username != null ? username : "unknown_user", // Default username
                password != null ? password : "", // Default password
                authorities != null ? authorities : List.of() // Default authorities
        );
        this.nome = nome != null ? nome : "Utente Sconosciuto"; // Default nome
        this.aziendaId = aziendaId != null ? aziendaId : "N/A"; // Default azienda ID
        this.permessi = permessi != null ? permessi : List.of(); // Default permessi
    }


    // Getter e Setter
    public String getNome() {
        return nome;
    }

    public void setNome(String nome) {
        this.nome = nome;
    }

    public String getAziendaId() {
        return aziendaId;
    }

    public void setAziendaId(String aziendaId) {
        this.aziendaId = aziendaId;
    }

    public Collection<String> getPermessi() {
        return permessi;
    }

    public void setPermessi(Collection<String> permessi) {
        this.permessi = permessi;
    }

    @Override
    public String toString() {
        return "CustomUserDetails{" +
                "nome='" + nome + '\'' +
                ", aziendaId='" + aziendaId + '\'' +
                ", permessi=" + permessi +
                '}';
    }
}
