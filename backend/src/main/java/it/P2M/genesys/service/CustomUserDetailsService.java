package it.P2M.genesys.service;

import it.P2M.genesys.model.CustomUserDetails;
import it.P2M.genesys.model.Utente;
import it.P2M.genesys.repository.UtenteRepository;
import org.springframework.security.core.authority.SimpleGrantedAuthority;
import org.springframework.security.core.userdetails.UserDetails;
import org.springframework.security.core.userdetails.UserDetailsService;
import org.springframework.security.core.userdetails.UsernameNotFoundException;
import org.springframework.stereotype.Service;

import java.util.List;

/**
 * Servizio personalizzato per caricare i dettagli dell'utente per l'autenticazione.
 * <p>
 * Implementa {@link UserDetailsService}, un'interfaccia di Spring Security,
 * per fornire un'implementazione personalizzata del metodo `loadUserByUsername`.
 */
@Service
public class CustomUserDetailsService implements UserDetailsService {

    private final UtenteRepository utenteRepository;

    /**
     * Costruttore per iniettare il repository degli utenti.
     *
     * @param utenteRepository Il repository per accedere ai dati degli utenti.
     */
    public CustomUserDetailsService(UtenteRepository utenteRepository) {
        this.utenteRepository = utenteRepository;
    }

    /**
     * Carica un utente in base alla sua email.
     * <p>
     * Questo metodo viene utilizzato da Spring Security per autenticare un utente
     * e costruire un'istanza di {@link UserDetails}.
     *
     * @param email L'email dell'utente.
     * @return I dettagli dell'utente come {@link UserDetails}.
     * @throws UsernameNotFoundException Se l'utente non viene trovato o se l'email è invalida.
     */
    @Override
    public UserDetails loadUserByUsername(String email) throws UsernameNotFoundException {
        // Assicurati che l'email non sia nulla o vuota
        if (email == null || email.isEmpty()) {
            throw new UsernameNotFoundException("Parametro email non valido!");
        }

        // Cerca l'utente nel repository in base all'email
        Utente utente = utenteRepository.findByEmail(email);

        // Se l'utente non viene trovato, lancia un'eccezione
        if (utente == null) {
            throw new UsernameNotFoundException("Utente non trovato con email: " + email);
        }

        // Prepara il ruolo con il prefisso `ROLE_` richiesto da Spring Security
        String ruolo = utente.getRuolo();

        // Recupera i dettagli aggiuntivi dell'utente
        String nome = utente.getNome();
        String cognome = utente.getCognome();
        String aziendaId = utente.getAziendaId() != null ? utente.getAziendaId().getId() : null;

        // Log di debug per verificare i dettagli dell'utente
        System.out.println("Nome: " + nome + ", Cognome: " + cognome + ", Azienda ID: " + aziendaId);
        System.out.println("Ruolo trovato in CustomUserDetails: " + ruolo);

        // Restituisci un'istanza di `CustomUserDetails` con i dati dell'utente
        return new CustomUserDetails(
                utente.getEmail(),
                utente.getPassword(),
                List.of(new SimpleGrantedAuthority(ruolo)), // Assegna il ruolo come authority
                nome,
                cognome,
                aziendaId
        );
    }
}
