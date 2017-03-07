# MyAuth
MyAuth is simple authentication plugin.

## Last update: v1.2.1 (March 3rd, 2017)
* Some database improvements

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

## Configuration:
| Variable | Values | Description |
|   ---    |  ---   |     ---     |
| `type` | `mysql`, `yml`, `json`, `sqlite`| Database type |
| `ip` | IP | Connection IP (MySQL) |
| `username` | string | MySQL DB username |
| `password` | string | `username`'s password |
| `database` | string | Database/Folder name |
| `table_prefix` | string | Database/Folder prefix |
| `language` | `en`, `ru` | Plugin language |
| `time_format` | date string | for more see https://php.net/manual/function.date.php |
| `enable_autologin` | boolean | Enables/Disables autologin (using IP and CID) |
| `hide_players` | boolean | Enables/Disables hiding players if they aren't logged in|
| `cancel_block` | boolean | Cancel actions with blocks if player is not logged in |
| `cancel_drop` | boolean | Cancel dropping items if player is not logged in |
| `cancel_interacting` | boolean | Cancel interaction with objects if player is not logged in |
| `cancel_buckets` | boolean | Cancel using buckets if player is not logged in |
| `cancel_moving` | boolean | Cancel moving if player is not logged in |
| `cancel_eating` | boolean | Cancel eating if player is not logged in |
