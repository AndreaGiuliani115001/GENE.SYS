import axios from 'axios';

const API_URL = 'http://localhost:8080'; // URL base del backend

export const fetchAziende = async () => {
    const token = localStorage.getItem('token');
    if (!token) {
        throw new Error('Token non trovato. Effettua il login.');
    }

    try {
        const response = await axios.get(`${API_URL}/aziende`, {
            headers: {
                Authorization: `Bearer ${token}`,
            },
        });
        return response.data; // Restituisce la lista delle aziende
    } catch (error: any) {
        console.error('Errore nel caricamento delle aziende:', error);
        throw error;
    }
};
