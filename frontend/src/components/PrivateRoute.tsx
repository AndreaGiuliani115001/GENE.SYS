import React, { useContext } from 'react';
import { Navigate } from 'react-router-dom';
import { AuthContext } from '../context/AuthContext';

interface PrivateRouteProps {
    children: React.ReactNode;
    allowedRoles?: string[]; // Lista di ruoli autorizzati per accedere alla rotta
}

const PrivateRoute: React.FC<PrivateRouteProps> = ({ children, allowedRoles }) => {
    const { user, isAuthenticated, loading } = useContext(AuthContext);

    console.log("PrivateRoute - Loading:", loading);
    console.log("PrivateRoute - User:", user);
    console.log("PrivateRoute - Allowed Roles:", allowedRoles);

    // Mostra una schermata di caricamento mentre AuthContext è in fase di inizializzazione
    if (loading) {
        return <div>Caricamento...</div>; // Spinner o messaggio personalizzato
    }

    // Controllo di autenticazione
    if (!isAuthenticated) {
        console.log("Accesso negato - Utente non autenticato");
        return <Navigate to="/" replace />;
    }

    // Controllo dei ruoli
    if (allowedRoles && (!user || !allowedRoles.includes(user.role))) {
        console.log("Accesso negato - Ruolo non autorizzato");
        return <Navigate to="/not-authorized" replace />; // Reindirizzamento a NotAuthorized
    }

    // Accesso consentito
    return <>{children}</>;
};

export default PrivateRoute;
