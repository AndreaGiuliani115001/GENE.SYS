import React, { useState, useContext } from 'react';
import { useNavigate } from 'react-router-dom';
import {AppBar, Toolbar, IconButton, Menu, MenuItem, Box} from '@mui/material';
import MenuIcon from '@mui/icons-material/Menu';
import AccountCircle from '@mui/icons-material/AccountCircle';
import DashboardIcon from '@mui/icons-material/Dashboard'; // Icona per la Dashboard
import { AuthContext } from '../context/AuthContext';

interface NavbarProps {
    onMenuClick: () => void;
}

const Navbar: React.FC<NavbarProps> = ({ onMenuClick }) => {
    const { logout } = useContext(AuthContext);
    const [anchorEl, setAnchorEl] = useState<null | HTMLElement>(null);
    const isMenuOpen = Boolean(anchorEl);
    const navigate = useNavigate();

    const handleMenuOpen = (event: React.MouseEvent<HTMLElement>) => {
        setAnchorEl(event.currentTarget);
    };

    const handleMenuClose = () => {
        setAnchorEl(null);
    };

    const handleLogout = () => {
        handleMenuClose();
        logout();
        navigate('/login');
    };

    const handleNavigateDashboard = () => {
        navigate('/dashboard');
    };

    return (
        <AppBar position="fixed">
            <Toolbar>
                {/* Pulsante per aprire la Sidebar */}
                <IconButton
                    color="inherit"
                    edge="start"
                    onClick={onMenuClick}
                    sx={{ mr: 2 }}
                >
                    <MenuIcon />
                </IconButton>

                {/* Logo */}
                <Box  sx={{ flexGrow: 1, textAlign: 'center', filter: 'invert(1)' }}>
                    <img
                        src="/apple-touch-icon.png" // Percorso del logo
                        alt="Logo"
                        width={50}
                        height={50}
                    />
                </Box>

                {/* Pulsante con icona per tornare alla Dashboard */}
                <IconButton
                    color="inherit"
                    onClick={handleNavigateDashboard}
                    sx={{ mr: 2 }}
                >
                    <DashboardIcon /> {/* Icona della Dashboard */}
                </IconButton>

                {/* Menu Utente */}
                <IconButton
                    color="inherit"
                    onClick={handleMenuOpen}
                >
                    <AccountCircle />
                </IconButton>
                <Menu
                    anchorEl={anchorEl}
                    open={isMenuOpen}
                    onClose={handleMenuClose}
                >
                    <MenuItem onClick={handleMenuClose}>Impostazioni</MenuItem>
                    <MenuItem onClick={handleLogout}>Logout</MenuItem>
                </Menu>
            </Toolbar>
        </AppBar>
    );
};


export default Navbar;
