package it.P2M.genesys.config;

import it.P2M.genesys.model.CustomUserDetails;
import it.P2M.genesys.repository.UtenteRepository;
import it.P2M.genesys.util.JwtTokenUtil;
import jakarta.servlet.FilterChain;
import jakarta.servlet.ServletException;
import jakarta.servlet.http.HttpServletRequest;
import jakarta.servlet.http.HttpServletResponse;
import org.springframework.security.authentication.UsernamePasswordAuthenticationToken;
import org.springframework.security.core.GrantedAuthority;
import org.springframework.security.core.authority.SimpleGrantedAuthority;
import org.springframework.security.core.context.SecurityContextHolder;
import org.springframework.stereotype.Component;
import org.springframework.web.filter.OncePerRequestFilter;

import java.io.IOException;
import java.util.ArrayList;
import java.util.List;

/**
 * Filtro per la gestione dell'autenticazione tramite JWT.
 */
@Component
public class JwtAuthenticationFilter extends OncePerRequestFilter {

    private final JwtTokenUtil jwtTokenUtil;
    private final UtenteRepository utenteRepository;

    public JwtAuthenticationFilter(JwtTokenUtil jwtTokenUtil, UtenteRepository utenteRepository) {
        this.jwtTokenUtil = jwtTokenUtil;
        this.utenteRepository = utenteRepository;
    }

    @Override
    protected void doFilterInternal(HttpServletRequest request, HttpServletResponse response, FilterChain filterChain)
            throws ServletException, IOException {

        // Estrai il token dall'header Authorization
        String token = jwtTokenUtil.extractToken(request);
        System.out.println("JwtAuthenticationFilter - Token estratto: " + token);

        if (token != null && jwtTokenUtil.validateToken(token)) {
            // Estrai le informazioni dal token
            String username = jwtTokenUtil.extractUsername(token);
            String aziendaId = jwtTokenUtil.extractFactory(token);
            String role = jwtTokenUtil.extractRole(token);
            List<String> permissions = jwtTokenUtil.extractPermissions(token);

            System.out.println("JwtAuthenticationFilter - Dettagli utente dal token:");
            System.out.println("Username: " + username);
            System.out.println("Role: " + role);
            System.out.println("Azienda ID: " + aziendaId);
            System.out.println("Permessi: " + permissions);

            // Costruisci l'elenco delle autorità (ruolo + permessi)
            List<GrantedAuthority> authorities = new ArrayList<>();
            authorities.add(new SimpleGrantedAuthority(role)); // Aggiungi il ruolo
            permissions.forEach(permission -> authorities.add(new SimpleGrantedAuthority(permission))); // Aggiungi i permessi

            // Crea un oggetto CustomUserDetails
            CustomUserDetails userDetails = new CustomUserDetails(
                    username != null ? username : "unknown_user", // Valore predefinito
                    "", // Password vuota (non necessaria per JWT)
                    authorities != null ? authorities : List.of(), // Lista vuota se null
                    "Utente Sconosciuto", // Nome predefinito (nel caso fosse null)
                    aziendaId != null ? aziendaId : "N/A", // ID azienda predefinito
                    permissions != null ? permissions : List.of() // Lista vuota se null
            );

            // Crea il token di autenticazione
            UsernamePasswordAuthenticationToken authToken =
                    new UsernamePasswordAuthenticationToken(userDetails, null, authorities);

            // Imposta l'autenticazione nel contesto di sicurezza
            SecurityContextHolder.getContext().setAuthentication(authToken);
            System.out.println("JwtAuthenticationFilter - Utente autenticato: " + username);

        } else {
            System.out.println("JwtAuthenticationFilter - Token non valido o mancante.");
        }

        // Passa al filtro successivo nella catena
        filterChain.doFilter(request, response);
    }
}
