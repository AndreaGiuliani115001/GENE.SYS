package it.P2M.genesys.service;

import it.P2M.genesys.model.CustomUserDetails;
import it.P2M.genesys.model.Permesso;
import it.P2M.genesys.model.Utente;
import it.P2M.genesys.repository.UtenteRepository;
import org.springframework.security.core.authority.SimpleGrantedAuthority;
import org.springframework.security.core.userdetails.UserDetails;
import org.springframework.security.core.userdetails.UserDetailsService;
import org.springframework.security.core.userdetails.UsernameNotFoundException;
import org.springframework.stereotype.Service;

import java.util.ArrayList;
import java.util.HashSet;
import java.util.List;
import java.util.Set;

@Service
public class CustomUserDetailsService implements UserDetailsService {


    private final UtenteRepository utenteRepository;

    public CustomUserDetailsService(UtenteRepository utenteRepository) {
        this.utenteRepository = utenteRepository;
    }

    @Override
    public UserDetails loadUserByUsername(String email) throws UsernameNotFoundException {

        System.out.println("CustomUserDetailsService - Caricamento utente con email: " + email);

        if (email == null || email.isEmpty()) {
            throw new UsernameNotFoundException("Parametro email non valido!");
        }

        Utente utente = utenteRepository.findByEmail(email);
        if (utente == null) {
            System.out.println("CustomUserDetailsService - Utente non trovato: " + email);
            throw new UsernameNotFoundException("Utente non trovato con email: " + email);
        }

        // Recupera il ruolo e i permessi effettivi
        String ruolo = utente.getRuolo().getNome(); // Es. ROLE_ADMIN
        List<Permesso> permessiEffettivi = getEffectivePermissions(utente);

        // Costruisci l'elenco delle autorità (ruolo + permessi)
        List<SimpleGrantedAuthority> authorities = new ArrayList<>();
        authorities.add(new SimpleGrantedAuthority(ruolo)); // Aggiungi il ruolo
        List<String> permessiStringList = new ArrayList<>(); // Per raccogliere i permessi in formato stringa
        for (Permesso permesso : permessiEffettivi) {
            String authority = permesso.getAzione() + "_" + permesso.getEntita();
            if (permesso.getEntitaId() != null) {
                authority += "_" + permesso.getEntitaId();
            }
            authorities.add(new SimpleGrantedAuthority(authority)); // Aggiungi il permesso come authority
            permessiStringList.add(authority); // Aggiungi il permesso alla lista di stringhe
        }

        // Recupera dettagli aggiuntivi
        String nome = utente.getNome();
        String aziendaId = utente.getAziendaId() != null ? utente.getAziendaId().getId() : null;

        System.out.println("CustomUserDetailsService - Dettagli utente trovati:");
        System.out.println("Nome: " + nome);
        System.out.println("Ruolo: " + ruolo);
        System.out.println("Azienda: " + aziendaId);
        System.out.println("Permessi: " + permessiStringList);

        // Ritorna il CustomUserDetails con i permessi stringa inclusi
        return new CustomUserDetails(
                utente.getEmail(),
                utente.getPassword(),
                authorities,
                nome,
                aziendaId,
                permessiStringList // Passa la lista di permessi come stringhe
        );
    }


    private List<Permesso> getEffectivePermissions(Utente utente) {
        List<Permesso> permessi = new ArrayList<>(utente.getRuolo().getPermessiPredefiniti());
        permessi.addAll(utente.getPermessiAggiuntivi());
        permessi.removeAll(utente.getPermessiLimitati());
        return permessi;
    }
}
