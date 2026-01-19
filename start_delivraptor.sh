#!/bin/bash
echo "installation des dépendance..."
sudo apt update
echo "Installation de libpq-dev..." 
sudo apt install -y libpq-dev 
echo "Vérification..." 
if dpkg -s libpq-dev >/dev/null 2>&1; 
then 
    echo "[OK] libpq-dev installé correctement" 
else 
    echo "[ERREUR] libpq-dev n'a pas été installé" 
    exit 1 
fi
echo ">> installation terminé"

echo "éxécution des script..."
./archiveC21/src/build.sh
./archiveC21/php/exec.sh
echo ">> éxécution terminé"