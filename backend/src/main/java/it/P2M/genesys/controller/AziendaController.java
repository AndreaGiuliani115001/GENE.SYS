package it.P2M.genesys.controller;

import it.P2M.genesys.model.Azienda;
import it.P2M.genesys.repository.AziendaRepository;
import org.springframework.web.bind.annotation.GetMapping;
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

    @GetMapping
    public List<Azienda> getAziende() {
        return aziendaRepository.findAll();
    }
}
