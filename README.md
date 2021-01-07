# ispconfig3_zabbix

Integrates website monitoring in the IPSConfig interface with a Zabbix server.\
Work with a single or multi servers ISPConfig >= 3.1 and Zabbix 5.2.

- [ISPConfig](https://www.ispconfig.org/)
- [Zabbix](https://www.zabbix.com/)

## How to install

Connect to the master ISPConfig server and go into the plugin directory.\
>```cd /usr/local/ispconfig/interface/web/```

*By default, the ISPConfig plugins are located in ```/usr/local/ispconfig/interface/web/```*

Clone this git : \
>```git clone https://github.com/cadjou/ispconfig3_zabbix.git```

Change the name to Zabbix :\
>```mv ispconfig3_zabbix zabbix```

Allow the administrator to access to Zabbix plugin:\
>Go to your **ISPConfig Interface > System > User** and modify the admin user\
>Check the **Zabbix checkbox** and save\
>Disconnect you from the interface then reconnect

Now you can see **The Zabbix Plugin in the topbar menu**

### MySQL Permission
By default, the ISPConfig MySQL User cannot Add/Alter/Drop Table, so you get this message when you click on the Zabbix menu\
```CREATE command denied to user 'ispconfig'@'localhost' for table 'zabbix_monitor'```

Change the user for the MySQL install by the root user:\
```nano /usr/local/ispconfig/interface/web/zabbix/install.php```
```php
    $db_host      = DB_HOST;
    $db_port      = DB_PORT;
    $db_charset   = DB_CHARSET;
    $db_database  = DB_DATABASE;
    $db_root_user = **DB_USER**; // By default DB_USER or user with create table permission
    $db_root_pwd  = **DB_PASSWORD**; // By DB_PASSWORD or Your root password
```
Click again on Zabbix in topbar menu, and the install will be ok.\
*If it's ok, the 2 files install will be deleted*

## How to configure

On the left menu, click on Administrator to complet the form to like ISPConfig and a Zabbix server.
There are 4 sections:
- Access to zabbix
    - URL Server
    - Login (Admin role)
    - Password
- Administration Limitation for ISPConfig
    - Maximum Monitor (-1 = illimited)
    - Minimum Check Period
    - Maximum Number of retry 
    - Maximum Time out
- SMTP's parameters to send the alerts
    - Server
    - Port
    - Use SSL
    - User
    - Password
    - Sender email
- Email parameters to recieve Alert by email
    - Recipient email
    - Alert Message
    - Recovery Message

On the left menu, these are also parameters for the Resellers and Clients to give Access and Limitations for earch of them

## Use Mutu server by KWA Digital *(Coming soon)*
If you don't have Zabbix server you can use the KWA Digital Zabbix server for 15 days for free to try.

 

