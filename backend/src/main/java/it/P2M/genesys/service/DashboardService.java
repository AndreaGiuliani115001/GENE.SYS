package it.P2M.genesys.service;

import it.P2M.genesys.repository.AziendaRepository;
import it.P2M.genesys.repository.CampoOperativoRepository;
import it.P2M.genesys.repository.PermessoRepository;
import it.P2M.genesys.repository.UtenteRepository;
import org.springframework.stereotype.Service;

import java.util.HashMap;
import java.util.Map;

/**
 * Servizio per fornire i dati della dashboard in base al ruolo dell'utente.
 * <p>
 * Questo servizio interagisce con i repository per ottenere dati statistici
 * e informazioni specifiche per gli utenti master e admin.
 */
@Service
public class DashboardService {

    private final AziendaRepository aziendaRepository;
    private final CampoOperativoRepository campoOperativoRepository;
    private final PermessoRepository permessoRepository;
    private final UtenteRepository utenteRepository;

    /**
     * Costruttore per iniettare i repository richiesti.
     *
     * @param aziendaRepository        Repository per la gestione delle aziende.
     * @param campoOperativoRepository Repository per la gestione dei campi operativi.
     * @param permessoRepository       Repository per la gestione dei permessi.
     * @param utenteRepository         Repository per la gestione degli utenti.
     */
    public DashboardService(AziendaRepository aziendaRepository,
                            CampoOperativoRepository campoOperativoRepository,
                            PermessoRepository permessoRepository,
                            UtenteRepository utenteRepository) {
        this.aziendaRepository = aziendaRepository;
        this.campoOperativoRepository = campoOperativoRepository;
        this.permessoRepository = permessoRepository;
        this.utenteRepository = utenteRepository;
    }

    /**
     * Recupera i dati della dashboard per un utente con ruolo master.
     * <p>
     * Fornisce informazioni generali sulle entità del sistema, come il numero totale
     * di aziende, campi operativi, utenti e permessi.
     *
     * @return Una mappa contenente i dati statistici per la dashboard master.
     */
    public Map<String, Object> getMasterDashboardData() {
        Map<String, Object> data = new HashMap<>();
        data.put("totalCompanies", aziendaRepository.count());
        data.put("totalOperationalFields", campoOperativoRepository.count());
        data.put("totalUsers", utenteRepository.count());
        data.put("totalPermissions", permessoRepository.count());
        return data;
    }

    /**
     * Recupera i dati della dashboard per un utente con ruolo admin.
     * <p>
     * Fornisce informazioni specifiche relative all'azienda dell'utente,
     * come il numero di utenti nell'azienda, il campo operativo e i permessi dell'utente.
     *
     * @param email L'email dell'utente admin.
     * @return Una mappa contenente i dati specifici per la dashboard admin.
     * @throws IllegalStateException Se l'utente o l'azienda non vengono trovati.
     */
    public Map<String, Object> getAdminDashboardData(String email) {
        Map<String, Object> data = new HashMap<>();
        var user = utenteRepository.findByEmail(email);

        if (user != null && user.getAziendaId() != null) {
            data.put("totalUsersInCompany", utenteRepository.countByAziendaId(user.getAziendaId().getId()));
            data.put("operationalField", user.getAziendaId().getCampoOperativo().getNome());
            data.put("userPermissions", permessoRepository.findAllByUserId(user.getId()));
        } else {
            throw new IllegalStateException("Utente o azienda non trovati per l'email: " + email);
        }

        return data;
    }
}
