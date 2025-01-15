package it.P2M.genesys.model;

import org.springframework.data.annotation.Id;
import org.springframework.data.mongodb.core.mapping.Document;

/**
 * Rappresenta un campo operativo nel sistema.
 * <p>
 * Questa classe è mappata nella collezione `campi_operativi` del database MongoDB.
 * Ogni oggetto rappresenta un settore o ambito operativo associato a una o più aziende.
 */
@Document(collection = "campi_operativi")
public class CampoOperativo {

    @Id
    private String id; // Identificativo univoco del campo operativo

    private String nome; // Nome del campo operativo

    /**
     * Costruttore predefinito per la classe `CampoOperativo`.
     * <p>
     * Necessario per il framework, ad esempio durante la deserializzazione.
     */
    public CampoOperativo() {
    }

    /**
     * Costruttore completo per la classe `CampoOperativo`.
     *
     * @param nome Il nome del campo operativo.
     */
    public CampoOperativo(String nome) {
        this.nome = nome;
    }

    /**
     * Restituisce l'identificativo univoco del campo operativo.
     *
     * @return L'ID del campo operativo.
     */
    public String getId() {
        return id;
    }

    /**
     * Restituisce il nome del campo operativo.
     *
     * @return Il nome del campo operativo.
     */
    public String getNome() {
        return nome;
    }

    /**
     * Imposta il nome del campo operativo.
     *
     * @param nome Il nome del campo operativo.
     */
    public void setNome(String nome) {
        this.nome = nome;
    }
}
