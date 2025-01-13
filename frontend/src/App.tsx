import React from 'react';
import { BrowserRouter as Router, Routes, Route } from 'react-router-dom';
import Login from './components/Login.tsx';
import Dashboard from './components/Dashboard.tsx';

const App: React.FC = () => {
    return (
        <Router>
            <Routes>
                {/* Rotta per il Login */}
                <Route path="/" element={<Login />} />

                {/* Rotta per la Dashboard (protetta in futuro) */}
                <Route path="/dashboard" element={<Dashboard />} />
            </Routes>
        </Router>
    );
};

export default App;
