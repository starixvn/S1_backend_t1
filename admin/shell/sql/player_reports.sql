SELECT DISTINCT player.playerId playerId, player.username username,
CONCAT(playerdetails.firstName, ' ', playerdetails.lastName) real_name,
CONCAT(player.groupName, ' - ', player.levelName ) member_level, player.email email,
playerdetails.registrationIP registrationIP, player.lastLoginIp lastLoginIp,
player.lastLoginTime lastLoginTime, player.lastLogoutTime lastLogoutTime,
player.createdOn createdOn, playerdetails.gender gender,
SUM(CASE WHEN transactions.transaction_type = 14 THEN transactions.amount ELSE 0 END) deposit_bonus,
SUM(CASE WHEN transactions.transaction_type = 13 THEN transactions.amount ELSE 0 END) cashback_bonus,
SUM(CASE WHEN transactions.transaction_type = 15 THEN transactions.amount ELSE 0 END) referral_bonus,
SUM(CASE WHEN transactions.transaction_type = 9 THEN transactions.amount ELSE 0 END) manual_bonus,
SUM(CASE WHEN transactions.transaction_type IN (14, 13, 15, 9) THEN transactions.amount ELSE 0 END) total_bonus,
SUM(CASE WHEN transactions.transaction_type = 1 THEN transactions.amount ELSE 0 END) total_deposit,
SUM(CASE WHEN transactions.transaction_type = 2 THEN transactions.amount ELSE 0 END) total_withdrawal

FROM (`player`)

LEFT JOIN `playerdetails` ON playerdetails.playerId = player.playerId
LEFT JOIN `transactions` ON transactions.to_id = player.playerId
WHERE `transactions`.`to_type` = 2 AND transactions.status = 1 AND transactions.created_at BETWEEN '2015-12-01 00:00:00' AND '2015-12-31 23:59:59'
GROUP BY `player`.`playerId`
ORDER BY total_deposit desc
