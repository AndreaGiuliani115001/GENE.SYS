import axios from 'axios';

const API_URL = 'http://localhost:8080'; // URL del backend

export const login = async (credentials: { email: string; password: string }) => {
    const response = await axios.post(`${API_URL}/auth/login`, credentials, {
        headers: {
            'Content-Type': 'application/json',
        },
    });
    return response.data;
};
