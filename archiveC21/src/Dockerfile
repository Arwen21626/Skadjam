FROM gcc:15 AS build


RUN apt-get update && apt-get install -y \
    libpq-dev \
    && rm -rf /var/lib/apt/lists/*

WORKDIR /app

# Copier les sources
COPY . .

# Compiler via ton script
RUN chmod +x build.sh && ./build.sh


FROM debian:bookworm-slim

RUN apt-get update && apt-get install -y \
    libpq5 \
    && rm -rf /var/lib/apt/lists/*

WORKDIR /app

COPY --from=build /app/delivraptor /app/delivraptor

COPY lst_client.data /app/lst_client.data
COPY image /app/image
COPY logs /app/logs

# Exposer le port
EXPOSE 6969

# Lancer ton serveur
CMD ["./delivraptor", "-p", "6969", "-f", "lst_client.data"]




