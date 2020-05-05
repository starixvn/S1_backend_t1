
mysql -u og_slave -p -h dw777dbslave.cqg0h98ul6vy.ap-northeast-1.rds.amazonaws.com -e "show processlist" | grep SELECT | grep "game_logs.id" | awk '{print "mysql -u og_slave -ppdbKQt8rGR -h dw777dbslave.cqg0h98ul6vy.ap-northeast-1.rds.amazonaws.com -e \"kill "$1"\""}'  | sh

