/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    "./html/**/**/*.php", // tous les fichiers HTML dans le dossier html
    // "./html/**/**/*.css",
    "./html/css/input.css", // ton CSS avec @apply
    "./html/**/*.js",   // tous les fichiers JS si tu utilises des classes dynamiques
  ],
  theme: {},
  plugins: [], // ajoute tes plugins Tailwind ici si besoin
}
