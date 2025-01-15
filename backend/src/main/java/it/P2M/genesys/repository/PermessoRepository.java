package it.P2M.genesys.repository;

import it.P2M.genesys.model.Permesso;
import org.springframework.data.mongodb.repository.MongoRepository;
import org.springframework.data.mongodb.repository.Query;

import java.util.List;

/**
 * Repository per la gestione delle operazioni sulla collezione `permessi` in MongoDB.
 * <p>
 * Fornisce metodi predefiniti per le operazioni CRUD e query personalizzate sulla collezione `permessi`.
 */
public interface PermessoRepository extends MongoRepository<Permesso, String> {

    /**
     * Trova tutti i permessi associati a un utente specifico.
     *
     * @param userId L'ID dell'utente.
     * @return Una lista di permessi associati all'utente specificato.
     */
    @Query("{ 'userId': ?0 }")
    List<Permesso> findAllByUserId(String userId);
}
