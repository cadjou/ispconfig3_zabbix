# Page Admin
## Parametres de Connexion
- Url du serveur
- Login
- MDP

## Parametres Globaux
- Periode de check
- Timeout
- Code status
- Retention historique
- Retention graph
- Retention apres suppression
- Permission parametres par le client

## Parametres par defaut
- Periode de check -> 60min
- Timeout -> 20s
- Code status -> 200
- Retention historique -> 90j
- Retention graph -> 730j
- Retention apres suppression -> 30j

# Page Zabbix
## Parametres particuliers
- Url domaine
- Texte recherché

## Actions
- Ajouter moniteur si reste siteweb non monitoré
- Liste moniteur selon acces utilisateur connecté
- Supprimer moniteur avec validation de suppression de l'historique
- Lien vers le graphique
- Lien vers le site web

```
Parameters list
Admin
    Server
        zabbix_host
        zabbix_user
        zabbix_pwd

    Constant
        glue
        isp_keyword
        httptest_keyword
        step_keyword
        application_keyword
        trigger_keyword

    Limits Admin
        mutu
        nbr_monitor

    Limits Reseller
        reseller_connexion
        reseller_trend
        reseller_event
        reseller_smtp
        reseller_alert

    Limits Client
        client_connexion
        client_trend
        client_event
        client_smtp
        client_alert

    Limits Monitor
        periode_check
        retries
        timeout

    Default Monitor
        periode_check
        retries
        timeout
        code_status

    Default SMTP
        smtp_host
        smtp_port
        smtp_ssl
        smtp_user
        smtp_pwd
        smtp_sender

User
    connexion
        user
        pwd

    email
        receiver

    Message
        alert_subject
        alert_content
        recovery_subject
        recovery_content

    Constant
        httptest_keyword
        step_keyword
        trigger_keyword
        application_keyword

    Default
        periode_check
        retries
        timeout
        code_status

    Email
        smtp_host
        smtp_port
        smtp_ssl
        smtp_user
        smtp_pwd
        smtp_sender
    
```
```
Array
(
    [s_old] => Array
        (
            [user] => Array
                (
                    [userid] => 16
                    [sys_userid] => 1
                    [sys_groupid] => 1
                    [sys_perm_user] => riud
                    [sys_perm_group] => riud
                    [sys_perm_other] => 
                    [username] => ****
                    [passwort] => ********************
                    [modules] => zabbix,tools,help,mail,dns,dashboard,sites
                    [startmodule] => dashboard
                    [app_theme] => default
                    [typ] => user
                    [active] => 1
                    [language] => fr
                    [groups] => 16
                    [default_group] => 16
                    [client_id] => 15
                    [id_rsa] => 
                    [ssh_rsa] => 
                    [lost_password_function] => 1
                    [lost_password_hash] => 
                    [lost_password_reqtime] => 
                    [theme] => default
                )

            [language] => fr
            [theme] => default
            [plugin_cache] => Array
                (
                    [client:client:on_after_insert] => Array
                        (
                            [0] => Array
                                (
                                    [plugin] => clients_template_plugin
                                    [function] => apply_client_templates
                                    [module] => 
                                )

                        )

                    [client:client:on_after_update] => Array
                        (
                            [0] => Array
                                (
                                    [plugin] => clients_template_plugin
                                    [function] => apply_client_templates
                                    [module] => 
                                )

                        )

                    [client:reseller:on_after_insert] => Array
                        (
                            [0] => Array
                                (
                                    [plugin] => clients_template_plugin
                                    [function] => apply_client_templates
                                    [module] => 
                                )

                        )

                    [client:reseller:on_after_update] => Array
                        (
                            [0] => Array
                                (
                                    [plugin] => clients_template_plugin
                                    [function] => apply_client_templates
                                    [module] => 
                                )

                        )

                    [dns:dns_slave:on_after_insert] => Array
                        (
                            [0] => Array
                                (
                                    [plugin] => dns_dns_slave_plugin
                                    [function] => dns_dns_slave_edit
                                    [module] => 
                                )

                        )

                    [dns:dns_slave:on_after_update] => Array
                        (
                            [0] => Array
                                (
                                    [plugin] => dns_dns_slave_plugin
                                    [function] => dns_dns_slave_edit
                                    [module] => 
                                )

                        )

                    [dns:dns_soa:on_after_insert] => Array
                        (
                            [0] => Array
                                (
                                    [plugin] => dns_dns_soa_plugin
                                    [function] => dns_dns_soa_edit
                                    [module] => 
                                )

                        )

                    [dns:dns_soa:on_after_update] => Array
                        (
                            [0] => Array
                                (
                                    [plugin] => dns_dns_soa_plugin
                                    [function] => dns_dns_soa_edit
                                    [module] => 
                                )

                        )

                    [mail:mail_domain:on_after_insert] => Array
                        (
                            [0] => Array
                                (
                                    [plugin] => mail_mail_domain_plugin
                                    [function] => mail_mail_domain_edit
                                    [module] => 
                                )

                        )

                    [mail:mail_domain:on_after_update] => Array
                        (
                            [0] => Array
                                (
                                    [plugin] => mail_mail_domain_plugin
                                    [function] => mail_mail_domain_edit
                                    [module] => 
                                )

                        )

                    [mail:mail_user_filter:on_after_insert] => Array
                        (
                            [0] => Array
                                (
                                    [plugin] => mail_user_filter_plugin
                                    [function] => mail_user_filter_edit
                                    [module] => 
                                )

                        )

                    [mail:mail_user_filter:on_after_update] => Array
                        (
                            [0] => Array
                                (
                                    [plugin] => mail_user_filter_plugin
                                    [function] => mail_user_filter_edit
                                    [module] => 
                                )

                        )

                    [mail:mail_user_filter:on_after_delete] => Array
                        (
                            [0] => Array
                                (
                                    [plugin] => mail_user_filter_plugin
                                    [function] => mail_user_filter_del
                                    [module] => 
                                )

                        )

                    [mailuser:mail_user_filter:on_after_insert] => Array
                        (
                            [0] => Array
                                (
                                    [plugin] => mail_user_filter_plugin
                                    [function] => mail_user_filter_edit
                                    [module] => 
                                )

                        )

                    [mailuser:mail_user_filter:on_after_update] => Array
                        (
                            [0] => Array
                                (
                                    [plugin] => mail_user_filter_plugin
                                    [function] => mail_user_filter_edit
                                    [module] => 
                                )

                        )

                    [mailuser:mail_user_filter:on_after_delete] => Array
                        (
                            [0] => Array
                                (
                                    [plugin] => mail_user_filter_plugin
                                    [function] => mail_user_filter_del
                                    [module] => 
                                )

                        )

                    [sites:web_database_user:on_after_update] => Array
                        (
                            [0] => Array
                                (
                                    [plugin] => sites_web_database_user_plugin
                                    [function] => sites_web_database_user_edit
                                    [module] => 
                                )

                        )

                    [sites:web_database_user:on_after_insert] => Array
                        (
                            [0] => Array
                                (
                                    [plugin] => sites_web_database_user_plugin
                                    [function] => sites_web_database_user_edit
                                    [module] => 
                                )

                        )

                    [sites:web_vhost_domain:on_after_insert] => Array
                        (
                            [0] => Array
                                (
                                    [plugin] => sites_web_vhost_domain_plugin
                                    [function] => sites_web_vhost_domain_edit
                                    [module] => 
                                )

                        )

                    [sites:web_vhost_domain:on_after_update] => Array
                        (
                            [0] => Array
                                (
                                    [plugin] => sites_web_vhost_domain_plugin
                                    [function] => sites_web_vhost_domain_edit
                                    [module] => 
                                )

                        )

                    [vm:openvz_vm:on_after_insert] => Array
                        (
                            [0] => Array
                                (
                                    [plugin] => vm_openvz_plugin
                                    [function] => openvz_vm_insert
                                    [module] => 
                                )

                        )

                    [vm:openvz_vm:on_after_update] => Array
                        (
                            [0] => Array
                                (
                                    [plugin] => vm_openvz_plugin
                                    [function] => openvz_vm_update
                                    [module] => 
                                )

                        )

                    [vm:openvz_vm:on_after_delete] => Array
                        (
                            [0] => Array
                                (
                                    [plugin] => vm_openvz_plugin
                                    [function] => openvz_vm_delete
                                    [module] => 
                                )

                        )

                )

            [module] => Array
                (
                    [name] => zabbix
                    [title] => Zabbix
                    [template] => module.tpl.htm
                    [startpage] => zabbix/monitor_list.php
                    [tab_width] => 
                    [nav] => Array
                        (
                            [0] => Array
                                (
                                    [title] => Monitor
                                    [open] => 1
                                    [items] => Array
                                        (
                                            [0] => Array
                                                (
                                                    [title] => Monitors
                                                    [target] => content
                                                    [link] => zabbix/monitor_list.php
                                                    [html_id] => zabbix_monitor_list
                                                )

                                            [1] => Array
                                                (
                                                    [title] => Trends
                                                    [target] => content
                                                    [link] => zabbix/monitor_graph.php
                                                    [html_id] => zabbix_monitor_graph
                                                )

                                            [2] => Array
                                                (
                                                    [title] => Alerts
                                                    [target] => content
                                                    [link] => zabbix/monitor_alert.php
                                                    [html_id] => zabbix_monitor_alert
                                                )

                                        )

                                )

                            [1] => Array
                                (
                                    [title] => Parameters
                                    [open] => 1
                                    [items] => Array
                                        (
                                            [0] => Array
                                                (
                                                    [title] => Client Parameters
                                                    [target] => content
                                                    [link] => zabbix/parameters_client_list.php
                                                    [html_id] => client_list
                                                )

                                        )

                                )

                        )

                )

            [id] => c4f2fc56751a444b390ba385286d9fd9
            [form] => Array
                (
                    [return_to] => 
                    [tab] => monitor
                )

        )

    [s] => Array
        (
            [user] => Array
                (
                    [userid] => 1
                    [sys_userid] => 1
                    [sys_groupid] => 0
                    [sys_perm_user] => riud
                    [sys_perm_group] => riud
                    [sys_perm_other] => 
                    [username] => ************
                    [passwort] => ******************
                    [modules] => zabbix,registrar,tools,help,mail,vm,admin,dns,monitor,dashboard,sites,client
                    [startmodule] => dashboard
                    [app_theme] => default
                    [typ] => admin
                    [active] => 1
                    [language] => fr
                    [groups] => 
                    [default_group] => 1
                    [client_id] => 0
                    [id_rsa] => 
                    [ssh_rsa] => 
                    [lost_password_function] => 1
                    [lost_password_hash] => 
                    [lost_password_reqtime] => 
                    [theme] => default
                )

            [language] => fr
            [theme] => default
            [plugin_cache] => Array
                (
                    [client:client:on_after_insert] => Array
                        (
                            [0] => Array
                                (
                                    [plugin] => clients_template_plugin
                                    [function] => apply_client_templates
                                    [module] => 
                                )

                        )

                    [client:client:on_after_update] => Array
                        (
                            [0] => Array
                                (
                                    [plugin] => clients_template_plugin
                                    [function] => apply_client_templates
                                    [module] => 
                                )

                        )

                    [client:reseller:on_after_insert] => Array
                        (
                            [0] => Array
                                (
                                    [plugin] => clients_template_plugin
                                    [function] => apply_client_templates
                                    [module] => 
                                )

                        )

                    [client:reseller:on_after_update] => Array
                        (
                            [0] => Array
                                (
                                    [plugin] => clients_template_plugin
                                    [function] => apply_client_templates
                                    [module] => 
                                )

                        )

                    [dns:dns_slave:on_after_insert] => Array
                        (
                            [0] => Array
                                (
                                    [plugin] => dns_dns_slave_plugin
                                    [function] => dns_dns_slave_edit
                                    [module] => 
                                )

                        )

                    [dns:dns_slave:on_after_update] => Array
                        (
                            [0] => Array
                                (
                                    [plugin] => dns_dns_slave_plugin
                                    [function] => dns_dns_slave_edit
                                    [module] => 
                                )

                        )

                    [dns:dns_soa:on_after_insert] => Array
                        (
                            [0] => Array
                                (
                                    [plugin] => dns_dns_soa_plugin
                                    [function] => dns_dns_soa_edit
                                    [module] => 
                                )

                        )

                    [dns:dns_soa:on_after_update] => Array
                        (
                            [0] => Array
                                (
                                    [plugin] => dns_dns_soa_plugin
                                    [function] => dns_dns_soa_edit
                                    [module] => 
                                )

                        )

                    [mail:mail_domain:on_after_insert] => Array
                        (
                            [0] => Array
                                (
                                    [plugin] => mail_mail_domain_plugin
                                    [function] => mail_mail_domain_edit
                                    [module] => 
                                )

                        )

                    [mail:mail_domain:on_after_update] => Array
                        (
                            [0] => Array
                                (
                                    [plugin] => mail_mail_domain_plugin
                                    [function] => mail_mail_domain_edit
                                    [module] => 
                                )

                        )

                    [mail:mail_user_filter:on_after_insert] => Array
                        (
                            [0] => Array
                                (
                                    [plugin] => mail_user_filter_plugin
                                    [function] => mail_user_filter_edit
                                    [module] => 
                                )

                        )

                    [mail:mail_user_filter:on_after_update] => Array
                        (
                            [0] => Array
                                (
                                    [plugin] => mail_user_filter_plugin
                                    [function] => mail_user_filter_edit
                                    [module] => 
                                )

                        )

                    [mail:mail_user_filter:on_after_delete] => Array
                        (
                            [0] => Array
                                (
                                    [plugin] => mail_user_filter_plugin
                                    [function] => mail_user_filter_del
                                    [module] => 
                                )

                        )

                    [mailuser:mail_user_filter:on_after_insert] => Array
                        (
                            [0] => Array
                                (
                                    [plugin] => mail_user_filter_plugin
                                    [function] => mail_user_filter_edit
                                    [module] => 
                                )

                        )

                    [mailuser:mail_user_filter:on_after_update] => Array
                        (
                            [0] => Array
                                (
                                    [plugin] => mail_user_filter_plugin
                                    [function] => mail_user_filter_edit
                                    [module] => 
                                )

                        )

                    [mailuser:mail_user_filter:on_after_delete] => Array
                        (
                            [0] => Array
                                (
                                    [plugin] => mail_user_filter_plugin
                                    [function] => mail_user_filter_del
                                    [module] => 
                                )

                        )

                    [sites:web_database_user:on_after_update] => Array
                        (
                            [0] => Array
                                (
                                    [plugin] => sites_web_database_user_plugin
                                    [function] => sites_web_database_user_edit
                                    [module] => 
                                )

                        )

                    [sites:web_database_user:on_after_insert] => Array
                        (
                            [0] => Array
                                (
                                    [plugin] => sites_web_database_user_plugin
                                    [function] => sites_web_database_user_edit
                                    [module] => 
                                )

                        )

                    [sites:web_vhost_domain:on_after_insert] => Array
                        (
                            [0] => Array
                                (
                                    [plugin] => sites_web_vhost_domain_plugin
                                    [function] => sites_web_vhost_domain_edit
                                    [module] => 
                                )

                        )

                    [sites:web_vhost_domain:on_after_update] => Array
                        (
                            [0] => Array
                                (
                                    [plugin] => sites_web_vhost_domain_plugin
                                    [function] => sites_web_vhost_domain_edit
                                    [module] => 
                                )

                        )

                    [vm:openvz_vm:on_after_insert] => Array
                        (
                            [0] => Array
                                (
                                    [plugin] => vm_openvz_plugin
                                    [function] => openvz_vm_insert
                                    [module] => 
                                )

                        )

                    [vm:openvz_vm:on_after_update] => Array
                        (
                            [0] => Array
                                (
                                    [plugin] => vm_openvz_plugin
                                    [function] => openvz_vm_update
                                    [module] => 
                                )

                        )

                    [vm:openvz_vm:on_after_delete] => Array
                        (
                            [0] => Array
                                (
                                    [plugin] => vm_openvz_plugin
                                    [function] => openvz_vm_delete
                                    [module] => 
                                )

                        )

                )

            [module] => Array
                (
                    [name] => zabbix
                    [title] => Zabbix
                    [template] => module.tpl.htm
                    [startpage] => zabbix/monitor_list.php
                    [tab_width] => 
                    [nav] => Array
                        (
                            [0] => Array
                                (
                                    [title] => Monitor
                                    [open] => 1
                                    [items] => Array
                                        (
                                            [0] => Array
                                                (
                                                    [title] => Monitors
                                                    [target] => content
                                                    [link] => zabbix/monitor_list.php
                                                    [html_id] => zabbix_monitor_list
                                                )

                                            [1] => Array
                                                (
                                                    [title] => Trends
                                                    [target] => content
                                                    [link] => zabbix/monitor_graph.php
                                                    [html_id] => zabbix_monitor_graph
                                                )

                                            [2] => Array
                                                (
                                                    [title] => Alerts
                                                    [target] => content
                                                    [link] => zabbix/monitor_alert.php
                                                    [html_id] => zabbix_monitor_alert
                                                )

                                        )

                                )

                            [1] => Array
                                (
                                    [title] => Parameters
                                    [open] => 1
                                    [items] => Array
                                        (
                                            [0] => Array
                                                (
                                                    [title] => Admin Parameters
                                                    [target] => content
                                                    [link] => zabbix/parameters_admin_edit.php?id=1
                                                    [html_id] => parameters_admin
                                                )

                                            [1] => Array
                                                (
                                                    [title] => Reseler Parameters
                                                    [target] => content
                                                    [link] => zabbix/parameters_reseller_list.php
                                                    [html_id] => reseller_list
                                                )

                                            [2] => Array
                                                (
                                                    [title] => Client Parameters
                                                    [target] => content
                                                    [link] => zabbix/parameters_client_list.php
                                                    [html_id] => client_list
                                                )

                                        )

                                )

                        )

                )

            [id] => c4f2fc56751a444b390ba385286d9fd9
            [new_ispconfig_version] => 3.2.1
            [form] => Array
                (
                    [return_to] => 
                    [tab] => parameters
                )

        )

    [search] => Array
        (
            [zabbixmonitorzabbix_monitor] => Array
                (
                    [order] => 
                )

            [limit] => 15
            [monitor] => Array
                (
                    [page] => 0
                )

            [zabbixresellersclient] => Array
                (
                    [order] => 
                )

            [resellers] => Array
                (
                    [page] => 0
                )

            [zabbixclientsclient] => Array
                (
                    [order] => 
                )

            [clients] => Array
                (
                    [page] => 0
                )

            [adminserverserver] => Array
                (
                    [order] => 
                )

            [server] => Array
                (
                    [page] => 0
                )

            [adminuserssys_user] => Array
                (
                    [order] => sys_user.client_id
                    [order_in_php] => 
                )

            [users] => Array
                (
                    [page] => 0
                    [search_client_id] => 
                    [search_active] => 
                    [search_username] => 
                )

        )

    [monitor] => Array
        (
            [server_id] => 4
            [server_name] => ******
        )

    [_csrf] => Array
        (
            [zabbix_admin_5ff0822eae8a6] => 3631ea16738a1798ded76f9b9f79a6104040ef45
            [zabbix_admin_5ff0897a04a67] => b1c6532c9ea5db34091063a2568c9bbec8280566
            [zabbix_admin_5ff08af10cf5d] => 24158b6789b57ac68d51adb765ef0cdd1c692bf9
            [resellers_5ff12468cbc57] => b00e3cb70960896f48e5c5c2fb5341d7d843fcd8
            [resellers_5ff1248aa386d] => 8d603807490e389e48180946525b6a0f2711f4f4
            [zabbix_admin_5ff1248c93d9f] => c112bcae37825737529feac2201db380e7f532e7
        )

    [_csrf_timeout] => Array
        (
            [zabbix_admin_5ff0822eae8a6] => 1609601086
            [zabbix_admin_5ff0897a04a67] => 1609602954
            [zabbix_admin_5ff08af10cf5d] => 1609603329
            [resellers_5ff12468cbc57] => 1609642616
            [resellers_5ff1248aa386d] => 1609642650
            [zabbix_admin_5ff1248c93d9f] => 1609642652
        )

    [zabbix] => Array
        (
            [parameters] => Array
                (
                    [client_id] => 37
                )

        )

)
```