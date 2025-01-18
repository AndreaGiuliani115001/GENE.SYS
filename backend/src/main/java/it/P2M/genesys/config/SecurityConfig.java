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
 * Configurazione di sicurezza per GENE.SYS.
 */
@Configuration
public class SecurityConfig {

    private final JwtTokenUtil jwtTokenUtil;
    private final UtenteRepository utenteRepository;

    public SecurityConfig(JwtTokenUtil jwtTokenUtil, UtenteRepository utenteRepository) {
        this.jwtTokenUtil = jwtTokenUtil;
        this.utenteRepository = utenteRepository;
    }

    @Bean
    public SecurityFilterChain securityFilterChain(HttpSecurity http) throws Exception {
        // Configura il filtro JWT
        JwtAuthenticationFilter jwtAuthenticationFilter = new JwtAuthenticationFilter(jwtTokenUtil, utenteRepository);

        http
                .cors(Customizer.withDefaults()) // Configurazione CORS predefinita
                .csrf(AbstractHttpConfigurer::disable) // Disabilita CSRF
                .authorizeHttpRequests(auth -> auth
                        // Endpoint pubblici
                        .requestMatchers("/auth/login").permitAll()

                        // Accesso basato sui ruoli
                        .requestMatchers("/dashboard").hasAnyRole("MASTER", "ADMIN", "PROJECT_MANAGER", "OPERATORE")
                        .requestMatchers("/dashboard/aziende/**").hasRole("MASTER") // Accesso solo a MASTER
                        .requestMatchers("/dashboard/utenti/**").hasAnyRole("MASTER", "ADMIN") // Accesso a MASTER e ADMIN

                        // Protezione di default per gli altri endpoint
                        .anyRequest().authenticated()
                )
                .addFilterBefore(jwtAuthenticationFilter, UsernamePasswordAuthenticationFilter.class); // Aggiunge il filtro JWT

        return http.build();
    }

    @Bean
    public CorsConfigurationSource corsConfigurationSource() {
        CorsConfiguration configuration = new CorsConfiguration();
        configuration.addAllowedOrigin("http://localhost:5173"); // Permetti richieste dal frontend
        configuration.addAllowedMethod("*");
        configuration.addAllowedHeader("*");
        configuration.setAllowCredentials(true); // Consenti credenziali

        UrlBasedCorsConfigurationSource source = new UrlBasedCorsConfigurationSource();
        source.registerCorsConfiguration("/**", configuration);
        return source;
    }

    @Bean
    public AuthenticationManager authenticationManager(AuthenticationConfiguration configuration) throws Exception {
        return configuration.getAuthenticationManager();
    }

    @Bean
    public PasswordEncoder passwordEncoder() {
        return new BCryptPasswordEncoder();
    }
}
