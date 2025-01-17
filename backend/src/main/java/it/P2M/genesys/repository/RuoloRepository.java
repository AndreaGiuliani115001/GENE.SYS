package it.P2M.genesys.repository;

import it.P2M.genesys.model.Ruolo;
import org.springframework.data.mongodb.repository.MongoRepository;
import org.springframework.stereotype.Repository;

import java.util.Optional;

@Repository
public interface RuoloRepository extends MongoRepository<Ruolo, String> {

    Optional<Ruolo> findByNome(String nome);
}

