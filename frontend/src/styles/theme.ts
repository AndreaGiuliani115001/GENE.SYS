import { createTheme } from '@mui/material/styles';

const theme = createTheme({
    palette: {
        primary: {
            main: '#27bcbc', // Colore dei bottoni principali
            contrastText: '#ffffff',
        },
        secondary: {
            main: '#ffbe3d', // Colore accenti o checkbox
        },
        background: {
            default: '#f4f5f9', // Sfondo generale
            paper: '#ffffff', // Sfondo per le card
        },
        text: {
            primary: '#333333', // Testo principale
            secondary: '#757575', // Testo secondario
        },
    },
    typography: {
        fontFamily: 'Roboto, Arial, sans-serif',
        button: {
            fontWeight: 700,
            textTransform: 'uppercase',
        },
    },
    shape: {
        borderRadius: 8, // Arrotondamento universale
    },
});

export default theme;