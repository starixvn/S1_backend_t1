update gspt_game_logs set gameshortcode=REPLACE(SUBSTRING_INDEX(game_name, '(', -1), ')', '')
