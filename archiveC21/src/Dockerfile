FROM debian:stable

# Dépendances pour compiler + libpq5
RUN apt-get update && apt-get install -y \
    build-essential \
    libpq5 \
    libpq-dev \
    && rm -rf /var/lib/apt/lists/*

WORKDIR /app

# Copier les sources
COPY . /app

# Compiler via ton script
RUN chmod +x build.sh && ./build.sh

# Créer les dossiers nécessaires
RUN mkdir -p /app/images /app/logs

# Exposer le port
EXPOSE 6969

# Lancer ton serveur
CMD ["./recupraptor", "-p", "6969", "-f", "lst_client.data"]
