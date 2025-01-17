import React from 'react';
import { Box, Typography, Button } from '@mui/material';
import ErrorOutlineIcon from '@mui/icons-material/ErrorOutline';
import { useNavigate } from 'react-router-dom';

const NotFound: React.FC = () => {
    const navigate = useNavigate();

    return (
        <Box
            sx={{
                display: 'flex',
                flexDirection: 'column',
                justifyContent: 'center',
                alignItems: 'center',
                height: '100vh',
                backgroundColor: '#f4f4f4',
                padding: 3,
            }}
        >
            <ErrorOutlineIcon
                sx={{ fontSize: 100, color: '#d32f2f', marginBottom: 2 }}
            />
            <Typography variant="h3" sx={{ marginBottom: 1, color: '#333' }}>
                Pagina Non Trovata
            </Typography>
            <Typography variant="body1" sx={{ marginBottom: 3, color: '#555', textAlign: 'center', maxWidth: 400 }}>
                La pagina che stai cercando non esiste. Controlla l'URL o torna alla dashboard.
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

export default NotFound;
