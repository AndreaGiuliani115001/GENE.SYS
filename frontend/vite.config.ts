import { defineConfig } from 'vite';
import react from '@vitejs/plugin-react';

export default defineConfig({
  plugins: [react()],
  server: {
    proxy: {
      '/api': {
        target: 'http://localhost:8080', // URL del backend
        changeOrigin: true,
        rewrite: (path) => path.replace(/^\/api/, ''), // Rimuove il prefisso /api
      },
    },
  },
});
