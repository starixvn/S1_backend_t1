/*==========Game Logs==========*/
/*========= Slot ===========*/
UPDATE
  `game_logs`
SET
  `game_type_id` = 63
WHERE `game_type_id` IN (583,584,586,589,592)
  AND `game_platform_id` = 20 ;

#========= Card Game ===========
UPDATE
  `game_logs`
SET
  `game_type_id` = 65
WHERE `game_type_id` IN (582,578)
  AND `game_platform_id` = 20 ;

#========= Arcade ===========
-- UPDATE
--   game_logs
-- SET
--   game_type_id =
-- WHERE game_type_id IN (585,593)
--   AND game_platform_id = 20 ;

#========= Table Game ===========
UPDATE
  `game_logs`
SET
  `game_type_id` = 64
WHERE `game_type_id` IN (587,588)
  AND `game_platform_id` = 20 ;


/*==========Game Description==========*/
/*========= Slot ===========*/
UPDATE
  `game_description`
SET
  `game_type_id` = 63
WHERE `game_type_id` IN (583,584,586,589,592)
  AND `game_platform_id` = 20 ;

#========= Card Game ===========
UPDATE
  `game_description`
SET
  `game_type_id` = 65
WHERE `game_type_id` IN (582,578)
  AND `game_platform_id` = 20 ;

#========= Arcade ===========
-- UPDATE
--   game_description
-- SET
--   game_type_id =
-- WHERE game_type_id IN (585,593)
--   AND game_platform_id = 20 ;

#========= Table Game ===========
UPDATE
  `game_description`
SET
  `game_type_id` = 64
WHERE `game_type_id` IN (587,588)
  AND `game_platform_id` = 20 ;


#============ Update 5/11/2017 ===================

#Game => Gold 999.9
UPDATE
  game_logs
SET
  game_description_id = 3061,
  game_type_id = 63
WHERE game_description_id = 9922 ;

#Game => Dice
UPDATE
  game_logs
SET
  game_description_id = 9776,
  game_type_id = 64
WHERE game_description_id = 9923 ;

#Game => Baccarat
UPDATE
  game_logs
SET
  game_description_id = 9846,
  game_type_id = 65
WHERE game_description_id = 9924 ;

#Game => Dwarven Gold
UPDATE
  game_logs
SET
  game_description_id = 9880,
  game_type_id = 63
WHERE game_description_id = 9925 ;

#Game => Casino War
UPDATE
  game_logs
SET
  game_description_id = 9914,
  game_type_id = 64
WHERE game_description_id = 9926 ;

