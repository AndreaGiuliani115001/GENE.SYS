import React from 'react';
import {BrowserRouter as Router, Routes, Route} from 'react-router-dom';
import PrivateRoute from './components/PrivateRoute';
import Dashboard from './pages/Dashboard';
import Login from "./pages/Login.tsx";
import NotAuthorized from "./pages/NotAuthorized.tsx";
import NotFound from "./pages/NotFound.tsx";
import Welcome from "./pages/Welcome.tsx";

const App: React.FC = () => {
    return (
        <Router>
            <Routes>
                {/* Rotta pubblica */}
                <Route path="/" element={<Welcome/>}/>
                <Route path="/login" element={<Login/>}/>

                {/* Rotte protette */}
                <Route
                    path="/dashboard/*"
                    element={
                        <PrivateRoute allowedRoles={['ROLE_MASTER', 'ROLE_ADMIN', 'ROLE_OPERATORE' , 'ROLE_PROJECT_MANAGER']}>
                            <Dashboard/>
                        </PrivateRoute>
                    }
                />

                <Route path="/not-authorized" element={<NotAuthorized />} />
                <Route path="*" element={<NotFound />} />
            </Routes>
        </Router>
    );
};

export default App;
