# MyAuth
MyAuth is simple authentication plugin.

## Last update: v1.2 (February 26th, 2017)
* Added SQLite3 database types, now it is default.

## Permissions:
- `myauth` - top permission of plugin, allows using everything

- `myauth.myadmin` - allows using command `/myadmin`
 
## Commands: 
- `/login <password>` - log in into an account
- `/register <password>` - register an account

- `/unregister <password> <password confirmation>` - unregister an account
- `/chp <password> <password confirmation>` - change account's password
- `/logout` - log out of an acccount

- `/myadmin` - plugin administrating

  - `/myadmin info <nickname>` - get info about an account
  - `/myadmin chp <nickname> <password>` - change password for an account
