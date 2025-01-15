import axios from 'axios';

export const fetchDashboardData = async () => {
    const token = localStorage.getItem('token');
    if (!token) {
        throw new Error('Token non trovato. Effettua il login.');
    }

    try {
        const response = await axios.get('http://localhost:8080/dashboard', {
            headers: {
                Authorization: `Bearer ${token}`,
            },
        });
        console.log('Risposta API /dashboard:', response); // Log della risposta
        return response.data;
    } catch (error: any) {
        console.error('Errore nella chiamata API /dashboard:', error); // Log dell'errore
        throw error;
    }
};

