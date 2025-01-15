package it.P2M.genesys.config;

import org.springframework.context.annotation.Bean;
import org.springframework.context.annotation.Configuration;
import org.springframework.web.servlet.config.annotation.CorsRegistry;
import org.springframework.web.servlet.config.annotation.WebMvcConfigurer;

/**
 * Configurazione CORS (Cross-Origin Resource Sharing) per l'applicazione.
 *
 * Questa classe consente di definire quali richieste da domini esterni (origini)
 * sono consentite per accedere alle risorse del server.
 *
 * È essenziale per abilitare la comunicazione tra il frontend (es. React) e il backend
 * (Spring Boot), specialmente se sono ospitati su domini diversi.
 */
@Configuration
public class CorsConfig implements WebMvcConfigurer {

    /**
     * Configura le regole CORS per l'applicazione.
     *
     * @param registry l'oggetto CorsRegistry utilizzato per specificare le regole.
     */
    @Override
    public void addCorsMappings(CorsRegistry registry) {
        registry.addMapping("/**") // Consenti tutte le rotte
                .allowedOrigins("http://localhost:5173") // Permetti richieste dal dominio del frontend
                .allowedMethods("GET", "POST", "PUT", "DELETE", "OPTIONS") // Consenti specifici metodi HTTP
                .allowedHeaders("*") // Consenti tutti gli header HTTP
                .allowCredentials(true); // Consenti l'invio di credenziali (es. cookie, intestazioni di autenticazione)
    }
}
