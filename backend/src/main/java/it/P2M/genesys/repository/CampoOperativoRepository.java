package it.P2M.genesys.repository;

import it.P2M.genesys.model.CampoOperativo;
import org.springframework.data.mongodb.repository.MongoRepository;

public interface CampoOperativoRepository extends MongoRepository<CampoOperativo, String> {

}
