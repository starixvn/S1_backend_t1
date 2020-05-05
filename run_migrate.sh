kubectl get pods | grep -v "\-sync\-" | grep $CLIENT_NAME-og- | tail -n 1 | awk '{print "kubectl exec -it "$1" -- su - vagrant -c \"cd /home/vagrant/Code/og; ./migrate.sh \""}'  | sh
