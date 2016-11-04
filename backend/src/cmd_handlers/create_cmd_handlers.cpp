#include "create_cmd_handlers.h"
#include "cmd_getpublicinfo_handler.h"
#include "cmd_hello_handler.h"
#include "cmd_login_handler.h"
#include "cmd_addnews_handler.h"
#include "cmd_send_letters_to_subscribers_handler.h"
#include "cmd_users_handler.h"
#include "cmd_user_handler.h"

void create_cmd_handlers(QMap<QString, ICmdHandler *> &pHandlers){
	QVector<ICmdHandler *> v;
	v.push_back(new CmdHelloHandler());
	v.push_back(new CmdLoginHandler());
	v.push_back(new CmdAddNewsHandler());
	v.push_back(new CmdGetPublicInfoHandler());
	v.push_back(new CmdSendLettersToSubscribersHandler());
	v.push_back(new CmdUsersHandler());
	v.push_back(new CmdUserHandler());

	for(int i = 0; i < v.size(); i++){
		QString cmd = v[i]->cmd();
		if(pHandlers.contains(cmd)){
			qDebug() << "[WARNING] cmd_handler for command " << cmd << " - already registered and will be skipped";	
		}else{
			pHandlers[cmd] = v[i];
		}
	}
}
