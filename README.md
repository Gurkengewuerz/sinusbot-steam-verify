## Steam Verifier for Sinusbot

<img src="https://github.com/Gurkengewuerz/sinusbot-steam-verify/raw/master/notForWebserver/preView/webview.png" alt="Webview" style="width: 50%;"/>

<img src="https://github.com/Gurkengewuerz/sinusbot-steam-verify/raw/master/notForWebserver/preView/animation.gif" alt="animation" style="width: 50%;"/>

### Setup
1. Install Sinusbot with the new Script Engine (0.9.15-b20cc30 +)
2. Create a database with a user und upload notForWebserver/database.sql
3. copy config.php.sample to config.php and edit the file carefully
4. set your private api_private_key in the config and put your SteamID as a perma Admin
5. copy notForWebserver/SteamVerify.js to sinusbotInstallDir/scripts
6. visit the Sinusbot webinterface and activate the script
7. change the Settings of the script (API Password = api_private_key from config.ini, API URL = something like http://localhost/verify/api.php)

### Libraries
- https://github.com/SmItH197/SteamAuthentication
- https://github.com/twbs/bootstrap

### TODO
- check on ClientMove Server Group (API Bug, waiting for @flyth)
- order Settings
- CSS fixes
