package it.P2M.genesys;

import org.springframework.boot.SpringApplication;
import org.springframework.boot.autoconfigure.SpringBootApplication;

/**
 * Classe principale dell'applicazione GENE.SYS.
 * <p>
 * Questa classe avvia l'applicazione Spring Boot.
 */
@SpringBootApplication
public class GenesysApplication {

	/**
	 * Metodo `main` che funge da punto di ingresso dell'applicazione.
	 * <p>
	 * Utilizza il metodo `run` della classe {@link SpringApplication}
	 * per avviare il contesto di Spring e configurare automaticamente l'applicazione.
	 *
	 * @param args Argomenti della riga di comando (opzionali).
	 */
	public static void main(String[] args) {
		SpringApplication.run(GenesysApplication.class, args);
	}
}
