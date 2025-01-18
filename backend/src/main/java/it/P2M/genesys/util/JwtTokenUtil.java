package it.P2M.genesys.util;

import io.jsonwebtoken.Jwts;
import io.jsonwebtoken.SignatureAlgorithm;
import io.jsonwebtoken.security.Keys;
import jakarta.servlet.http.HttpServletRequest;
import org.springframework.stereotype.Component;

import java.security.Key;
import java.util.Date;
import java.util.List;

@Component
public class JwtTokenUtil {

    // Chiave segreta per la firma del token
    private final Key SECRET_KEY = Keys.secretKeyFor(SignatureAlgorithm.HS512);

    // Tempo di scadenza del token (24 ore)
    private final long EXPIRATION_TIME = 86400000;

    /**
     * Genera un token JWT per un dato utente.
     *
     * @param email  Nome dell'utente.
     * @param role      Ruolo dell'utente.
     * @param permissions Lista di permessi (es. "visualizza_azienda_12345").
     * @return Il token JWT generato.
     */
    public String generateToken(String email, String role, String aziendaId, List<String> permissions) {
        return Jwts.builder()
                .setSubject(email) // Imposta lo username come subject
                .claim("role", role)// Aggiunge il ruolo
                .claim("aziendaId", aziendaId) //Aggiunge l'azienda
                .claim("permissions", permissions) // Aggiunge i permessi
                .setIssuedAt(new Date()) // Data di creazione
                .setExpiration(new Date(System.currentTimeMillis() + EXPIRATION_TIME)) // Data di scadenza
                .signWith(SECRET_KEY) // Firma il token con la chiave segreta
                .compact(); // Genera il token come stringa
    }

    /**
     * Estrae lo username dal token.
     *
     * @param token Il token JWT.
     * @return Lo username contenuto nel token.
     */
    public String extractUsername(String token) {
        return Jwts.parserBuilder()
                .setSigningKey(SECRET_KEY)
                .build()
                .parseClaimsJws(token)
                .getBody()
                .getSubject();
    }

    /**
     * Estrae il ruolo dal token.
     *
     * @param token Il token JWT.
     * @return Il ruolo contenuto nel token.
     */
    public String extractRole(String token) {
        return Jwts.parserBuilder()
                .setSigningKey(SECRET_KEY)
                .build()
                .parseClaimsJws(token)
                .getBody()
                .get("role", String.class);
    }

    /**
     * Estrae l'azienda dal token.
     *
     * @param token Il token JWT.
     * @return l'azienda contenuta nel token.
     */
    public String extractFactory(String token) {
        return Jwts.parserBuilder()
                .setSigningKey(SECRET_KEY)
                .build()
                .parseClaimsJws(token)
                .getBody()
                .get("aziendaId", String.class);
    }

    /**
     * Estrae i permessi dal token.
     *
     * @param token Il token JWT.
     * @return Una lista di permessi contenuti nel token.
     */
    public List<String> extractPermissions(String token) {
        return Jwts.parserBuilder()
                .setSigningKey(SECRET_KEY)
                .build()
                .parseClaimsJws(token)
                .getBody()
                .get("permissions", List.class);
    }

    /**
     * Valida un token JWT.
     *
     * @param token Il token JWT.
     * @return True se il token è valido, altrimenti False.
     */
    public boolean validateToken(String token) {
        try {
            Jwts.parserBuilder()
                    .setSigningKey(SECRET_KEY)
                    .build()
                    .parseClaimsJws(token);
            return true;
        } catch (Exception e) {
            System.err.println("Token non valido: " + e.getMessage());
            return false;
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
