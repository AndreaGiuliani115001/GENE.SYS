package it.P2M.genesys.repository;

import it.P2M.genesys.model.Permesso;
import org.springframework.data.mongodb.repository.MongoRepository;

public interface PermessoRepository extends MongoRepository<Permesso, String> {

}
