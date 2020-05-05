export OG_BASEPATH=/home/vagrant/Code/og/admin

echo '{"queue_secret": "c659_7946-DE52cafe206d038084627d453a"}' | gearman -f halt_job
