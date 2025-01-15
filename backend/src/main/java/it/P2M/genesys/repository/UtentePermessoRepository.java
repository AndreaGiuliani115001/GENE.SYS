package it.P2M.genesys.repository;

import it.P2M.genesys.model.UtentePermesso;
import org.springframework.data.mongodb.repository.MongoRepository;

import java.util.List;

/**
 * Repository per la gestione delle operazioni sulla collezione `utenti_permessi` in MongoDB.
 * <p>
 * Fornisce metodi predefiniti per le operazioni CRUD e query personalizzate per gestire la relazione tra utenti e permessi.
 */
public interface UtentePermessoRepository extends MongoRepository<UtentePermesso, String> {

    /**
     * Trova tutti i permessi associati a un utente specifico.
     *
     * @param utenteId L'ID dell'utente.
     * @return Una lista di associazioni utente-permesso per l'utente specificato.
     */
    List<UtentePermesso> findByUtenteId(String utenteId);

    /**
     * Trova tutti gli utenti che hanno un permesso specifico.
     *
     * @param permessoId L'ID del permesso.
     * @return Una lista di associazioni utente-permesso per il permesso specificato.
     */
    List<UtentePermesso> findByPermessoId(String permessoId);
}
