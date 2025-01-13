package it.P2M.genesys.repository;

import it.P2M.genesys.model.UtentePermesso;
import org.springframework.data.mongodb.repository.MongoRepository;

import java.util.List;

public interface UtentePermessoRepository extends MongoRepository<UtentePermesso, String> {
    List<UtentePermesso> findByUtenteId(String utenteId); // Trova permessi di un utente
    List<UtentePermesso> findByPermessoId(String permessoId); // Trova utenti con un permesso
}
