/*update promo category on transactions*/

update transactions set promo_category=(select promoCategory
  from promorules join playerpromo on playerpromo.promorulesId=promorules.promorulesId
  where playerpromo.playerpromoId=transactions.player_promo_id)
where promo_category is null and player_promo_id is not null;

