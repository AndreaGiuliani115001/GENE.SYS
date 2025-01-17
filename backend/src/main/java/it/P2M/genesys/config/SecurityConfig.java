package it.P2M.genesys.config;

import it.P2M.genesys.util.JwtTokenUtil;
import it.P2M.genesys.repository.UtenteRepository;
import org.springframework.context.annotation.Bean;
import org.springframework.context.annotation.Configuration;
import org.springframework.security.authentication.AuthenticationManager;
import org.springframework.security.config.Customizer;
import org.springframework.security.config.annotation.authentication.configuration.AuthenticationConfiguration;
import org.springframework.security.config.annotation.web.builders.HttpSecurity;
import org.springframework.security.config.annotation.web.configurers.AbstractHttpConfigurer;
import org.springframework.security.crypto.bcrypt.BCryptPasswordEncoder;
import org.springframework.security.crypto.password.PasswordEncoder;
import org.springframework.security.web.SecurityFilterChain;
import org.springframework.security.web.authentication.UsernamePasswordAuthenticationFilter;
import org.springframework.web.cors.CorsConfiguration;
import org.springframework.web.cors.CorsConfigurationSource;
import org.springframework.web.cors.UrlBasedCorsConfigurationSource;

/**
 * Configurazione di sicurezza per l'applicazione Spring Boot.
 * <p>
 * Questa classe configura Spring Security per gestire autenticazione, autorizzazione, CORS,
 * e l'integrazione del filtro di autenticazione JWT.
 */
@Configuration
public class SecurityConfig {

    private final JwtTokenUtil jwtTokenUtil;
    private final UtenteRepository utenteRepository;

    /**
     * Costruttore della configurazione di sicurezza.
     *
     * @param jwtTokenUtil     Utilità per la gestione dei token JWT.
     * @param utenteRepository Repository per il recupero degli utenti.
     */
    public SecurityConfig(JwtTokenUtil jwtTokenUtil, UtenteRepository utenteRepository) {
        this.jwtTokenUtil = jwtTokenUtil;
        this.utenteRepository = utenteRepository;
    }

    /**
     * Configura la catena di filtri di sicurezza.
     *
     * @param http                   Oggetto HttpSecurity per configurare le regole di sicurezza.
     * @param authenticationManager  Manager per la gestione dell'autenticazione.
     * @return Una SecurityFilterChain configurata.
     * @throws Exception In caso di errore durante la configurazione.
     */
    @Bean
    public SecurityFilterChain securityFilterChain(HttpSecurity http, AuthenticationManager authenticationManager) throws Exception {
        // Inizializza il filtro di autenticazione JWT
        JwtAuthenticationFilter jwtAuthenticationFilter = new JwtAuthenticationFilter(jwtTokenUtil, utenteRepository);

        http
                .cors(Customizer.withDefaults()) // Configura CORS con impostazioni predefinite
                .csrf(AbstractHttpConfigurer::disable) // Disabilita la protezione CSRF
                .authorizeHttpRequests(auth -> auth
                        // Endpoint pubblici
                        .requestMatchers("/auth/login").permitAll()

                        // Regole per master (accesso multi-azienda)
                        .requestMatchers("/dashboard/aziende/**").hasRole("MASTER")

                        // Regole per master e admin (gestione utenti)
                        .requestMatchers("/dashboard/utenti/**").hasAnyRole("MASTER", "ADMIN")

                        // Regole per project manager (linee di produzione e progetti)
                        .requestMatchers("/dashboard/linee-produzione/**", "/dashboard/progetti/**").hasRole("PROJECT_MANAGER")

                        // Regole per operatore (checklist)
                        .requestMatchers("/dashboard/checklist/**").hasRole("OPERATORE")

                        // Accesso autenticato per la dashboard principale
                        .requestMatchers("/dashboard").authenticated()

                        // Autenticazione richiesta per tutto il resto
                        .anyRequest().authenticated()
                )

                .addFilterBefore(jwtAuthenticationFilter, UsernamePasswordAuthenticationFilter.class) // Aggiungi il filtro JWT
                .formLogin(login -> login
                        .usernameParameter("email") // Configura il parametro per il nome utente
                        .passwordParameter("password") // Configura il parametro per la password
                        .loginProcessingUrl("/login") // Configura l'URL per l'elaborazione del login
                        .successHandler((request, response, authentication) -> {
                            // Genera un token JWT al login riuscito
                            String username = authentication.getName();
                            String role = authentication.getAuthorities().iterator().next().getAuthority();
                            String token = jwtTokenUtil.generateToken(username, role);

                            // Restituisce il token JWT come risposta JSON
                            response.setContentType("application/json");
                            response.setStatus(200);
                            response.getWriter().write("{\"token\": \"" + token + "\"}");
                        })
                        .failureHandler((request, response, exception) -> {
                            // Gestisce il fallimento del login
                            response.setContentType("application/json");
                            response.setStatus(401);
                            response.getWriter().write("{\"error\": \"Credenziali non valide!\"}");
                        })
                );
        return http.build();
    }

    /**
     * Configura la gestione delle richieste CORS (Cross-Origin Resource Sharing).
     *
     * @return Un CorsConfigurationSource configurato.
     */
    @Bean
    public CorsConfigurationSource corsConfigurationSource() {
        CorsConfiguration configuration = new CorsConfiguration();
        configuration.addAllowedOrigin("http://localhost:5173"); // Consenti richieste dal frontend React
        configuration.addAllowedMethod("*"); // Consenti tutti i metodi HTTP
        configuration.addAllowedHeader("*"); // Consenti tutti gli header HTTP
        configuration.setAllowCredentials(true); // Permetti credenziali (cookie, token di autenticazione)

        UrlBasedCorsConfigurationSource source = new UrlBasedCorsConfigurationSource();
        source.registerCorsConfiguration("/**", configuration); // Applica CORS a tutte le rotte
        return source;
    }

    /**
     * Bean per il gestore dell'autenticazione.
     *
     * @param authenticationConfiguration Configurazione di autenticazione.
     * @return L'oggetto AuthenticationManager.
     * @throws Exception In caso di errore.
     */
    @Bean
    public AuthenticationManager authenticationManager(AuthenticationConfiguration authenticationConfiguration) throws Exception {
        return authenticationConfiguration.getAuthenticationManager();
    }

    /**
     * Bean per l'encoder delle password.
     *
     * @return Un oggetto PasswordEncoder che utilizza BCrypt.
     */
    @Bean
    public PasswordEncoder passwordEncoder() {
        return new BCryptPasswordEncoder();
    }
}
