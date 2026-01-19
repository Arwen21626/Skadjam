#ifndef SENDER_GLOBAL_H
#define SENDER_GLOBAL_H

#include "../logger/logger.h"
#include <sys/types.h>
#include <sys/socket.h>
#include <unistd.h>
#include <string.h>
#include <stdio.h>
#include <errno.h>

int push(int fd, const char *msg, const char *cmd);
int push_binary(int fd, const void *data, size_t size, char cmd);

#endif