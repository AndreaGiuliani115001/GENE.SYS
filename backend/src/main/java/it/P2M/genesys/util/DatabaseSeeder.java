

package it.P2M.genesys.util;

import it.P2M.genesys.model.*;
import it.P2M.genesys.repository.*;
import org.springframework.boot.CommandLineRunner;
import org.springframework.context.annotation.Bean;
import org.springframework.context.annotation.Configuration;
import org.springframework.security.crypto.password.PasswordEncoder;

@Configuration
public class DatabaseSeeder {

    @Bean
    CommandLineRunner initDatabase(
            SettingRepository settingRepository,
            AziendaRepository aziendaRepository,
            CampoOperativoRepository campoOperativoRepository,
            PermessoRepository permessoRepository,
            UtenteRepository utenteRepository,
            UtentePermessoRepository utentePermessoRepository,
            PasswordEncoder passwordEncoder) {
        return args -> {

            // Controlla se il database è già inizializzato
            Setting databaseInitialized = settingRepository.findByKey("database_initialized");
            if (databaseInitialized != null && "true".equals(databaseInitialized.getValue())) {
                System.out.println("Database già inizializzato. Salto il seeder.");
                return;
            }

            // Creazione del Campo Operativo
            CampoOperativo campoOperativo = new CampoOperativo("Navale");
            campoOperativo = campoOperativoRepository.save(campoOperativo);

            // Creazione delle Aziende
            Azienda p2m = new Azienda("P2M", "Via Roma 1, Milano", "+39 0123 456789", "info@p2m.com", "12345678901");
            p2m.setCampoOperativo(campoOperativo);
            p2m = aziendaRepository.save(p2m);

            Azienda xyz = new Azienda("XYZ", "Via Torino 5, Roma", "+39 0987 654321", "info@xyz.com", "09876543210");
            xyz.setCampoOperativo(campoOperativo);
            aziendaRepository.save(xyz);

            // Creazione dei Permessi
            Permesso modifica = permessoRepository.save(new Permesso("Modifica", "Azienda"));
            Permesso eliminazione = permessoRepository.save(new Permesso("Eliminazione", "Azienda"));
            Permesso visualizzazione = permessoRepository.save(new Permesso("Visualizzazione", "Azienda"));

            // Creazione degli Utenti
            Utente masterP2M = new Utente("Master", "P2M", "master@p2m.com", "admin", passwordEncoder.encode("password"));
            masterP2M.setAzienda(p2m);
            masterP2M = utenteRepository.save(masterP2M);

            Utente operatoreP2M = new Utente("Operatore", "P2M", "operatore@p2m.com", "operatore", passwordEncoder.encode("password"));
            operatoreP2M.setAzienda(p2m);
            operatoreP2M = utenteRepository.save(operatoreP2M);

            // Creazione delle relazioni Utente-Permesso
            utentePermessoRepository.save(new UtentePermesso(masterP2M.getId(), modifica.getId()));
            utentePermessoRepository.save(new UtentePermesso(masterP2M.getId(), eliminazione.getId()));
            utentePermessoRepository.save(new UtentePermesso(masterP2M.getId(), visualizzazione.getId()));

            utentePermessoRepository.save(new UtentePermesso(operatoreP2M.getId(), visualizzazione.getId()));

            System.out.println("Database inizializzato con successo!");

            // Imposta il flag di inizializzazione nel database
            settingRepository.save(new Setting("database_initialized", "true"));
        };
    }
}
