import React from 'react';
import { useFormik } from 'formik';
import * as Yup from 'yup';
import { login } from '../services/authService.ts';
import { TextField, Button, Box, Checkbox, FormControlLabel } from '@mui/material';

const Login: React.FC = () => {
    const formik = useFormik({
        initialValues: {
            email: '',
            password: '',
            rememberMe: false,
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
                backgroundColor: 'background.default', // Usa il tema globale
            }}
        >
            <Box
                component="form"
                onSubmit={formik.handleSubmit}
                sx={{
                    width: 400,
                    padding: 3,
                    borderRadius: 2,
                    boxShadow: '0 4px 10px rgba(0,0,0,0.1)',
                    backgroundColor: 'background.paper', // Usa il tema globale
                    textAlign: 'center',
                }}
            >
                {/* Logo */}
                <Box sx={{ marginBottom: 3 }}>
                    <img
                        src="/apple-touch-icon.png" // Percorso del logo
                        alt="Logo"
                        width={80}
                        height={80}
                    />
                </Box>

                {/* Campo Email */}
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

                {/* Campo Password */}
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
                    sx={{ marginBottom: 2 }}
                />

                {/* Checkbox Ricordami */}
                <FormControlLabel
                    control={
                        <Checkbox
                            name="rememberMe"
                            onChange={formik.handleChange}
                            checked={formik.values.rememberMe}
                        />
                    }
                    label="Remember me"
                    sx={{ marginBottom: 2 }}
                />

                {/* Bottone Login */}
                <Button
                    type="submit"
                    variant="contained"
                    fullWidth
                    sx={{ padding: '10px 0', textTransform: 'uppercase', fontWeight: 'bold' }}
                >
                    Login
                </Button>
            </Box>
        </Box>
    );
};

export default Login;
