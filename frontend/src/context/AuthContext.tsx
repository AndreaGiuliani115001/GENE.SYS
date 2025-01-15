import React, { createContext, useState, useEffect, ReactNode } from 'react';

interface AuthContextProps {
    token: string | null;
    setToken: (token: string | null) => void;
    isAuthenticated: boolean;
    logout: () => void;
}

export const AuthContext = createContext<AuthContextProps>({
    token: null,
    setToken: () => {},
    isAuthenticated: false,
    logout: () => {},
});

export const AuthProvider: React.FC<{ children: ReactNode }> = ({ children }) => {
    const [token, setToken] = useState<string | null>(localStorage.getItem('token'));

    useEffect(() => {
        if (token) {
            localStorage.setItem('token', token);
        } else {
            localStorage.removeItem('token');
        }
    }, [token]);

    const logout = () => {
        setToken(null); // Rimuove il token dallo stato
        localStorage.removeItem('token'); // Rimuove il token dal localStorage
    };

    const isAuthenticated = !!token;

    return (
        <AuthContext.Provider value={{ token, setToken, isAuthenticated, logout }}>
            {children}
        </AuthContext.Provider>
    );
};
