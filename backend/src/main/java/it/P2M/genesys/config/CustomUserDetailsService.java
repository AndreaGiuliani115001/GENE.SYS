package it.P2M.genesys.config;

import it.P2M.genesys.model.Utente;
import it.P2M.genesys.repository.UtenteRepository;
import org.springframework.security.core.userdetails.User;
import org.springframework.security.core.userdetails.UserDetails;
import org.springframework.security.core.userdetails.UserDetailsService;
import org.springframework.security.core.userdetails.UsernameNotFoundException;
import org.springframework.stereotype.Service;

@Service
public class CustomUserDetailsService implements UserDetailsService {

    private final UtenteRepository utenteRepository;

    public CustomUserDetailsService(UtenteRepository utenteRepository) {
        this.utenteRepository = utenteRepository;
    }

    @Override
    public UserDetails loadUserByUsername(String email) throws UsernameNotFoundException {

        if (email == null || email.isEmpty()) {
            System.out.println("Parametro email è vuoto o nullo!");
            throw new UsernameNotFoundException("Parametro email non valido!");
        }

        System.out.println("Email ricevuta: " + email);

        Utente utente = utenteRepository.findByEmail(email);

        if (utente == null) {
            System.out.println("Utente non trovato per email: " + email); // Log utente non trovato
            throw new UsernameNotFoundException("Utente non trovato con email: " + email);
        }

        // Usa il campo 'ruolo' come ruolo dell'utente
        String ruolo = utente.getRuolo(); // es. "ADMIN", "OPERATOR"

        return User.builder()
                .username(utente.getEmail())
                .password(utente.getPassword())
                .roles(ruolo) // Assegna il ruolo come ruolo Spring Security
                .build();
    }
}
