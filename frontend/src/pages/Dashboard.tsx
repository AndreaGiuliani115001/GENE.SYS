import React, { useEffect, useState } from 'react';
import { fetchDashboardData } from '../services/dashboardService';
import { CircularProgress, Typography, Box, Grid, Card, CardContent, CssBaseline } from '@mui/material';
import Navbar from '../components/Navbar';
import Sidebar from '../components/Sidebar';

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

    if (loading) {
        return (
            <Box sx={{ display: 'flex', justifyContent: 'center', alignItems: 'center', minHeight: '100vh' }}>
                <CircularProgress />
            </Box>
        );
    }

    if (error) {
        return <Typography color="error">{error}</Typography>;
    }

    return (
        <Box sx={{ display: 'flex', minHeight: '100vh' }}>
            <CssBaseline />
            <Navbar onMenuClick={toggleSidebar} />
            <Sidebar isOpen={isSidebarOpen} onClose={toggleSidebar} />

            <Box sx={{ flexGrow: 1, p: 3, transition: 'margin 0.3s', marginLeft: isSidebarOpen ? '240px' : '0' }}>
                <Typography variant="h4" gutterBottom>
                    Dashboard
                </Typography>
                {data && (
                    <Grid container spacing={3}>
                        {Object.entries(data).map(([key, value]) => (
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
                )}
            </Box>
        </Box>
    );
};

export default Dashboard;
