import React from 'react';
import { Box, Typography, Button } from '@mui/material';
import WarningIcon from '@mui/icons-material/Warning';
import { useNavigate } from 'react-router-dom';

const NotAuthorized: React.FC = () => {
    const navigate = useNavigate();

    return (
        <Box
            sx={{
                display: 'flex',
                flexDirection: 'column',
                justifyContent: 'center',
                alignItems: 'center',
                height: '100vh',
                backgroundColor: '#f8f9fa',
                padding: 3,
            }}
        >
            <WarningIcon
                sx={{ fontSize: 80, color: '#f57c00', marginBottom: 2 }}
            />
            <Typography variant="h4" sx={{ marginBottom: 1, color: '#333' }}>
                Accesso Negato
            </Typography>
            <Typography variant="body1" sx={{ marginBottom: 3, color: '#555', textAlign: 'center', maxWidth: 400 }}>
                Non hai i permessi necessari per visualizzare questa pagina.
                Contatta l'amministratore se pensi ci sia un errore.
            </Typography>
            <Button
                variant="contained"
                color="primary"
                size="large"
                onClick={() => navigate('/dashboard')}
            >
                Torna alla Dashboard
            </Button>
        </Box>
    );
};

export default NotAuthorized;
