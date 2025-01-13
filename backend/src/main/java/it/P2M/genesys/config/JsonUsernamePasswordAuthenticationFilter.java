package it.P2M.genesys.config;

import com.fasterxml.jackson.databind.ObjectMapper;
import org.springframework.security.authentication.UsernamePasswordAuthenticationToken;
import org.springframework.security.core.Authentication;
import org.springframework.security.core.AuthenticationException;
import org.springframework.security.web.authentication.UsernamePasswordAuthenticationFilter;

import jakarta.servlet.http.HttpServletRequest;
import jakarta.servlet.http.HttpServletResponse;
import java.io.IOException;
import java.util.Map;

public class JsonUsernamePasswordAuthenticationFilter extends UsernamePasswordAuthenticationFilter {

    private final ObjectMapper objectMapper = new ObjectMapper();

    @Override
    public Authentication attemptAuthentication(HttpServletRequest request, HttpServletResponse response) throws AuthenticationException {
        if ("application/json".equals(request.getContentType())) {
            try {
                // Legge il body della richiesta JSON
                Map<String, String> jsonRequest = objectMapper.readValue(request.getInputStream(), Map.class);

                String email = jsonRequest.get("email");
                String password = jsonRequest.get("password");

                UsernamePasswordAuthenticationToken authRequest = new UsernamePasswordAuthenticationToken(email, password);

                // Passa l'oggetto Authentication a Spring Security
                return this.getAuthenticationManager().authenticate(authRequest);
            } catch (IOException e) {
                throw new RuntimeException(e);
            }
        }

        // Usa il comportamento di default per altre richieste
        return super.attemptAuthentication(request, response);
    }
}
