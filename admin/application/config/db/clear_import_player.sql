-- clear import player

drop table if exists tmp_import_player;

create table tmp_import_player select playerId from player where registered_by='importer';

create index idx_playerid on tmp_import_player(playerId);

-- clear playerbankdetails
delete from playerbankdetails where playerId in (select playerId from tmp_import_player);

-- clear game_provider_auth
delete from game_provider_auth where player_id in (select playerId from tmp_import_player);

-- clear playertag
delete from playertag where playerId in (select playerId from tmp_import_player);

-- clear playerdetails
delete from playerdetails where playerId in (select playerId from tmp_import_player);

-- clear sale_orders
delete from sale_orders where player_id in (select playerId from tmp_import_player);

-- clear walletaccount
delete from walletaccount where playerId in (select playerId from tmp_import_player);

-- clear player
delete from player where playerId in (select playerId from tmp_import_player);

-- clear tmp_import_hxh
delete from tmp_import_hxh;
