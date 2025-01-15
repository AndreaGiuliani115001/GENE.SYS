package it.P2M.genesys.config;

import it.P2M.genesys.model.CustomUserDetails;
import it.P2M.genesys.model.Utente;
import it.P2M.genesys.repository.UtenteRepository;
import it.P2M.genesys.util.JwtTokenUtil;
import jakarta.servlet.FilterChain;
import jakarta.servlet.ServletException;
import jakarta.servlet.http.HttpServletRequest;
import jakarta.servlet.http.HttpServletResponse;
import org.springframework.security.authentication.UsernamePasswordAuthenticationToken;
import org.springframework.security.core.authority.SimpleGrantedAuthority;
import org.springframework.security.core.context.SecurityContextHolder;
import org.springframework.stereotype.Component;
import org.springframework.web.filter.OncePerRequestFilter;

import java.io.IOException;
import java.util.List;

/**
 * Filtro di autenticazione JWT.
 * <p>
 * Questo filtro intercetta ogni richiesta HTTP e verifica se contiene un token JWT valido.
 * Se il token è valido, estrae i dettagli dell'utente e li imposta nel contesto di sicurezza
 * di Spring Security.
 */
@Component
public class JwtAuthenticationFilter extends OncePerRequestFilter {

    private final JwtTokenUtil jwtTokenUtil;
    private final UtenteRepository utenteRepository;

    /**
     * Costruttore del filtro di autenticazione JWT.
     *
     * @param jwtTokenUtil     Utilità per la gestione dei token JWT.
     * @param utenteRepository Repository per recuperare i dettagli degli utenti dal database.
     */
    public JwtAuthenticationFilter(JwtTokenUtil jwtTokenUtil, UtenteRepository utenteRepository) {
        this.jwtTokenUtil = jwtTokenUtil;
        this.utenteRepository = utenteRepository;
    }

    /**
     * Metodo principale del filtro che viene chiamato per ogni richiesta HTTP.
     *
     * @param request     La richiesta HTTP in ingresso.
     * @param response    La risposta HTTP in uscita.
     * @param filterChain La catena di filtri da eseguire.
     * @throws ServletException In caso di errore durante l'elaborazione.
     * @throws IOException      In caso di errore di input/output.
     */
    @Override
    protected void doFilterInternal(HttpServletRequest request, HttpServletResponse response, FilterChain filterChain)
            throws ServletException, IOException {

        System.out.println("JwtAuthenticationFilter - Header Authorization: " + request.getHeader("Authorization"));

        // Recupera l'header Authorization dalla richiesta
        String authHeader = request.getHeader("Authorization");

        // Controlla se l'header è presente e inizia con "Bearer "
        if (authHeader != null && authHeader.startsWith("Bearer ")) {
            // Estrae il token dall'header
            String token = authHeader.substring(7);
            System.out.println("Token JWT estratto: " + token);

            // Estrae il nome utente dal token
            String username = jwtTokenUtil.extractUsername(token);
            System.out.println("Username estratto dal token: " + username);

            // Verifica che il nome utente non sia nullo e che il contesto di sicurezza sia vuoto
            if (username != null && SecurityContextHolder.getContext().getAuthentication() == null) {
                // Recupera i dettagli dell'utente dal repository
                Utente utente = utenteRepository.findByEmail(username);
                if (utente != null) {
                // Determina il ruolo dell'utente e lo formatta
                String ruolo = "ROLE_" + utente.getRuolo();

                // Crea una lista di autorità (ruoli) per l'utente
                List<SimpleGrantedAuthority> authorities = List.of(new SimpleGrantedAuthority(ruolo));

                // Crea un'istanza di CustomUserDetails per rappresentare l'utente
                CustomUserDetails customUserDetails = new CustomUserDetails(
                        utente.getEmail(),          // Email come username
                        utente.getPassword(),       // Password (potenzialmente nullata)
                        authorities,                // Autorità (ruoli)
                        utente.getNome(),           // Nome dell'utente
                        utente.getCognome(),        // Cognome dell'utente
                        utente.getAziendaId().getId() // ID dell'azienda associata
                );

                // Crea un token di autenticazione con i dettagli dell'utente
                UsernamePasswordAuthenticationToken authToken = new UsernamePasswordAuthenticationToken(
                        customUserDetails, null, authorities
                );

                // Imposta il token nel contesto di sicurezza
                SecurityContextHolder.getContext().setAuthentication(authToken);
                System.out.println("Utente autenticato e aggiunto al contesto di sicurezza.");
                }
            }
        }

        // Prosegue con la catena di filtri
        filterChain.doFilter(request, response);
    }
}
