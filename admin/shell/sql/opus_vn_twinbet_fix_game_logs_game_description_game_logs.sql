/*==========Game Logs==========*/
#========= Card Game ===========
UPDATE 
  `game_logs`
SET
  `game_type_id` = 65 
WHERE `game_type_id` IN (578)
  AND `game_platform_id` = 20 ;

#========= Table Game ===========
UPDATE 
  `game_logs`
SET
  `game_type_id` = 64
WHERE `game_type_id` IN (579)
  AND `game_platform_id` = 20 ;


/*==========Game Description==========*/
#========= Card Game ===========
UPDATE 
  `game_description`
SET
  `game_type_id` = 65 
WHERE `game_type_id` IN (578)
  AND `game_platform_id` = 20 ;

#========= Table Game ===========
UPDATE 
  `game_description`
SET
  `game_type_id` = 64
WHERE `game_type_id` IN (579)
  AND `game_platform_id` = 20 ;