package it.P2M.genesys.repository;

import it.P2M.genesys.model.Azienda;
import org.springframework.data.mongodb.repository.MongoRepository;

public interface AziendaRepository extends MongoRepository<Azienda, String> {

    Azienda findByNome(String nome); // Cerca un'azienda per nome
}
