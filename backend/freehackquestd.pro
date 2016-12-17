TEMPLATE = app
TARGET = freehackquestd

QT += core sql network websockets
QT -= gui

CONFIG   += console
CONFIG   -= app_bundle
OBJECTS_DIR = tmp/
MOC_DIR = tmp/
RCC_DIR = tmp/
CONFIG += c++11 c++14

SOURCES += \
	src/main.cpp \
	src/smtp/smtp.cpp \
	src/websocketserver.cpp \
	src/usertoken.cpp \
	src/exportapi.cpp \
	src/errors.cpp \
	src/error.cpp \
	src/cmd_handlers/create_cmd_handlers.cpp \
	src/cmd_handlers/cmd_addnews_handler.cpp \
	src/cmd_handlers/cmd_getpublicinfo_handler.cpp \
	src/cmd_handlers/cmd_hello_handler.cpp \
	src/cmd_handlers/cmd_login_handler.cpp \
	src/cmd_handlers/cmd_send_letters_to_subscribers_handler.cpp \
	src/cmd_handlers/cmd_users_handler.cpp \
	src/cmd_handlers/cmd_user_handler.cpp \
	src/cmd_handlers/cmd_updatedatabase_handler.cpp \
	src/updates/create_list_updates.cpp \
	src/updates/update0067.cpp \
	src/updates/update0068.cpp \

HEADERS += \
	src/smtp/smtp.h \
	src/interfaces/iwebsocketserver.h \
	src/interfaces/icmdhandler.h \
	src/interfaces/iupdate.h \
	src/websocketserver.h \
	src/usertoken.h \
	src/exportapi.h \
	src/errors.h \
	src/error.h \
	src/cmd_handlers/create_cmd_handlers.h \
	src/cmd_handlers/cmd_addnews_handler.h \
	src/cmd_handlers/cmd_getpublicinfo_handler.h \
	src/cmd_handlers/cmd_hello_handler.h \
	src/cmd_handlers/cmd_login_handler.h \
	src/cmd_handlers/cmd_send_letters_to_subscribers_handler.h \
	src/cmd_handlers/cmd_users_handler.h \
	src/cmd_handlers/cmd_user_handler.h \
	src/cmd_handlers/cmd_updatedatabase_handler.h \
	src/updates/create_list_updates.h \
	src/updates/update0067.h \
	src/updates/update0068.h \
