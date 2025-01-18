package it.P2M.genesys.controller;

import it.P2M.genesys.model.Permesso;
import it.P2M.genesys.model.Utente;
import it.P2M.genesys.repository.UtenteRepository;
import it.P2M.genesys.util.JwtTokenUtil;
import org.springframework.http.ResponseEntity;
import org.springframework.security.crypto.password.PasswordEncoder;
import org.springframework.web.bind.annotation.*;

import java.util.ArrayList;
import java.util.List;
import java.util.Map;

@RestController
@RequestMapping("/auth")
public class AuthController {

    private final UtenteRepository utenteRepository;
    private final PasswordEncoder passwordEncoder;
    private final JwtTokenUtil jwtTokenUtil;

    public AuthController(UtenteRepository utenteRepository, PasswordEncoder passwordEncoder, JwtTokenUtil jwtTokenUtil) {
        this.utenteRepository = utenteRepository;
        this.passwordEncoder = passwordEncoder;
        this.jwtTokenUtil = jwtTokenUtil;
    }

    @PostMapping("/login")
    public ResponseEntity<?> login(@RequestBody Map<String, String> credentials) {
        String email = credentials.get("email");
        String password = credentials.get("password");

        // Validazione input
        if (email == null || password == null) {
            return ResponseEntity.badRequest().body("Email o password mancanti");
        }

        // Recupera l'utente dal database
        Utente utente = utenteRepository.findByEmail(email);
        if (utente == null) {
            return ResponseEntity.status(404).body("Utente non trovato");
        }

        // Verifica la password
        if (!passwordEncoder.matches(password, utente.getPassword())) {
            return ResponseEntity.status(401).body("Credenziali non valide");
        }

        // Recupera ruolo, permessi effettivi e azienda.
        String role = utente.getRuolo().getNome(); // Es. ROLE_ADMIN
        System.out.println("Azienda associata: " + utente.getAziendaId());
        String aziendaId = utente.getAziendaId() != null ? utente.getAziendaId().getId() : null;
        List<Permesso> permessiEffettivi = getEffectivePermissions(utente);

        // Converte i permessi in stringhe
        List<String> permissions = new ArrayList<>();
        for (Permesso permesso : permessiEffettivi) {
            String permissionString = permesso.getAzione() + "_" + permesso.getEntita();
            if (permesso.getEntitaId() != null) {
                permissionString += "_" + permesso.getEntitaId();
            }
            permissions.add(permissionString);
        }

        // Genera il token JWT
        String token = jwtTokenUtil.generateToken(email, role, aziendaId, permissions);

        // Restituisce il token come risposta JSON
        Map<String, String> response = Map.of("token", token);
        return ResponseEntity.ok(response);
    }


    // Combina i permessi del ruolo con quelli aggiuntivi/limitati
    private List<Permesso> getEffectivePermissions(Utente utente) {
        List<Permesso> permessi = new ArrayList<>(utente.getRuolo().getPermessiPredefiniti());
        permessi.addAll(utente.getPermessiAggiuntivi());
        permessi.removeAll(utente.getPermessiLimitati());
        return permessi;
    }
}
