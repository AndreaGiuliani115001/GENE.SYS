package it.P2M.genesys.model;

import org.springframework.data.annotation.Id;
import org.springframework.data.mongodb.core.mapping.DBRef;
import org.springframework.data.mongodb.core.mapping.Document;

import java.util.List;

@Document(collection = "ruoli")
public class Ruolo {

    @Id
    private String id;

    private String nome; // Es. "ROLE_MASTER", "ROLE_ADMIN", "ROLE_PROJECT_MANAGER", "ROLE_OPERATORE"

    @DBRef
    private List<Permesso> permessiPredefiniti; // Lista dei permessi associati al ruolo

    // Costruttore vuoto
    public Ruolo() {
    }

    // Costruttore con parametri
    public Ruolo(String id, String nome, List<Permesso> permessiPredefiniti) {
        this.id = id;
        this.nome = nome;
        this.permessiPredefiniti = permessiPredefiniti;
    }

    // Getter e Setter
    public String getId() {
        return id;
    }

    public void setId(String id) {
        this.id = id;
    }

    public String getNome() {
        return nome;
    }

    public void setNome(String nome) {
        this.nome = nome;
    }

    public List<Permesso> getPermessiPredefiniti() {
        return permessiPredefiniti;
    }

    public void setPermessiPredefiniti(List<Permesso> permessiPredefiniti) {
        this.permessiPredefiniti = permessiPredefiniti;
    }

    @Override
    public String toString() {
        return "Ruolo{" +
                "id='" + id + '\'' +
                ", nome='" + nome + '\'' +
                ", permessiPredefiniti=" + permessiPredefiniti +
                '}';
    }
}


