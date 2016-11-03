//
//Copyright (C) 2016 Gurkengewuerz <niklas@gurkengewuerz.de>
//
//This program is free software: you can redistribute it and/or modify
//it under the terms of the GNU General Public License as published by
//the Free Software Foundation, either version 3 of the License, or
//(at your option) any later version.
//*
//This program is distributed in the hope that it will be useful,
//but WITHOUT ANY WARRANTY; without even the implied warranty of
//MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
//GNU General Public License for more details.
//
//You should have received a copy of the GNU General Public License
//along with this program.  If not, see <http://www.gnu.org/licenses/>.
//

/*
 * 
 * @author Gurkengewuerz <niklas@gurkengewuerz.de>
 * 
 */

registerPlugin({
    name: 'SteamVerify',
    version: '0.1',
    description: 'A steam verify Plugin',
    author: 'Gurkengewuerz <niklas@gurkengewuerz.de>',
    vars: {
        verifyGroup: {
            title: 'Verify Group',
            type: 'number',
            placeholder: '150'
        },
        apiURL: {
            title: 'API URL',
            type: 'string',
            placeholder: 'http://localhost/verify/api.php'
        },
        apiPasswd: {
            title: 'API Password',
            type: 'string',
            placeholder: 'adkadmkadmlkamfklmlaf'
        },
        sendNotVerifiedMessage: {
            title: "Send not verified Users at join a Message",
            type: 'select',
            options: [
                'Yes',
                'No'
            ]
        },
        notVerifiedMessage: {
            title: 'Not Verified Message [Placeholder: %u = Username]',
            type: 'multiline',
            placeholder: 'Hello %u! You are not verified! You can do this with by...',
            conditions: [
                {
                    field: 'sendNotVerifiedMessage',
                    value: 0
                }
            ]
        },
        successVerifiedMessage: {
            title: 'Success Verified Message [Placeholder: %u = Username, %p = Steam Name]',
            type: 'multiline',
            placeholder: 'Thank you %p for verifying you.'
        },
        errorVerifiedMessage: {
            title: 'Error Message [Placeholder: %u = Username, %e = Error Message]',
            type: 'multiline',
            placeholder: 'There was an error, please report it!'
        }
    }
}, function (sinusbot, config, info) {
    var event = require('event');
    var backend = require('backend');
    var engine = require('engine');

    var DEBUG = true;

    engine.log('Loading ' + info.name + ' v' + info.version + '  - by ' + info.author);

    event.on('chat', function (ev) {
        var cmd = ev.text.split(' ');
        switch (cmd[0].toLowerCase()) {
            case "!verify":
                sinusbot.http({
                    "method": "GET",
                    "url": config.apiURL + "?api_key=" + config.apiPasswd + "&type=verify&client_id=" + ev.client.UID() + "&verify_code=" + cmd[1],
                    "timeout": 6000
                }, function (error, response) {
                    if (response.statusCode !== 200) {
                        engine.log(error);
                        return;
                    }
                    var response = JSON.parse(response.data);
                    if (response["success"]) {
                        ev.client.chat(config.successVerifiedMessage.replace("%u", ev.client.nick()).replace("%p", response["name"]));
                        ev.client.addToServerGroup(config.verifyGroup);
                    } else {
                        ev.client.chat(config.errorVerifiedMessage.replace("%u", ev.client.nick()).replace("%e", response["error"]));
                    }
                });
                break;

            case "!check":
                sinusbot.http({
                    "method": "GET",
                    "url": config.apiURL + "?api_key=" + config.apiPasswd + "&type=getSteamIDbyClient&client_id=" + cmd[1],
                    "timeout": 6000
                }, function (error, response) {
                    if (response.statusCode !== 200) {
                        engine.log(error);
                        return;
                    }
                    var response = JSON.parse(response.data);
                    if (response["success"]) {
                        // SUCCESS getSteamIDbyClient
                        var stringList = "Clients:\r\n";
                        for (var key in response["steamids"]) {
                            var val = response["steamids"][key];
                            stringList += "- " + val + " => " + key + "\r\n";
                        }
                        ev.client.chat(stringList);
                    } else {
                        sinusbot.http({
                            "method": "GET",
                            "url": config.apiURL + "?api_key=" + config.apiPasswd + "&type=getClientsBySteamID&steam_id=" + cmd[1],
                            "timeout": 6000
                        }, function (error, response) {
                            if (response.statusCode !== 200) {
                                engine.log(error);
                                return;
                            }
                            var response = JSON.parse(response.data);
                            if (response["success"]) {
                                // SUCCESS getClientsBySteamID
                                var stringList = "Clients:\r\n";
                                for (var key in response["teamspeakids"]) {
                                    var val = response["teamspeakids"][key];
                                    var client = backend.getClientByUniqueID(key);
                                    var clientName = "Unknown";
                                    if(typeof client !== undefined){
                                        clientName = client.nick();
                                    }
                                    stringList += "- " + key + " => " + clientName + "\r\n";
                                }
                                ev.client.chat(stringList);
                            } else {
                                ev.client.chat("No Client found!");
                            }
                        });
                    }
                });
                break;
        }
    });

    event.on('clientMove', function (ev) {
        if (config.sendNotVerifiedMessage === 1) {
            if (ev.fromChannel === null) {
                ev.client.chat(config.notVerifiedMessage.replace("%u", ev.client.nick()));
            }
        }
    });
});
