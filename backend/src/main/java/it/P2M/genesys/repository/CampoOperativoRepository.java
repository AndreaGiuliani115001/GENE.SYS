package it.P2M.genesys.repository;

import it.P2M.genesys.model.CampoOperativo;
import org.springframework.data.mongodb.repository.MongoRepository;

/**
 * Repository per la gestione delle operazioni sulla collezione `campi_operativi` in MongoDB.
 * <p>
 * Fornisce metodi predefiniti per le operazioni CRUD sulla collezione `campi_operativi`.
 */
public interface CampoOperativoRepository extends MongoRepository<CampoOperativo, String> {

    // Puoi aggiungere qui metodi personalizzati per query specifiche se necessario
}
