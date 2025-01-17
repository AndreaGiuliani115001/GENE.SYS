import { StrictMode } from 'react';
import { createRoot } from 'react-dom/client';
import './styles/index.css'; // Stili personalizzati
import App from './App.tsx';
import { AuthProvider } from './context/AuthContext';
import { ThemeProvider } from '@mui/material/styles';
import CssBaseline from '@mui/material/CssBaseline';
import theme from './styles/theme'; // Tema globale Material-UI

// Seleziona il root element
const rootElement = document.getElementById('root');

if (rootElement) {
    createRoot(rootElement).render(
        <StrictMode>
            <ThemeProvider theme={theme}>
                <CssBaseline /> {/* Reset CSS globale */}
                <AuthProvider>
                    <App />
                </AuthProvider>
            </ThemeProvider>
        </StrictMode>
    );
} else {
    console.error("Root element not found!");
}