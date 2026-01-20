/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    "./html/**/**/*.php", // tous les fichiers HTML dans le dossier html
    "./html/**/*.php", // tous les fichiers HTML dans le dossier html
    "./html/css/input.css", // ton CSS avec @apply
    "./html/**/*.js",   // tous les fichiers JS pour les classes dynamiques
  ],
  theme: {},
  plugins: [
    require('tailwind-scrollbar'),
  ],
}
