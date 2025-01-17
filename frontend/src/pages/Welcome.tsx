import React from 'react';
import { Box, Button, Typography, Grid, Card, CardContent } from '@mui/material';
import { Build, Settings, Recycling} from '@mui/icons-material';  // Icone di Material-UI
import { useNavigate } from 'react-router-dom';

const Welcome: React.FC = () => {
    const navigate = useNavigate();

    const goToLogin = () => {
        navigate('/login');
    };


    return (
        <Box
            sx={{
                display: 'flex',
                flexDirection: 'column',
                alignItems: 'center',
                justifyContent: 'center',
                height: '100vh',
                textAlign: 'center',
                padding: 3,
            }}
        >
            {/* Logo (puoi sostituirlo con un'immagine) */}
            <Box sx={{ mb: 2,
                display: 'inline-block',
                overflow: 'hidden', // Nasconde la parte dell'immagine che esce dal box
                '& img': {
                    transition: 'transform 0.3s ease-in-out', // Transizione fluida per la trasformazione
                },
                '&:hover img': {
                    transform: 'scale(1.1)', // Aumenta l'immagine quando passa sopra
                },}}>
                <img src="/apple-touch-icon.png" alt="GENE.SYS Logo" width="100" />
            </Box>

            {/* Titolo e descrizione */}
            <Typography variant="h4" gutterBottom sx={{ fontWeight: 600 }}>
                BENVENUTO IN GENE.SYS
            </Typography>
            <Typography variant="body1" paragraph sx={{ color: '#757575' }}>
                Software per la digitalizzazione della raccolta dati
                <br />
                di processi industriali e manutentivi.
            </Typography>

            {/* Card per le varie sezioni */}
            <Grid container spacing={3} sx={{ mb: 4 }} justifyContent="center">
                <Grid item xs={12} sm={4}>
                    <Card>
                        <CardContent>
                            <Build fontSize="large" sx={{ color: '#2ec4b6', mb: 2 }} />
                            <Typography variant="h6">Produzione</Typography>
                            <Typography variant="body2" color="text.secondary">

                            </Typography>
                        </CardContent>
                    </Card>
                </Grid>

                <Grid item xs={12} sm={4}>
                    <Card>
                        <CardContent>
                            <Settings fontSize="large" sx={{ color: '#2ec4b6', mb: 2 }} />
                            <Typography variant="h6">Manutenzione</Typography>
                            <Typography variant="body2" color="text.secondary">

                            </Typography>
                        </CardContent>
                    </Card>
                </Grid>

                <Grid item xs={12} sm={4}>
                    <Card>
                        <CardContent>
                            <Recycling fontSize="large" sx={{ color: '#2ec4b6', mb: 2 }} />
                            <Typography variant="h6">Smaltimento/DPP</Typography>
                            <Typography variant="body2" color="text.secondary">

                            </Typography>
                        </CardContent>
                    </Card>
                </Grid>
            </Grid>

            {/* Pulsante per andare al login */}
            <Button
                variant="contained"
                sx={{
                    color: 'white',
                    padding: '12px 20px',
                    borderRadius: '4px',
                }}
                onClick={goToLogin}
            >
                Accedi
            </Button>

            {/* Footer */}
            <Box sx={{ mt: 3, color: '#757575', fontSize: '12px' }}>
                <Typography variant="body2" align="center">
                    © 2025 GENE.SYS. Tutti i diritti riservati.
                </Typography>
            </Box>
        </Box>
    );
};

export default Welcome;
