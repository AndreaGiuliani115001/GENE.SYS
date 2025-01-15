package it.P2M.genesys.model;

import org.springframework.data.annotation.Id;
import org.springframework.data.mongodb.core.mapping.Document;

/**
 * Rappresenta una configurazione o un'impostazione salvata nel sistema.
 * <p>
 * Ogni impostazione è composta da una chiave univoca e un valore associato.
 * Questa classe è mappata nella collezione `settings` del database MongoDB.
 */
@Document(collection = "settings")
public class Setting {

    @Id
    private String key; // Chiave univoca per identificare l'impostazione

    private String value; // Valore associato alla chiave

    /**
     * Costruttore predefinito per la classe `Setting`.
     * <p>
     * Necessario per la deserializzazione da parte di MongoDB o framework come Jackson.
     */
    public Setting() {}

    /**
     * Costruttore completo per la classe `Setting`.
     *
     * @param key   La chiave univoca per l'impostazione.
     * @param value Il valore associato alla chiave.
     */
    public Setting(String key, String value) {
        this.key = key;
        this.value = value;
    }

    /**
     * Restituisce la chiave dell'impostazione.
     *
     * @return La chiave dell'impostazione.
     */
    public String getKey() {
        return key;
    }

    /**
     * Imposta la chiave dell'impostazione.
     *
     * @param key La chiave da impostare.
     */
    public void setKey(String key) {
        this.key = key;
    }

    /**
     * Restituisce il valore dell'impostazione.
     *
     * @return Il valore dell'impostazione.
     */
    public String getValue() {
        return value;
    }

    /**
     * Imposta il valore dell'impostazione.
     *
     * @param value Il valore da impostare.
     */
    public void setValue(String value) {
        this.value = value;
    }
}
