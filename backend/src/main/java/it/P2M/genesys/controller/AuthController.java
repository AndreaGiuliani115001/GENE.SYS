package it.P2M.genesys.controller;

import it.P2M.genesys.util.JwtTokenUtil;
import org.springframework.http.HttpStatus;
import org.springframework.http.ResponseEntity;
import org.springframework.security.authentication.AuthenticationManager;
import org.springframework.security.authentication.UsernamePasswordAuthenticationToken;
import org.springframework.security.core.Authentication;
import org.springframework.security.core.AuthenticationException;
import org.springframework.security.core.context.SecurityContextHolder;
import org.springframework.web.bind.annotation.*;
import org.springframework.web.server.ResponseStatusException;

import java.util.HashMap;
import java.util.Map;

/**
 * Controller per la gestione delle operazioni di autenticazione.
 * <p>
 * Fornisce endpoint per l'accesso (`/auth/login`) e l'emissione di token JWT.
 */
@RestController
@RequestMapping("/auth")
public class AuthController {

    private final AuthenticationManager authenticationManager;
    private final JwtTokenUtil jwtTokenUtil;

    /**
     * Costruttore del controller di autenticazione.
     *
     * @param authenticationManager Gestore per l'autenticazione.
     * @param jwtTokenUtil           Utilità per la gestione dei token JWT.
     */
    public AuthController(AuthenticationManager authenticationManager, JwtTokenUtil jwtTokenUtil) {
        this.authenticationManager = authenticationManager;
        this.jwtTokenUtil = jwtTokenUtil;
    }

    /**
     * Endpoint per il login dell'utente.
     * <p>
     * Questo metodo verifica le credenziali inviate e, se corrette, genera un token JWT.
     *
     * @param loginRequest Una mappa contenente i campi `email` e `password`.
     * @return Una risposta contenente il token JWT in formato JSON.
     * @throws ResponseStatusException Se i campi sono mancanti o le credenziali non sono valide.
     */
    @PostMapping("/login")
    public ResponseEntity<Map<String, String>> login(@RequestBody Map<String, String> loginRequest) {
        // Estrae email e password dalla richiesta
        String email = loginRequest.get("email");
        String password = loginRequest.get("password");

        // Verifica che i campi non siano nulli o vuoti
        if (email == null || email.isBlank() || password == null || password.isBlank()) {
            throw new ResponseStatusException(HttpStatus.BAD_REQUEST, "Email e password sono obbligatori");
        }

        try {
            // Autentica l'utente utilizzando AuthenticationManager
            Authentication authentication = authenticationManager.authenticate(
                    new UsernamePasswordAuthenticationToken(email, password)
            );
            // Imposta l'autenticazione nel contesto di sicurezza
            SecurityContextHolder.getContext().setAuthentication(authentication);

            // Genera il token JWT utilizzando il nome utente
            String token = jwtTokenUtil.generateToken(authentication.getName());

            // Prepara la risposta con il token
            Map<String, String> response = new HashMap<>();
            response.put("token", token);
            return ResponseEntity.ok(response);
        } catch (AuthenticationException e) {
            // Gestisce errori di autenticazione
            throw new ResponseStatusException(HttpStatus.UNAUTHORIZED, "Credenziali non valide!");
        }
    }
}
