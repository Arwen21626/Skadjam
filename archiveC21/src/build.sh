#!/bin/bash

# Nom de l'exécutable
OUTPUT="delivraptor"

# Récupération de tous les fichiers .c (récursif)
C_FILES=$(find . -type f -name "*.c")

# Vérification qu'il y a au moins un fichier
if [ -z "$C_FILES" ]; then
    echo "Aucun fichier .c trouvé"
    exit 1
fi

# Compilation
echo "Compilation en cours..."
echo "gcc $C_FILES -o "$OUTPUT" -Wall -lpq"
gcc $C_FILES -o "$OUTPUT" -Wall -lpq

# Résultat
if [ $? -eq 0 ]; then
    echo "Compilation réussie : ./$OUTPUT"
else
    echo "Erreur de compilation"
    exit 1
fi