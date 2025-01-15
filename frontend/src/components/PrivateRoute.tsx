import React, { useContext } from 'react';
import { Navigate } from 'react-router-dom';
import { AuthContext } from '../context/AuthContext';

interface PrivateRouteProps {
    children: React.ReactNode;
}

const PrivateRoute: React.FC<PrivateRouteProps> = ({ children }) => {
    const { isAuthenticated } = useContext(AuthContext);

    return isAuthenticated ? (
        <>{children}</>
    ) : (
        <Navigate to="/" replace />
    );
};

export default PrivateRoute;
