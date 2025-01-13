import axios from 'axios';

const API_URL = 'http://localhost:8080/auth/login';

export const login = async (credentials: { email: string; password: string }) => {
    const response = await axios.post(API_URL, credentials);
    return response.data;
};
