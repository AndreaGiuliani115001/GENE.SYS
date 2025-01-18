import React, { createContext, useState, useEffect, ReactNode } from 'react';

// Aggiungi un'interfaccia per i dettagli dell'utente
interface User {
    email: string; // Email dell'utente
    role: string;
    aziendaId: string;
    permissions: string[];
}

interface AuthContextProps {
    token: string | null;
    setToken: (token: string | null) => void;
    isAuthenticated: boolean;
    user: User | null; // Dettagli dell'utente
    loading: boolean; // Stato di caricamento
    logout: () => void;
}

// Contesto con il nuovo stato user
export const AuthContext = createContext<AuthContextProps>({
    token: null,
    setToken: () => {},
    isAuthenticated: false,
    user: null, // Default user null
    loading: true, // Default loading true
    logout: () => {},
});

export const AuthProvider: React.FC<{ children: ReactNode }> = ({ children }) => {
    const [token, setToken] = useState<string | null>(localStorage.getItem('token'));
    const [user, setUser] = useState<User | null>(null); // Stato per l'utente
    const [loading, setLoading] = useState(true); // Stato di caricamento

    // Funzione per decodificare il token
    const decodeToken = (token: string): User | null => {
        try {
            const payload = JSON.parse(atob(token.split('.')[1])); // Decodifica il payload del token
            return { email: payload.sub, role: payload.role , aziendaId: payload.aziendaId, permissions: payload.permissions}; // Mappa i dettagli dell'utente
        } catch (e) {
            console.error("Errore nella decodifica del token:", e);
            return null;
        }
    };

    useEffect(() => {
        const initializeAuth = async () => {
            if (token) {
                try {
                    localStorage.setItem('token', token);
                    const decodedUser = decodeToken(token);
                    setUser(decodedUser);
                    console.log("Utente decodificato:", decodedUser);
                } catch (error) {
                    console.error("Errore durante l'inizializzazione:", error);
                    setUser(null);
                }
            } else {
                localStorage.removeItem('token');
                setUser(null);
            }
            setLoading(false); // Termina il caricamento
        };

        initializeAuth();
    }, [token]);

    const logout = () => {
        setToken(null); // Rimuove il token dallo stato
        localStorage.removeItem('token'); // Rimuove il token dal localStorage
        setUser(null); // Rimuove i dettagli dell'utente
    };

    const isAuthenticated = !!token;

    return (
        <AuthContext.Provider value={{ token, setToken, isAuthenticated, user, loading, logout }}>
            {children}
        </AuthContext.Provider>
    );
};
