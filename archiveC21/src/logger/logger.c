#include "logger.h"

static FILE *log_file = NULL;

static const char *lvl_to_string(log_lvl_t level) {
    switch (level) {
        case LOG_DEBUG: return "[DEBUG]  ";
        case LOG_INFO:  return "[INFO]   ";
        case LOG_WARN:  return "[WARN]   ";
        case LOG_ERROR: return "[ERROR]  ";
        default:        return "[UNKNOWN]";
    }
}

void log_init() {
    char filename[22];
    char datebuf[9];

    time_t now = time(NULL);
    struct tm *t = localtime(&now);
    strftime(datebuf, sizeof(datebuf), "%Y%m%d", t);
    snprintf(filename, sizeof(filename), "logs/log_%s.log",datebuf);
    log_file = fopen(filename, "a+");
    if (!log_file) {
        perror("Impossible d'ouvrir le fichier de log");
    }
}

void log_close() {
    if (log_file) {
        fclose(log_file);
        log_file = NULL;
    }
}


void log_message(log_lvl_t level,
                 log_src_t src,
                 const char *ip,
                 int port,
                 const char *file,
                 int line,
                 const char *fmt, ...) {
    
    if (!log_file){
        /* fallback: write to stderr if file not available */
        FILE *out = stderr;
        time_t now = time(NULL);
        struct tm *t = localtime(&now);
        char timebuf[9];
        strftime(timebuf, sizeof(timebuf), "%H:%M:%S", t);
        if (src == LOG_SRC_CLIENT) {
            fprintf(out, "[%s] %s [CLIENT] (%s:%d) [%s:%d] ", timebuf, lvl_to_string(level), file, line, ip, port);
        } else {
            fprintf(out, "[%s] %s [SERV]   (%s:%d) ", timebuf, lvl_to_string(level), file, line);
        }
        va_list args2;
        va_start(args2, fmt);
        vfprintf(out, fmt, args2);
        va_end(args2);
        fprintf(out, "\n");
        fflush(out);
        return;
    }
    
    time_t now = time(NULL);
    struct tm *t = localtime(&now);

    char timebuf[9];
    strftime(timebuf, sizeof(timebuf), "%H:%M:%S", t);
    
    if (!log_file) {
        perror("Impossible d'ouvrir le fichier de log");
    }

    if (src == LOG_SRC_CLIENT){
        fprintf(log_file, "[%s] %s [CLIENT] (%s:%d) [%s:%d] ",
                timebuf,
                lvl_to_string(level),
                file,
                line,
                ip,
                port);
    }else{
        fprintf(log_file, "[%s] %s [SERV]   (%s:%d) ",
                timebuf,
                lvl_to_string(level),
                file,
                line);
    }

    va_list args;
    va_start(args, fmt);
    vfprintf(log_file, fmt, args);
    va_end(args);

    fprintf(log_file, "\n");
    fflush(log_file);
}

