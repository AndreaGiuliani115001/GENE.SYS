package it.P2M.genesys.repository;

import it.P2M.genesys.model.Setting;
import org.springframework.data.mongodb.repository.MongoRepository;

public interface SettingRepository extends MongoRepository<Setting, String> {
    Setting findByKey(String key);
}
