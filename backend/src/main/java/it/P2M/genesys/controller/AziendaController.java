package it.P2M.genesys.controller;

import it.P2M.genesys.model.Azienda;
import it.P2M.genesys.model.CustomUserDetails;
import it.P2M.genesys.repository.AziendaRepository;
import it.P2M.genesys.service.AuthorizationService;
import org.springframework.http.HttpStatus;
import org.springframework.http.ResponseEntity;
import org.springframework.security.access.prepost.PreAuthorize;
import org.springframework.security.core.annotation.AuthenticationPrincipal;
import org.springframework.web.bind.annotation.GetMapping;
import org.springframework.web.bind.annotation.PathVariable;
import org.springframework.web.bind.annotation.RequestMapping;
import org.springframework.web.bind.annotation.RestController;

import java.util.List;

@RestController
@RequestMapping("/aziende")
public class AziendaController {

    private final AziendaRepository aziendaRepository;

    public AziendaController(AziendaRepository aziendaRepository) {
        this.aziendaRepository = aziendaRepository;
    }

    @PreAuthorize("hasAuthority('visualizza_azienda')")
    @GetMapping
    public List<Azienda> getAziende() {
        return aziendaRepository.findAll();
    }

    @PreAuthorize("hasAuthority('visualizza_azienda_' + #id)")
    @GetMapping("/{id}")
    public ResponseEntity<Azienda> getAziendaById(@PathVariable String id) {
        return aziendaRepository.findById(id)
                .map(ResponseEntity::ok)
                .orElse(ResponseEntity.status(HttpStatus.NOT_FOUND).build());
    }
}


