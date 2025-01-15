package it.P2M.genesys.repository;

import it.P2M.genesys.model.Utente;
import org.springframework.data.mongodb.repository.MongoRepository;

/**
 * Repository per la gestione delle operazioni sulla collezione `utenti` in MongoDB.
 * <p>
 * Fornisce metodi predefiniti per le operazioni CRUD e query personalizzate sugli utenti.
 */
public interface UtenteRepository extends MongoRepository<Utente, String> {

    /**
     * Conta il numero di utenti associati a una determinata azienda.
     *
     * @param aziendaId L'ID dell'azienda.
     * @return Il numero di utenti associati all'azienda specificata.
     */
    long countByAziendaId(String aziendaId);

    /**
     * Trova un utente in base alla sua email.
     *
     * @param email L'email dell'utente.
     * @return L'utente trovato o {@code null} se nessuna corrispondenza è stata trovata.
     */
    Utente findByEmail(String email);
}
