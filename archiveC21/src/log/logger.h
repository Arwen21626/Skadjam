#ifndef LOGGER_H
#define LOGGER_H

#include <stdio.h>
#include <time.h>
#include <stdarg.h>



typedef enum {
    LOG_DEBUG,
    LOG_INFO,
    LOG_WARN,
    LOG_ERROR
} log_lvl_t;

typedef enum {
    LOG_SRC_CLIENT,
    LOG_SRC_SERV
} log_src_t;

void log_init();
void log_close();

void log_message(log_lvl_t level,
                 log_src_t src,
                 const char *ip,
                 int port,
                 const char *file,
                 int line,
                 const char *fmt, ...);

#define LOG_SERV(level, ...) log_message(level, LOG_SRC_SERV, NULL, 0, __FILE__, __LINE__, __VA_ARGS__)
#define LOG_CLIENT(level, ip, port, ...) log_message(level, LOG_SRC_CLIENT, ip, port, __FILE__, __LINE__, __VA_ARGS__)


#endif