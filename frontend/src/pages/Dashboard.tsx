import React, { useEffect, useState } from 'react';
import {Routes, Route, Navigate} from 'react-router-dom';
import { fetchDashboardData } from '../services/dashboardService';
import { CircularProgress, Typography, Box, Grid, Card, CardContent, CssBaseline } from '@mui/material';
import Navbar from '../components/Navbar';
import Sidebar from '../components/Sidebar';
import Utenti from './Utenti';
import Aziende from './Aziende';
import Permessi from './Permessi';
import PrivateRoute from "../components/PrivateRoute.tsx";

interface DashboardData {
    numeroAziende: number;
    numeroUtenti: number;
}

const Dashboard: React.FC = () => {
    const [isSidebarOpen, setIsSidebarOpen] = useState(false);
    const [data, setData] = useState<DashboardData | null>(null);
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState<string | null>(null);

    useEffect(() => {
        const fetchData = async () => {
            try {
                const response = await fetchDashboardData();
                console.log("Dati ricevuti dalla dashboard:", response); // Verifica dei dati
                setData(response);
            } catch (err: any) {
                const message = err.response?.data?.message || err.message || 'Errore nel caricamento dei dati';
                setError(message);
            } finally {
                setLoading(false);
            }
        };

        fetchData();
    }, []);

    const toggleSidebar = () => {
        setIsSidebarOpen(!isSidebarOpen);
    };

    return (
        <Box sx={{ display: 'flex', minHeight: '100vh' }}>
            <CssBaseline />
            <Navbar onMenuClick={toggleSidebar} />
            <Sidebar isOpen={isSidebarOpen} onClose={toggleSidebar} />

            <Box sx={{ marginTop: '64px', flexGrow: 1, p: 3, marginLeft: isSidebarOpen ? '240px' : '0', transition: 'margin 0.3s' }}>
                <Routes>
                    <Route
                        path="/"
                        element={
                            loading ? (
                                <Box sx={{ display: 'flex', justifyContent: 'center', alignItems: 'center', minHeight: '100vh' }}>
                                    <CircularProgress />
                                </Box>
                            ) : error ? (
                                <Typography color="error">{error}</Typography>
                            ) : (
                                <>
                                    <Grid container spacing={3}>
                                        {data &&
                                            Object.entries(data).map(([key, value]) => (
                                                <Grid item xs={12} sm={6} md={4} key={key}>
                                                    <Card>
                                                        <CardContent>
                                                            <Typography variant="h6">{key}</Typography>
                                                            <Typography variant="h4">{value}</Typography>
                                                        </CardContent>
                                                    </Card>
                                                </Grid>
                                            ))}
                                    </Grid>
                                </>
                            )
                        }
                    />

                    <Route
                        path="utenti"
                        element={
                            <PrivateRoute allowedRoles={['ROLE_MASTER', 'ROLE_ADMIN']}>
                                <Utenti />
                            </PrivateRoute>
                        }
                    />
                    <Route
                        path="aziende"
                        element={
                            <PrivateRoute allowedRoles={['ROLE_MASTER']}>
                                <Aziende />
                            </PrivateRoute>
                        }
                    />
                    <Route
                        path="permessi"
                        element={
                            <PrivateRoute allowedRoles={['ROLE_MASTER', 'ROLE_ADMIN']}>
                                <Permessi />
                            </PrivateRoute>
                        }
                    />

                    {/* Fallback per sottorotte non valide */}
                    <Route path="*" element={<Navigate to="/not-found" replace />} />
                </Routes>
            </Box>
        </Box>
    );
};

export default Dashboard;
