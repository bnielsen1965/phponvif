# PHP/ONVIF

A PHP web application used to manage ONVIF capable IP cameras.








---------------------------------


# INSTALLATION


## Install with composer



## Install manually

---------------------------------


# TROUBLESHOOTING

## SELinux

The application must be able to send out udp packets for the discovery
process and it must be capable of connecting to devices all as the
user that is running the PHP application.

On RedHat based distributions with selinux enabled the policies must
be modified to allow these network connections.

To allow the httpd process to make network connections, as root:

> setsebool -P httpd_can_network_connect on

To allow the udp discovery packets, as root:

semanage port -a -t http_port_t -p udp 3702



# DEVELOPING


