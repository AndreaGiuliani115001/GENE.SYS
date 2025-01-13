import React from 'react';
import { useFormik } from 'formik';
import * as Yup from 'yup';
import { login } from '../services/authService';
import { TextField, Button, Box, Typography } from '@mui/material';

const Login: React.FC = () => {
    const formik = useFormik({
        initialValues: {
            email: '',
            password: '',
        },
        validationSchema: Yup.object({
            email: Yup.string().email('Email non valida').required('Email obbligatoria'),
            password: Yup.string().required('Password obbligatoria'),
        }),
        onSubmit: async (values) => {
            try {
                const data = await login(values);
                localStorage.setItem('token', data.token);
                alert('Login avvenuto con successo!');
                window.location.href = '/dashboard';
            } catch (error) {
                alert('Errore nel login: credenziali non valide o problema di rete.');
            }
        },
    });

    return (
        <Box
            sx={{
                height: '100vh',
                display: 'flex',
                justifyContent: 'center',
                alignItems: 'center',
                backgroundColor: '#242424', // Sfondo nero
            }}
        >
            <Box
                component="form"
                onSubmit={formik.handleSubmit}
                sx={{
                    width: 400,
                    padding: 3,
                    borderRadius: 2,
                    boxShadow: '0 4px 10px rgba(0,0,0,0.5)',
                    backgroundColor: '#ffffff', // Bianco per il contenitore
                }}
            >
                <Typography variant="h5" align="center" gutterBottom>
                    Login
                </Typography>
                <TextField
                    label="Email"
                    type="email"
                    name="email"
                    onChange={formik.handleChange}
                    onBlur={formik.handleBlur}
                    value={formik.values.email}
                    error={formik.touched.email && Boolean(formik.errors.email)}
                    helperText={formik.touched.email && formik.errors.email}
                    fullWidth
                    sx={{ marginBottom: 2 }}
                />
                <TextField
                    label="Password"
                    type="password"
                    name="password"
                    onChange={formik.handleChange}
                    onBlur={formik.handleBlur}
                    value={formik.values.password}
                    error={formik.touched.password && Boolean(formik.errors.password)}
                    helperText={formik.touched.password && formik.errors.password}
                    fullWidth
                    sx={{ marginBottom: 3 }}
                />
                <Button type="submit" variant="contained" color="primary" fullWidth>
                    Login
                </Button>
            </Box>
        </Box>
    );
};

export default Login;
