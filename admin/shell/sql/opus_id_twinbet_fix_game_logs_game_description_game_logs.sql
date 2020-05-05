/*==========Game Logs==========*/
/*========= Slot ===========*/
UPDATE 
  `game_logs`
SET
  `game_type_id` = 63 
WHERE `game_type_id` IN (579,584,585) 
  AND `game_platform_id` = 20 ;
  
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
WHERE `game_type_id` IN (588)
  AND `game_platform_id` = 20 ;


/*==========Game Description==========*/
/*========= Slot ===========*/
UPDATE 
  `game_description`
SET
  `game_type_id` = 63 
WHERE `game_type_id` IN (579,584,585) 
  AND `game_platform_id` = 20 ;
  
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
WHERE `game_type_id` IN (588)
  AND `game_platform_id` = 20 ;