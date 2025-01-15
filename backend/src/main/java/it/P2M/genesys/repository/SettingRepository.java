package it.P2M.genesys.repository;

import it.P2M.genesys.model.Setting;
import org.springframework.data.mongodb.repository.MongoRepository;

/**
 * Repository per la gestione delle operazioni sulla collezione `settings` in MongoDB.
 * <p>
 * Fornisce metodi predefiniti per le operazioni CRUD e query personalizzate sulla collezione `settings`.
 */
public interface SettingRepository extends MongoRepository<Setting, String> {

    /**
     * Trova un'impostazione in base alla chiave.
     *
     * @param key La chiave dell'impostazione da cercare.
     * @return L'impostazione trovata o {@code null} se nessuna corrispondenza è stata trovata.
     */
    Setting findByKey(String key);
}
