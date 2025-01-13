package it.P2M.genesys.repository;

import it.P2M.genesys.model.Utente;
import org.springframework.data.mongodb.repository.MongoRepository;

public interface UtenteRepository extends MongoRepository<Utente, String> {
    Utente findByEmail(String email); // Metodo per cercare un utente tramite email
}
