#ifndef SENDER_IMG_H
#define SENDER_IMG_H

#include "../logger/logger.h"
#include "../serveur/utils.h"
#include "sender_global.h"

int send_img(int fd, const char *image, size_t size);

#endif