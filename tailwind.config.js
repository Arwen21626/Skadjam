/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    "./html/**/*.html", // tous les fichiers HTML dans le dossier html
    "./html/css/input.css", // ton CSS avec @apply
    "./html/**/*.js",   // tous les fichiers JS si tu utilises des classes dynamiques
  ],
  theme: {
    extend: {
      colors: {
        'vertFonce': '#365452',
        'vertClair': '#588A87',
        'beige': '#E3D7CC',
        'rouge': '#A70101',
        'bleuClair': '#E2F9FF',
        'turquoise': '#86D0CC'
      },
    }, // Ajout couleurs, polices
  },
  plugins: [], // ajoute tes plugins Tailwind ici si besoin
}
