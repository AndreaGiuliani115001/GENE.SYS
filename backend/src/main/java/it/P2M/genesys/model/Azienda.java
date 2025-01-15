package it.P2M.genesys.model;

import org.springframework.data.annotation.Id;
import org.springframework.data.mongodb.core.mapping.Document;
import org.springframework.data.mongodb.core.mapping.DBRef;

/**
 * Rappresenta un'azienda registrata nel sistema.
 * <p>
 * Questa classe è mappata nella collezione `aziende` del database MongoDB.
 */
@Document(collection = "aziende")
public class Azienda {

    @Id
    private String id; // Identificativo univoco dell'azienda

    private String nome; // Nome dell'azienda
    private String indirizzo; // Indirizzo dell'azienda
    private String telefono; // Numero di telefono dell'azienda
    private String email; // Email dell'azienda
    private String pIva; // Partita IVA dell'azienda

    @DBRef
    private CampoOperativo campoOperativo; // Riferimento al campo operativo associato

    /**
     * Costruttore predefinito per la classe `Azienda`.
     */
    public Azienda() {
    }

    /**
     * Costruttore completo per la classe `Azienda`.
     *
     * @param nome      Nome dell'azienda.
     * @param indirizzo Indirizzo dell'azienda.
     * @param telefono  Numero di telefono dell'azienda.
     * @param email     Email dell'azienda.
     * @param pIva      Partita IVA dell'azienda.
     */
    public Azienda(String nome, String indirizzo, String telefono, String email, String pIva) {
        this.nome = nome;
        this.indirizzo = indirizzo;
        this.telefono = telefono;
        this.email = email;
        this.pIva = pIva;
    }

    /**
     * Restituisce l'identificativo univoco dell'azienda.
     *
     * @return L'ID dell'azienda.
     */
    public String getId() {
        return id;
    }

    /**
     * Restituisce il nome dell'azienda.
     *
     * @return Il nome dell'azienda.
     */
    public String getNome() {
        return nome;
    }

    /**
     * Imposta il nome dell'azienda.
     *
     * @param nome Il nome dell'azienda.
     */
    public void setNome(String nome) {
        this.nome = nome;
    }

    /**
     * Restituisce la partita IVA dell'azienda.
     *
     * @return La partita IVA dell'azienda.
     */
    public String getpIva() {
        return pIva;
    }

    /**
     * Imposta la partita IVA dell'azienda.
     *
     * @param pIva La partita IVA dell'azienda.
     */
    public void setpIva(String pIva) {
        this.pIva = pIva;
    }

    /**
     * Restituisce l'indirizzo dell'azienda.
     *
     * @return L'indirizzo dell'azienda.
     */
    public String getIndirizzo() {
        return indirizzo;
    }

    /**
     * Imposta l'indirizzo dell'azienda.
     *
     * @param indirizzo L'indirizzo dell'azienda.
     */
    public void setIndirizzo(String indirizzo) {
        this.indirizzo = indirizzo;
    }

    /**
     * Restituisce il numero di telefono dell'azienda.
     *
     * @return Il numero di telefono dell'azienda.
     */
    public String getTelefono() {
        return telefono;
    }

    /**
     * Imposta il numero di telefono dell'azienda.
     *
     * @param telefono Il numero di telefono dell'azienda.
     */
    public void setTelefono(String telefono) {
        this.telefono = telefono;
    }

    /**
     * Restituisce l'email dell'azienda.
     *
     * @return L'email dell'azienda.
     */
    public String getEmail() {
        return email;
    }

    /**
     * Imposta l'email dell'azienda.
     *
     * @param email L'email dell'azienda.
     */
    public void setEmail(String email) {
        this.email = email;
    }

    /**
     * Restituisce il campo operativo associato all'azienda.
     *
     * @return Il campo operativo dell'azienda.
     */
    public CampoOperativo getCampoOperativo() {
        return campoOperativo;
    }

    /**
     * Imposta il campo operativo associato all'azienda.
     *
     * @param campoOperativo Il campo operativo dell'azienda.
     */
    public void setCampoOperativo(CampoOperativo campoOperativo) {
        this.campoOperativo = campoOperativo;
    }
}
