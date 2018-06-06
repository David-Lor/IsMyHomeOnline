# Is my home online?

This project will let you know whenever your home Internet connection is down and up again, via Telegram.

The project has two parts:

* Remote: two PHP scripts that must run on a external server
* Home: a shell script that will POST to the Remote server with certain frequency (you can use whatever tool you want, just need to perform a POST request to your Remote server)

## Requirements

* A 24/7 server-like at home (i.e. a Raspberry Pi, an Arduino, an advanced router...) that will reach the external server, sending a POST request at a certain frequency
* A 24/7 external web server with PHP and cron/programmable tasks (i.e. a shared web hosting or a VPS -you can use a free hosting-)
* A Telegram account
* A Telegram Bot (create it via @BotFather)

## Parts

### Remote

In my case, for keeping low costs, I am running this on a free hosting at [000webhost](https://www.000webhost.com) that supports both PHP and Cron. The Remote server have two PHP scripts:

* endpoint.php: your device at home will POST to this PHP file
* service.php: this script is executed by the server with Cron, checking the last contact from the home. If no contact was performed, the script will notify us via Telegram

service.php must be set on cron to be executed each X minutes (000webhost allows to run one cron task per hosting each 10 minutes or longer). Telegram message will only be sent once after the status changed (from UP to DOWN and from DOWN to UP).

#### config.json

On the config.template.json (rename to config.json) file you must set some variables and you can customize others. These are the settings available:

* Time
  * LastContactMaxSeconds: time limit in seconds to consider your home connection as DOWN
* Endpoint
  * Token: set here a custom token. This avoids unauthorized users to send POST requests to the endpoint (the one provided is a random SHA512 string, but you can put whatever you want)
  * Return: the string to return after a POST request to the endpoint was successful
* Telegram
  * BotToken: your Telegram Bot token
  * ChatID: your Telegram User ID, or the ChatID of a group, if you want to send the message there
  * MessageDown: text of the message to be sent when your connection is DOWN
  * MessageDown: text of the message to be sent when your connection is UP again

### Home

The home server/device will just make a POST request to the Remote webserver endpoint. This can be performed with a simple command programmed with Cron in Linux.

I included two Linux shell scripts that use Curl and Wget. Both work the same way, they just send a POST request to the server.

#### Configs on the scripts

You must set two variables on the script you want to use:

* TOKEN: the same custom token as the one set on Remote config.json (Endpoint > Token)
* The complete address of your Remote endpoint.php file
