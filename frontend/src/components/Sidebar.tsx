import React, { useContext } from 'react';
import { Drawer, List, ListItem, ListItemIcon, ListItemText, Divider, Typography } from '@mui/material';
import PeopleIcon from '@mui/icons-material/People';
import TimelineIcon from '@mui/icons-material/Timeline';
import FactoryIcon from '@mui/icons-material/Factory';
import AssignmentIcon from '@mui/icons-material/Assignment';
import BuildIcon from '@mui/icons-material/Build';
import WidgetsIcon from '@mui/icons-material/Widgets';
import ChecklistIcon from '@mui/icons-material/Checklist';
import PrecisionManufacturingIcon from '@mui/icons-material/PrecisionManufacturing';
import DocumentIcon from '@mui/icons-material/Description';
import SecurityIcon from '@mui/icons-material/Security';
import LicenseIcon from '@mui/icons-material/Verified';
import { useNavigate, useLocation } from 'react-router-dom';
import {AuthContext} from "../context/AuthContext.tsx";

interface SidebarProps {
    isOpen: boolean;
    onClose: () => void;
}

const Sidebar: React.FC<SidebarProps> = ({ isOpen, onClose }) => {
    const navigate = useNavigate();
    const location = useLocation();

    const menuItems = [
        { text: 'Utenti', icon: <PeopleIcon />, path: '/dashboard/utenti', roles: ['ROLE_MASTER', 'ROLE_ADMIN']},
        { text: 'Aziende', icon: <FactoryIcon />, path: '/dashboard/aziende', roles: ['ROLE_MASTER'] },
        { text: 'Linee produzione', icon: <TimelineIcon />, path: '/dashboard/linee-produzione', roles: ['ROLE_MASTER', 'ROLE_ADMIN', 'ROLE_PROJECT_MANAGER'] },
        { text: 'Progetti', icon: <AssignmentIcon />, path: '/dashboard/progetti', roles: ['ROLE_MASTER', 'ROLE_ADMIN', 'ROLE_PROJECT_MANAGER']},
        { text: 'Attività', icon: <BuildIcon />, path: '/dashboard/attivita', roles: ['ROLE_MASTER', 'ROLE_ADMIN', 'ROLE_PROJECT_MANAGER']},
        { text: 'Componenti', icon: <WidgetsIcon />, path: '/dashboard/componenti', roles: ['ROLE_MASTER', 'ROLE_ADMIN', 'ROLE_PROJECT_MANAGER'] },
        { text: 'Operazioni', icon: <PrecisionManufacturingIcon />, path: '/dashboard/operazioni', roles: ['ROLE_MASTER', 'ROLE_ADMIN', 'ROLE_PROJECT_MANAGER'] },
        { text: 'Checklist', icon: <ChecklistIcon />, path: '/dashboard/checklist', roles: ['ROLE_MASTER', 'ROLE_ADMIN', 'ROLE_PROJECT_MANAGER', 'ROLE_OPERATORE'] },
        { text: 'Documenti', icon: <DocumentIcon />, path: '/dashboard/documenti' , roles: ['ROLE_MASTER', 'ROLE_ADMIN', 'ROLE_PROJECT_MANAGER', 'ROLE_OPERATORE']},
    ];

    const configItems = [
        { text: 'Licenze', icon: <LicenseIcon />, path: '/dashboard/config/licenze', roles: ['ROLE_MASTER', 'ROLE_ADMIN'] },
        { text: 'Sicurezza', icon: <SecurityIcon />, path: '/dashboard/config/sicurezza', roles: ['ROLE_MASTER', 'ROLE_ADMIN'] },
    ];

    const { user } = useContext(AuthContext);

    const filteredMenuItems = menuItems.filter(item =>
        user && item.roles.includes(user.role)
    );

    const filteredConfigItems = configItems.filter(item =>
        user && item.roles.includes(user.role)
    );

    return (
        <Drawer
            variant="temporary"
            open={isOpen}
            onClose={onClose}
            sx={{
                '& .MuiDrawer-paper': {
                    width: 250,
                    boxSizing: 'border-box',
                    backgroundColor: '#f4f4f4',
                },
            }}
        >
            <div>
                <Typography
                    variant="h6"
                    align="center"
                    sx={{
                        padding: '16px',
                        backgroundColor: '#27bcbc',
                        color: '#fff',
                    }}
                >
                    GENE.SYS
                </Typography>
                <Divider />
                <List>
                    {filteredMenuItems.map((item, index) => (
                        <ListItem
                            key={index}
                            onClick={() => {
                                navigate(item.path);
                                onClose();
                            }}
                            sx={{
                                backgroundColor: location.pathname === item.path ? '#27bcbc' : 'transparent',
                                color: location.pathname === item.path ? '#1976d2' : '#757575',
                                '&:hover': {
                                    backgroundColor: '#27bcbc4c',
                                    color: '#1976d2',
                                },
                                padding: '12px 20px',
                                margin: '4px 0',
                                borderRadius: '4px',
                                cursor: 'pointer',
                            }}
                        >
                            <ListItemIcon
                                sx={{
                                    color: location.pathname === item.path ? '#FFFFFF' : '#27bcbc',
                                }}
                            >
                                {item.icon}
                            </ListItemIcon>
                            <ListItemText
                                primary={item.text}
                                sx={{
                                    color: location.pathname === item.path ? '#FFFFFF' : '#757575',
                                    fontWeight: location.pathname === item.path ? 'bold' : 'normal',
                                }}
                            />
                        </ListItem>
                    ))}
                </List>
                <Divider />
                <Typography
                    variant="subtitle2"
                    align="center"
                    sx={{
                        padding: '8px',
                        color: '#757575',
                        fontWeight: 'bold',
                    }}
                >
                    Configurazione
                </Typography>
                <List>
                    {filteredConfigItems.map((item, index) => (
                        <ListItem
                            key={index}
                            onClick={() => {
                                navigate(item.path);
                                onClose();
                            }}
                            sx={{
                                backgroundColor: location.pathname === item.path ? '#27bcbc' : 'transparent',
                                color: location.pathname === item.path ? '#1976d2' : '#757575',
                                '&:hover': {
                                    backgroundColor: '#27bcbc4c',
                                    color: '#1976d2',
                                },
                                padding: '12px 20px',
                                margin: '4px 0',
                                borderRadius: '4px',
                                cursor: 'pointer',
                            }}
                        >
                            <ListItemIcon
                                sx={{
                                    color: location.pathname === item.path ? '#FFFFFF' : '#27bcbc',
                                }}
                            >
                                {item.icon}
                            </ListItemIcon>
                            <ListItemText
                                primary={item.text}
                                sx={{
                                    color: location.pathname === item.path ? '#FFFFFF' : '#757575',
                                    fontWeight: location.pathname === item.path ? 'bold' : 'normal',
                                }}
                            />
                        </ListItem>
                    ))}
                </List>
            </div>
        </Drawer>
    );
};

export default Sidebar;
