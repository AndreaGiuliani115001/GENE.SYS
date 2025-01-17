import React, { useEffect, useState } from 'react';
import { Box, CircularProgress, Typography, Grid, Card, CardContent } from '@mui/material';
import { fetchAziende } from '../services/AziendeService.ts';

interface CampoOperativo {
    id: string;
    nome: string;
}

interface Azienda {
    id: string;
    nome: string;
    indirizzo: string;
    telefono: string;
    email: string;
    pIva: string;
    campoOperativo: CampoOperativo;
}

const Aziende: React.FC = () => {
    const [aziende, setAziende] = useState<Azienda[]>([]);
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState<string | null>(null);

    useEffect(() => {
        const fetchData = async () => {
            try {
                const response = await fetchAziende();
                setAziende(response);
            } catch (err: any) {
                setError(err.response?.data?.message || 'Errore nel caricamento delle aziende.');
            } finally {
                setLoading(false);
            }
        };

        fetchData();
    }, []);

    if (loading) {
        return (
            <Box sx={{ display: 'flex', justifyContent: 'center', alignItems: 'center', minHeight: '100vh' }}>
                <CircularProgress />
            </Box>
        );
    }

    if (error) {
        return (
            <Box sx={{ display: 'flex', justifyContent: 'center', alignItems: 'center', minHeight: '100vh' }}>
                <Typography color="error">{error}</Typography>
            </Box>
        );
    }

    return (
        <>
            <Grid container spacing={3}>
                {aziende.map((azienda) => (
                    <Grid item xs={12} sm={6} md={4} key={azienda.id}>
                        <Card>
                            <CardContent>
                                <Typography variant="h5" gutterBottom>
                                    {azienda.nome}
                                </Typography>
                                <Typography variant="body1">
                                    <strong>Indirizzo:</strong> {azienda.indirizzo}
                                </Typography>
                                <Typography variant="body1">
                                    <strong>Email:</strong> {azienda.email}
                                </Typography>
                                <Typography variant="body1">
                                    <strong>Telefono:</strong> {azienda.telefono}
                                </Typography>
                                <Typography variant="body1">
                                    <strong>Partita IVA:</strong> {azienda.pIva}
                                </Typography>
                                <Typography variant="body1">
                                    <strong>Campo Operativo:</strong> {azienda.campoOperativo.nome}
                                </Typography>
                            </CardContent>
                        </Card>
                    </Grid>
                ))}
            </Grid>
        </>
    );
};

export default Aziende;
