package it.P2M.genesys.util;

import it.P2M.genesys.model.*;
import it.P2M.genesys.repository.*;
import org.springframework.boot.CommandLineRunner;
import org.springframework.context.annotation.Bean;
import org.springframework.context.annotation.Configuration;
import org.springframework.security.crypto.password.PasswordEncoder;

/**
 * Classe per l'inizializzazione del database con dati di base.
 * <p>
 * Questa configurazione viene eseguita una sola volta all'avvio
 * dell'applicazione se il database non è già stato inizializzato.
 */
@Configuration
public class DatabaseSeeder {

    /**
     * Bean che si occupa di popolare il database con dati iniziali.
     *
     * @param settingRepository        Repository per gestire le impostazioni.
     * @param aziendaRepository        Repository per gestire le aziende.
     * @param campoOperativoRepository Repository per gestire i campi operativi.
     * @param permessoRepository       Repository per gestire i permessi.
     * @param utenteRepository         Repository per gestire gli utenti.
     * @param utentePermessoRepository Repository per gestire le relazioni utente-permesso.
     * @param passwordEncoder          Encoder per le password.
     * @return Un `CommandLineRunner` che popola il database.
     */
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

            // Controlla se il database è già stato inizializzato
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
            xyz = aziendaRepository.save(xyz);

            // Creazione dei Permessi
            Permesso modifica = permessoRepository.save(new Permesso("Modifica", "Azienda"));
            Permesso eliminazione = permessoRepository.save(new Permesso("Eliminazione", "Azienda"));
            Permesso visualizzazione = permessoRepository.save(new Permesso("Visualizzazione", "Azienda"));

            // Creazione degli Utenti
            Utente masterP2M = new Utente("Master", "P2M", "master@p2m.com", "master", passwordEncoder.encode("password"));
            masterP2M.setAziendaId(p2m);
            masterP2M = utenteRepository.save(masterP2M);

            Utente adminXYZ = new Utente("Admin", "XYZ", "admin@xyz.com", "admin", passwordEncoder.encode("password"));
            adminXYZ.setAziendaId(xyz);
            adminXYZ = utenteRepository.save(adminXYZ);

            // Creazione delle relazioni Utente-Permesso per master
            utentePermessoRepository.save(new UtentePermesso(masterP2M.getId(), modifica.getId()));
            utentePermessoRepository.save(new UtentePermesso(masterP2M.getId(), eliminazione.getId()));
            utentePermessoRepository.save(new UtentePermesso(masterP2M.getId(), visualizzazione.getId()));

            // Creazione delle relazioni Utente-Permesso per admin
            utentePermessoRepository.save(new UtentePermesso(adminXYZ.getId(), modifica.getId()));
            utentePermessoRepository.save(new UtentePermesso(adminXYZ.getId(), eliminazione.getId()));
            utentePermessoRepository.save(new UtentePermesso(adminXYZ.getId(), visualizzazione.getId()));

            System.out.println("Database inizializzato con successo!");

            // Imposta il flag di inizializzazione nel database
            settingRepository.save(new Setting("database_initialized", "true"));
        };
    }
}
