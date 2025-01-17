package it.P2M.genesys.service;

import it.P2M.genesys.model.Permesso;
import it.P2M.genesys.model.Ruolo;
import it.P2M.genesys.repository.RuoloRepository;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.stereotype.Service;

import java.util.List;

@Service
public class RoleService {

    @Autowired
    private RuoloRepository ruoloRepository;

    public List<Permesso> getPermissionsByRole(String ruoloNome) {
        Ruolo ruolo = ruoloRepository.findByNome(ruoloNome)
                .orElseThrow(() -> new RuntimeException("Ruolo non trovato"));
        return ruolo.getPermessiPredefiniti();
    }
}

