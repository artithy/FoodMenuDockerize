import { defineConfig } from 'vite'
import tailwindcss from '@tailwindcss/vite'
export default defineConfig({
  plugins: [
    tailwindcss(),
  ],
  server: {
    host: true, // allows access from network IPs
    port: 5173,
    allowedHosts: [
      'food-project.tithy.art', // add your hostname here
      'localhost',
      '127.0.0.1'
    ]
  }
})