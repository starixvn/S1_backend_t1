SELECT DISTINCT
transactions.created_at created_at,
(CASE transactions.transaction_type WHEN 1 THEN '存款'
 WHEN 2 THEN '取款'
 WHEN 5 THEN '主钱包转到子钱包'
 WHEN 6 THEN '子钱包转到主钱包'
 WHEN 7 THEN '人工加余额'
 WHEN 8 THEN '人工减余额'
 WHEN 9 THEN '加奖金'
 WHEN 10 THEN '减奖金'
 WHEN 11 THEN '人工加子钱包余额'
 WHEN 12 THEN '人工减子钱包余额'
 WHEN 13 THEN '返水'
 WHEN 14 THEN 'VIP组奖金'
 WHEN 15 THEN '玩家推荐奖金'
 WHEN 16 THEN '代理收入'
 ELSE '' END) transaction_type,
(CASE transactions.from_type WHEN 1 THEN fromUser.username WHEN 2 THEN fromPlayer.username WHEN 3 THEN fromAffiliate.username ELSE NULL END) from_username,
(CASE transactions.to_type WHEN 1 THEN toUser.username WHEN 2 THEN toPlayer.username WHEN 3 THEN toAffiliate.username ELSE NULL END) to_username,
transactions.amount amount,
transactions.before_balance before_balance, transactions.after_balance after_balance,
external_system.system_code subwallet, promotype.promoTypeName promoTypeName,
transactions.total_before_balance total_before_balance, transactions.flag flag,
transactions.external_transaction_id external_transaction_id, transactions.note note
FROM (`transactions`)
LEFT JOIN `adminusers` fromUser ON transactions.from_type = 1 AND fromUser.userId = transactions.from_id
LEFT JOIN `player` fromPlayer ON transactions.from_type = 2 AND fromPlayer.playerId = transactions.from_id
LEFT JOIN `affiliates` fromAffiliate ON transactions.from_type = 3 AND fromAffiliate.affiliateId = transactions.from_id
LEFT JOIN `adminusers` toUser ON transactions.to_type = 1 AND toUser.userId = transactions.to_id
LEFT JOIN `player` toPlayer ON transactions.to_type = 2 AND toPlayer.playerId = transactions.to_id
LEFT JOIN `affiliates` toAffiliate ON transactions.to_type = 3 AND toAffiliate.affiliateId = transactions.to_id
LEFT JOIN `playeraccount` ON playeraccount.playerAccountId = transactions.sub_wallet_id
LEFT JOIN `external_system` ON external_system.id = playeraccount.typeId AND playeraccount.type = 'subwallet'
LEFT JOIN `promotype` ON promotype.promotypeId = transactions.promo_category
LEFT JOIN `sale_orders` ON sale_orders.transaction_id = transactions.id
WHERE `transactions`.`created_at` BETWEEN '2015-12-01 00:00:00' AND '2015-12-31 23:59:59' AND transactions.transaction_type IN ('1','2','3','4','5','6','7','8','9','10','11','12','13','14','15')
ORDER BY `created_at` DESC
