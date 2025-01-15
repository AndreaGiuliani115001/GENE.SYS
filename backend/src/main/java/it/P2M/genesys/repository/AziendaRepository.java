package it.P2M.genesys.repository;

import it.P2M.genesys.model.Azienda;
import org.springframework.data.mongodb.repository.MongoRepository;

/**
 * Repository per la gestione delle operazioni sulla collezione `aziende` in MongoDB.
 * <p>
 * Fornisce metodi per effettuare operazioni CRUD e query personalizzate sulle aziende.
 */
public interface AziendaRepository extends MongoRepository<Azienda, String> {

    /**
     * Conta il numero totale di documenti nella collezione `aziende`.
     *
     * @return Il numero totale di aziende.
     */
    long count();

    /**
     * Cerca un'azienda nella collezione in base al nome.
     *
     * @param nome Il nome dell'azienda da cercare.
     * @return L'azienda trovata o {@code null} se nessuna corrispondenza è stata trovata.
     */
    Azienda findByNome(String nome);
}
