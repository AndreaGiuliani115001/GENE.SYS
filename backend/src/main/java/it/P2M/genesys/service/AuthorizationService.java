package it.P2M.genesys.service;

import it.P2M.genesys.model.CustomUserDetails;
import it.P2M.genesys.model.Permesso;
import it.P2M.genesys.model.Utente;
import it.P2M.genesys.repository.UtenteRepository;
import org.springframework.stereotype.Service;

import java.util.ArrayList;
import java.util.List;

@Service
public class AuthorizationService {

    private final UtenteRepository utenteRepository;

    public AuthorizationService(UtenteRepository utenteRepository) {
        this.utenteRepository = utenteRepository;
    }

    public boolean hasPermission(String email, String azione, String entita, String entitaId) {
        // Recupera l'utente dal database
        Utente utente = utenteRepository.findByEmail(email);


        // Combina i permessi globali e specifici
        List<Permesso> permessiEffettivi = getEffectivePermissions(utente);

        // Verifica i permessi
        return permessiEffettivi.stream().anyMatch(permesso ->
                permesso.getAzione().equals(azione) &&
                        permesso.getEntita().equals(entita) &&
                        (permesso.getEntitaId() == null || permesso.getEntitaId().equals(entitaId))
        );
    }

    private List<Permesso> getEffectivePermissions(Utente utente) {
        // Combina i permessi del ruolo con quelli personalizzati
        List<Permesso> permessi = new ArrayList<>(utente.getRuolo().getPermessiPredefiniti());
        permessi.addAll(utente.getPermessiAggiuntivi());
        permessi.removeAll(utente.getPermessiLimitati());
        return permessi;
    }
}

