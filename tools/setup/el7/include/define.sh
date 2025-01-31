# declare binaries path
declare -r awk="/usr/bin/awk"
declare -r basename="/usr/bin/basename"
declare -r cat="/usr/bin/cat"
declare -r chmod="/usr/bin/chmod"
declare -r chown="/usr/bin/chown"
declare -r cp="/usr/bin/cp"
declare -r date="/usr/bin/date"
declare -r getenforce="/usr/sbin/getenforce"
declare -r getopt="/usr/bin/getopt"
declare -r grep="/usr/bin/grep"
declare -r gzip="/usr/bin/gzip"
declare -r head="/usr/bin/head"
declare -r install="/usr/bin/install"
declare -r ln="/usr/bin/ln"
declare -r ls="/usr/bin/ls"
declare -r mkdir="/bin/mkdir"
declare -r mv="/usr/bin/mv"
if [ -f "/opt/rh/rh-mysql80/root/usr/bin/mysql" ]; then
    declare -r mysql="/opt/rh/rh-mysql80/root/usr/bin/mysql"
else
    declare -r mysql="/usr/bin/mysql"
fi
if [ -f "/opt/rh/rh-mysql80/root/usr/bin/mysqladmin" ]; then
    declare -r mysqladmin="/opt/rh/rh-mysql80/root/usr/bin/mysqladmin"
else
    declare -r mysqladmin="/usr/bin/mysqladmin"
fi
if [ -f "/opt/rh/rh-mysql80/root/usr/bin/mysqldump" ]; then
    declare -r mysqldump="/opt/rh/rh-mysql80/root/usr/bin/mysqldump"
else
    declare -r mysqldump="/usr/bin/mysqldump"
fi
declare -r php_launcher="/usr/share/tuleap/src/utils/php-launcher.sh"
declare -r printf="/usr/bin/printf"
declare -r rm="/usr/bin/rm"
declare -r sed="/usr/bin/sed"
declare -r su="/bin/su"
declare -r systemctl="/usr/bin/systemctl"
declare -r touch="/bin/touch"
declare -r tr="/usr/bin/tr"
declare -r tuleapcfg="/usr/bin/tuleap-cfg"

declare -a cmd=("${awk}" "${basename}" "${cat}" "${chmod}" "${chown}"
                "${cp}" "${date}" "${getenforce}" "${getopt}" "${grep}"
                "${gzip}" "${head}" "${install}" "${ln}" "${mkdir}" "${mv}"
                "${mysql}" "${mysqladmin}" "${mysqldump}" "${printf}"
                "${rm}" "${sed}" "${su}" "${systemctl}" "${touch}" "${tr}")

# declare files path
declare -r group_file="/etc/group"
declare -r install_dir="/usr/share/tuleap"
declare -r password_file="/root/.tuleap_passwd"
declare -r rh_release="/etc/redhat-release"
declare -r script_name="$(${basename} ${0})"
declare -r sefile="/etc/selinux/config"
declare -r src_db_mysql="${install_dir}/src/db/mysql"
declare -r tuleap_lib_bin="/usr/lib/tuleap/bin"
declare -r tuleap_src_plugins="/usr/share/tuleap/plugins"
declare -r tuleap_data="/var/lib/tuleap"
declare -r tuleap_dir="/etc/tuleap"
declare -r tuleap_conf="${tuleap_dir}/conf"
declare -r tuleap_plugins="${tuleap_dir}/plugins"
declare -r pluginsadministration="${tuleap_plugins}/pluginsadministration"
declare -r local_inc="local.inc"
declare -r database_inc="database.inc"
declare -r tuleap_dump="/root/.tuleap_dump"
declare -r tuleap_log="/var/log/tuleap/tuleap_setup.log"
declare -r tuleap_src="${install_dir}/src"
declare -r urandom="/dev/urandom"
declare -r nginx_conf="/etc/nginx/conf.d/tuleap.conf"
declare -r httpd_conf="/etc/httpd/conf/httpd.conf"
declare -r httpd_conf_ssl="/etc/httpd/conf.d/ssl.conf"

# declare options
declare -r sys_db_name="tuleap"
declare -r my_opt="--batch --skip-column-names"
declare -r project_admin="admin"
declare -r tuleap_unix_user="codendiadm"
declare -i mysql_port=3306

declare -a timers=("tuleap-process-system-events-default.timer"
                   "tuleap-process-system-events-statistics.timer"
                   "tuleap-process-system-events-tv3-tv5-migration.timer"
                   "tuleap-launch-system-check.timer"
                   "tuleap-launch-daily-event.timer"
                   "tuleap-launch-plugin-job.timer")

assumeyes="false"
db_exist="false"
long_org_name="Tuleap"
mysql_user="root"
new_db="true"
org_name="Tuleap"
server_name="NULL"
