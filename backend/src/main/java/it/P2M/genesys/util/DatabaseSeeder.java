package it.P2M.genesys.util;

import it.P2M.genesys.model.*;
import it.P2M.genesys.repository.*;
import org.springframework.boot.CommandLineRunner;
import org.springframework.context.annotation.Bean;
import org.springframework.context.annotation.Configuration;
import org.springframework.security.crypto.password.PasswordEncoder;

import java.util.List;

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
     * @param ruoloRepository          Repository per gestire i ruoli.
     * @param utenteRepository         Repository per gestire gli utenti.
     * @param passwordEncoder          Encoder per le password.
     * @return Un `CommandLineRunner` che popola il database.
     */
    @Bean
    CommandLineRunner initDatabase(
            SettingRepository settingRepository,
            AziendaRepository aziendaRepository,
            CampoOperativoRepository campoOperativoRepository,
            PermessoRepository permessoRepository,
            RuoloRepository ruoloRepository,
            UtenteRepository utenteRepository,
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
            Permesso visualizzaAziendaGlobale = permessoRepository.save(new Permesso("visualizza", "azienda"));
            Permesso modificaAziendaGlobale = permessoRepository.save(new Permesso("modifica", "azienda"));
            Permesso eliminaAziendaGlobale = permessoRepository.save(new Permesso("elimina", "azienda"));
            Permesso aggiungiAziendaGlobale = permessoRepository.save(new Permesso("aggiungi", "azienda"));

            Permesso visualizzaAziendaSpecifico = permessoRepository.save(new Permesso("visualizza", "azienda", xyz.getId()));
            Permesso modificaAziendaSpecifico = permessoRepository.save(new Permesso("modifica", "azienda", xyz.getId()));

            // Creazione dei Ruoli
            Ruolo masterRole = new Ruolo(null, "ROLE_MASTER", List.of(
                    visualizzaAziendaGlobale, modificaAziendaGlobale, eliminaAziendaGlobale, aggiungiAziendaGlobale
            ));
            masterRole = ruoloRepository.save(masterRole);

            Ruolo adminRole = new Ruolo(null, "ROLE_ADMIN", List.of(
                    visualizzaAziendaSpecifico, modificaAziendaSpecifico
            ));
            adminRole = ruoloRepository.save(adminRole);

            Ruolo projectManagerRole = new Ruolo(null, "ROLE_PROJECT_MANAGER", List.of(
                    visualizzaAziendaGlobale
            ));
            projectManagerRole = ruoloRepository.save(projectManagerRole);

            Ruolo operatoreRole = new Ruolo(null, "ROLE_OPERATORE", List.of(
                    visualizzaAziendaGlobale
            ));
            operatoreRole = ruoloRepository.save(operatoreRole);

            // Creazione degli Utenti con Ruoli e Permessi Personalizzati
            Utente masterP2M = new Utente("Master", "P2M", "master@p2m.com", masterRole, passwordEncoder.encode("password"));
            masterP2M.setAziendaId(p2m);
            masterP2M.setPermessiAggiuntivi(List.of()); // Nessun permesso extra
            masterP2M.setPermessiLimitati(List.of()); // Nessuna limitazione
            masterP2M = utenteRepository.save(masterP2M);

            Utente adminXYZ = new Utente("Admin", "P2M", "admin@xyz.com", adminRole, passwordEncoder.encode("password"));
            adminXYZ.setAziendaId(xyz);
            adminXYZ.setPermessiAggiuntivi(List.of(eliminaAziendaGlobale)); // Aggiunge permesso di eliminazione globale
            adminXYZ.setPermessiLimitati(List.of(modificaAziendaSpecifico)); // Rimuove permesso di modifica specifico
            adminXYZ = utenteRepository.save(adminXYZ);

            Utente projectManagerXYZ = new Utente("ProjectManager", "XYZ", "projectmanager@xyz.com", projectManagerRole, passwordEncoder.encode("password"));
            projectManagerXYZ.setAziendaId(xyz);
            projectManagerXYZ.setPermessiAggiuntivi(List.of()); // Nessun permesso extra
            projectManagerXYZ.setPermessiLimitati(List.of()); // Nessuna limitazione
            projectManagerXYZ = utenteRepository.save(projectManagerXYZ);

            Utente operatoreXYZ = new Utente("Operatore", "XYZ", "operatore@xyz.com", operatoreRole, passwordEncoder.encode("password"));
            operatoreXYZ.setAziendaId(xyz);
            operatoreXYZ.setPermessiAggiuntivi(List.of()); // Nessun permesso extra
            operatoreXYZ.setPermessiLimitati(List.of()); // Nessuna limitazione
            operatoreXYZ = utenteRepository.save(operatoreXYZ);

            System.out.println("Database inizializzato con successo!");

            // Imposta il flag di inizializzazione nel database
            settingRepository.save(new Setting("database_initialized", "true"));
        };
    }
}
