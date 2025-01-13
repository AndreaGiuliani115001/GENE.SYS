package it.P2M.genesys.model;

import org.springframework.data.annotation.Id;
import org.springframework.data.mongodb.core.mapping.Document;
import org.springframework.data.mongodb.core.mapping.DBRef;

import java.util.List;

@Document(collection = "aziende")
public class Azienda {

    @Id
    private String id;

    private String nome;
    private String indirizzo;
    private String telefono;
    private String email;
    private String pIva;

    @DBRef
    private CampoOperativo campoOperativo;

    // Costruttori, getter e setter

    public Azienda() {
    }

    public Azienda(String nome, String indirizzo, String telefono, String email, String pIva) {
        this.nome = nome;
        this.indirizzo = indirizzo;
        this.telefono = telefono;
        this.email = email;
        this.pIva = pIva;
    }

    public String getId() {
        return id;
    }

    public String getNome() {
        return nome;
    }

    public void setNome(String nome) {
        this.nome = nome;
    }

    public String getpIva() {
        return pIva;
    }

    public void setpIva(String pIva) {
        this.pIva = pIva;
    }

    public String getIndirizzo() {
        return indirizzo;
    }

    public void setIndirizzo(String indirizzo) {
        this.indirizzo = indirizzo;
    }

    public String getTelefono() {
        return telefono;
    }

    public void setTelefono(String telefono) {
        this.telefono = telefono;
    }

    public String getEmail() {
        return email;
    }

    public void setEmail(String email) {
        this.email = email;
    }

    public CampoOperativo getCampoOperativo() {
        return campoOperativo;
    }

    public void setCampoOperativo(CampoOperativo campoOperativo) {
        this.campoOperativo = campoOperativo;
    }

}
