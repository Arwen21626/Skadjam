/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    "./html/**/*.html", // tous les fichiers HTML dans le dossier html
    "./html/css/input.css", // ton CSS avec @apply
    "./html/**/*.js",   // tous les fichiers JS si tu utilises des classes dynamiques
  ],
  theme: {
    extend: {}, // tu peux ajouter tes couleurs, polices, etc. ici
  },
  plugins: [], // ajoute tes plugins Tailwind ici si besoin
}
