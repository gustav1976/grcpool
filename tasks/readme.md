Example in /etc/cron.daily

for entry in /var/www/www.grcpool.com/tasks/_daily/*
do
        echo $entry
        echo "`date +%Y.%m.%d.%H.%M.%S` ##################### START" >> /var/log/grcpool/${entry##*/}.log
        {
                php "$entry"
                echo $'\r'
        } >> /var/log/grcpool/${entry##*/}.log
        echo "`date +%Y.%m.%d.%H.%M.%S` ##################### END" >> /var/log/grcpool/${entry##*/}.log
done
