package it.P2M.genesys.model;

import org.springframework.data.annotation.Id;
import org.springframework.data.mongodb.core.mapping.DBRef;
import org.springframework.data.mongodb.core.mapping.Document;

import java.util.List;

@Document(collection = "campi_operativi")
public class CampoOperativo {

    @Id
    private String id;

    private String nome; // Nome del campo operativo

    // Costruttori, getter e setter

    public CampoOperativo() {
    }

    public CampoOperativo(String nome) {
        this.nome = nome;
    }

    // Getter e setter
    public String getId() {
        return id;
    }

    public String getNome() {
        return nome;
    }

    public void setNome(String nome) {
        this.nome = nome;
    }
}
