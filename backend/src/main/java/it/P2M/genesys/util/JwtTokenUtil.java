package it.P2M.genesys.util;

import io.jsonwebtoken.Jwts;
import io.jsonwebtoken.SignatureAlgorithm;
import io.jsonwebtoken.security.Keys;
import jakarta.servlet.http.HttpServletRequest;
import org.springframework.stereotype.Component;

import java.security.Key;
import java.util.Date;

/**
 * Classe per la gestione dei token JWT (JSON Web Token).
 * <p>
 * Fornisce metodi per generare, validare e analizzare i token JWT.
 */
@Component
public class JwtTokenUtil {

    /**
     * Chiave segreta per la firma del token JWT.
     * Viene generata automaticamente utilizzando l'algoritmo HS512.
     */
    private final Key SECRET_KEY = Keys.secretKeyFor(SignatureAlgorithm.HS512);

    /**
     * Tempo di scadenza del token in millisecondi (24 ore).
     */
    private final long EXPIRATION_TIME = 86400000;

    /**
     * Estrae lo username (subject) dal token JWT.
     *
     * @param token Il token JWT.
     * @return Lo username contenuto nel token.
     */
    public String extractUsername(String token) {
        return Jwts.parserBuilder()
                .setSigningKey(SECRET_KEY) // Usa la chiave segreta per analizzare il token
                .build()
                .parseClaimsJws(token)
                .getBody()
                .getSubject(); // Restituisce il "subject" del token (lo username)
    }

    /**
     * Genera un token JWT per un dato username.
     *
     * @param username Lo username da includere nel token.
     * @return Una stringa che rappresenta il token JWT.
     */
    public String generateToken(String username, String role) {
        return Jwts.builder()
                .setSubject(username)
                .claim("role", role)// Imposta lo username come "subject"
                .setIssuedAt(new Date()) // Imposta la data di creazione
                .setExpiration(new Date(System.currentTimeMillis() + EXPIRATION_TIME)) // Imposta la scadenza
                .signWith(SECRET_KEY) // Firma il token con la chiave segreta
                .compact(); // Converte il token in una stringa
    }

    /**
     * Valida un token JWT controllandone la firma e la scadenza.
     *
     * @param token Il token JWT da validare.
     * @return `true` se il token è valido, altrimenti `false`.
     */
    public boolean validateToken(String token) {
        try {
            Jwts.parserBuilder()
                    .setSigningKey(SECRET_KEY) // Usa la chiave segreta per validare il token
                    .build()
                    .parseClaimsJws(token); // Analizza il token
            return true; // Token valido
        } catch (Exception e) {
            return false; // Token non valido
        }
    }

    /**
     * Estrae il token JWT dall'intestazione HTTP Authorization.
     *
     * @param request La richiesta HTTP.
     * @return Il token JWT se presente, altrimenti `null`.
     */
    public String extractToken(HttpServletRequest request) {
        String bearerToken = request.getHeader("Authorization");

        if (bearerToken != null && bearerToken.startsWith("Bearer ")) {
            return bearerToken.substring(7); // Rimuove "Bearer " e restituisce solo il token
        }
        return null; // Nessun token trovato
    }
}
