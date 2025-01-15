package it.P2M.genesys.controller;

import it.P2M.genesys.model.CustomUserDetails;
import it.P2M.genesys.repository.AziendaRepository;
import it.P2M.genesys.repository.PermessoRepository;
import it.P2M.genesys.repository.UtenteRepository;
import org.springframework.security.core.Authentication;
import org.springframework.web.bind.annotation.GetMapping;
import org.springframework.web.bind.annotation.RequestMapping;
import org.springframework.web.bind.annotation.RestController;

import java.util.HashMap;
import java.util.Map;

/**
 * Controller per la gestione dei dati della dashboard.
 * <p>
 * Fornisce endpoint per ottenere statistiche e informazioni personalizzate in base al ruolo dell'utente.
 */
@RestController
@RequestMapping("/dashboard")
public class DashboardController {

    private final AziendaRepository aziendaRepository;
    private final UtenteRepository utenteRepository;
    private final PermessoRepository permessoRepository;

    /**
     * Costruttore del controller della dashboard.
     *
     * @param aziendaRepository Repository per gestire i dati delle aziende.
     * @param utenteRepository  Repository per gestire i dati degli utenti.
     * @param permessoRepository Repository per gestire i permessi.
     */
    public DashboardController(AziendaRepository aziendaRepository,
                               UtenteRepository utenteRepository,
                               PermessoRepository permessoRepository) {
        this.aziendaRepository = aziendaRepository;
        this.utenteRepository = utenteRepository;
        this.permessoRepository = permessoRepository;
    }

    /**
     * Endpoint per ottenere i dati della dashboard.
     * <p>
     * I dati restituiti dipendono dal ruolo dell'utente autenticato:
     * <ul>
     *     <li>`ROLE_MASTER`: Statistiche generali sul sistema.</li>
     *     <li>`ROLE_ADMIN`: Informazioni limitate all'azienda dell'utente.</li>
     * </ul>
     *
     * @param authentication L'oggetto di autenticazione che contiene i dettagli dell'utente.
     * @return Una mappa contenente i dati della dashboard.
     * @throws IllegalStateException Se il principal non è del tipo `CustomUserDetails` per gli utenti `ROLE_ADMIN`.
     */
    @GetMapping
    public Map<String, Object> getDashboardData(Authentication authentication) {

        System.out.println("DashboardController - Metodo chiamato!");

        Map<String, Object> response = new HashMap<>();

        // Log l'autenticazione
        System.out.println("Autenticazione: " + authentication);
        System.out.println("Principal: " + authentication.getPrincipal());

        // Ottiene il ruolo principale dell'utente
        String role = authentication.getAuthorities().iterator().next().getAuthority();
        System.out.println("Ruolo dell'utente: " + role);

        if ("ROLE_MASTER".equals(role)) {
            // Recupera statistiche generali per gli utenti con ruolo MASTER
            long numeroAziende = aziendaRepository.count();
            long numeroUtenti = utenteRepository.count();
            long numeroPermessi = permessoRepository.count();

            System.out.println("Master Dashboard - Aziende: " + numeroAziende + ", Utenti: " + numeroUtenti + ", Permessi: " + numeroPermessi);

            response.put("Totale Aziende", numeroAziende);
            response.put("Totale Utenti", numeroUtenti);
            response.put("Permessi", numeroPermessi);

        } else if ("ROLE_ADMIN".equals(role)) {
            // Recupera dati specifici per gli utenti con ruolo ADMIN
            Object principal = authentication.getPrincipal();
            if (principal instanceof CustomUserDetails) {
                String aziendaId = ((CustomUserDetails) principal).getAziendaId();
                long numeroUtenti = utenteRepository.countByAziendaId(aziendaId);
                response.put("Totale Utenti", numeroUtenti);
            } else {
                // Lancia un'eccezione se il principal non è del tipo previsto
                throw new IllegalStateException("Principal non è del tipo CustomUserDetails: " + principal.getClass().getName());
            }
        }

        return response;
    }
}
