#include "logger.h"

/* Fichier de log actuellement ouvert.
   NULL = aucun fichier ouvert. */
static FILE *log_file = NULL;

/* Convertit un niveau de log (enum log_lvl_t)
   en chaîne lisible pour l'affichage. */
static const char *lvl_to_string(log_lvl_t level) {
    switch (level) {
        case LOG_DEBUG: return "[DEBUG]  ";
        case LOG_INFO:  return "[INFO]   ";
        case LOG_WARN:  return "[WARN]   ";
        case LOG_ERROR: return "[ERROR]  ";
        default:        return "[UNKNOWN]";
    }
}

/* Initialise le système de log :
   - génère un nom de fichier basé sur la date (YYYYMMDD)
   - ouvre logs/log_<date>.log en mode append
   - stocke le FILE* dans log_file */
void log_init() {
    char filename[22];
    char datebuf[9];

    time_t now = time(NULL);
    struct tm *t = localtime(&now);

    /* Formatage de la date pour le nom du fichier */
    strftime(datebuf, sizeof(datebuf), "%Y%m%d", t);
    snprintf(filename, sizeof(filename), "logs/log_%s.log", datebuf);

    /* Ouverture du fichier de log */
    log_file = fopen(filename, "a+");
    if (!log_file) {
        perror("Impossible d'ouvrir le fichier de log");
    }
}

/* Ferme proprement le fichier de log si ouvert */
void log_close() {
    if (log_file) {
        fclose(log_file);
        log_file = NULL;
    }
}

/* Fonction principale d’écriture dans les logs.
   Paramètres :
   - level : niveau de log (DEBUG/INFO/WARN/ERROR)
   - src   : source du message (serveur ou client)
   - ip    : IP du client (si src == LOG_CLIENT)
   - port  : port du client
   - file  : fichier source appelant (macro __FILE__)
   - line  : ligne dans le fichier source (macro __LINE__)
   - fmt   : format printf du message
   Effets :
   - écrit dans le fichier log_file si ouvert
   - sinon écrit sur stderr (fallback)
*/
void log_message(log_lvl_t level,
                 log_src_t src,
                 const char *ip,
                 int port,
                 const char *file,
                 int line,
                 const char *fmt, ...) {
    
    /* Si aucun fichier ouvert → fallback vers stderr */
    if (!log_file){
        FILE *out = stderr;

        time_t now = time(NULL);
        struct tm *t = localtime(&now);
        char timebuf[9];
        strftime(timebuf, sizeof(timebuf), "%H:%M:%S", t);

        /* Format différent selon la source (client/serveur) */
        if (src == LOG_SRC_CLIENT) {
            fprintf(out, "[%s] %s [CLIENT] (%s:%d) [%s:%d] ",
                    timebuf, lvl_to_string(level), file, line, ip, port);
        } else {
            fprintf(out, "[%s] %s [SERV]   (%s:%d) ",
                    timebuf, lvl_to_string(level), file, line);
        }

        /* Impression du message formaté */
        va_list args2;
        va_start(args2, fmt);
        vfprintf(out, fmt, args2);
        va_end(args2);

        fprintf(out, "\n");
        fflush(out);
        return;
    }
    
    /* Cas normal : écriture dans le fichier de log */
    time_t now = time(NULL);
    struct tm *t = localtime(&now);

    char timebuf[9];
    strftime(timebuf, sizeof(timebuf), "%H:%M:%S", t);

    /* Formatage selon la source */
    if (src == LOG_SRC_CLIENT){
        fprintf(log_file, "[%s] %s [CLIENT] (%s:%d) [%s:%d] ",
                timebuf,
                lvl_to_string(level),
                file,
                line,
                ip,
                port);
    } else {
        fprintf(log_file, "[%s] %s [SERV]   (%s:%d) ",
                timebuf,
                lvl_to_string(level),
                file,
                line);
    }

    /* Impression du message formaté */
    va_list args;
    va_start(args, fmt);
    vfprintf(log_file, fmt, args);
    va_end(args);

    fprintf(log_file, "\n");
    fflush(log_file);
}
