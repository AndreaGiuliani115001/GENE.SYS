package it.P2M.genesys.util;

import io.jsonwebtoken.Jwts;
import io.jsonwebtoken.SignatureAlgorithm;
import io.jsonwebtoken.security.Keys;
import org.springframework.stereotype.Component;

import java.security.Key;
import java.util.Date;

@Component
public class JwtTokenUtil {

    private final Key SECRET_KEY = Keys.secretKeyFor(SignatureAlgorithm.HS512); // Genera una chiave sicura

    private final long EXPIRATION_TIME = 86400000; // 24 ore

    public String generateToken(String username) {
        return Jwts.builder()
                .setSubject(username)
                .setIssuedAt(new Date())
                .setExpiration(new Date(System.currentTimeMillis() + EXPIRATION_TIME))
                .signWith(SECRET_KEY) // Usa la chiave sicura
                .compact();
    }
}
