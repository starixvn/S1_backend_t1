local _M = {
    redis = require "resty.redis",
    prefix_access = "ipblocklist:access",
    prefix_block = "ipblocklist:block",
    findtime = 60,
    maxaccess = 20,
    redis_pool_size = 15,
    redis_keepalive = 1000,
}
_M.redis_connect = function ()
    local red = _M.redis:new()
    red:set_timeout(500)
    red:set_keepalive(_M.redis_keepalive, _M.redis_pool_size)
    local ok, err = red:connect("proxyredis", 6379)
    if not ok then
        ngx.say("failed to connect: ", err)
        return
    end
    return red
end
_M.deny_message = function(limited_seconds, ip)
    return [[<!DOCTYPE html>
<html>
<head>
	<title></title>
	<link href="https://fonts.googleapis.com/css?family=Alfa+Slab+One|Ek+Mukta" rel="stylesheet">
	<style type="text/css">
		.container{
			width: 1000px;
			margin: 0 auto;
		}

		h1.title{
			color: #999999;
			font-family: 'Alfa Slab One', cursive;
			font-size: 20rem;
			text-align: center;
			margin: 20% 0 0 0;
			height: 320px;
		}

		h1.title span{
			background: #999;
			width: 270px;
			height: 270px;
			display: inline-block;
			border-radius: 100%;
		}

		p.text{
			color: #999999;
			font-family: 'Ek Mukta', sans-serif;
			font-size: 5rem;
			text-align: center;
			text-transform: uppercase;
			letter-spacing: 3px;
			font-weight: 600;
			margin: 0; 
		}

		.container a.back{
			font-family: 'Ek Mukta', sans-serif;
			background: #e00000;
			padding: 10px 20px;
			border-radius: 5px;
			width: 20%;
			margin: 0 auto;
			color: #fff;
			display: block;
			text-align: center;
			font-size: 1rem;
			text-transform: uppercase;
			text-decoration: none;
		}

		.container a.back:hover{
			background: #860000;
		}
		p.footer{
			color: #999;
			font-size: 1rem;
			position: absolute;
			bottom: 0;
			font-family: 'Ek Mukta', sans-serif;
			text-align: center;
			width: 100%;
			left: 0;
			text-transform: uppercase;
		}		
	</style>
</head>
<body>

<div class="container">
<h1 class="title">999</h1>
<p class="text">Sorry, this IP ]] .. ip ..[[ is blocked .  please try again after ]]..limited_seconds..[[ seconds!</p>
<p class="footer">Powered By Tripleone Tech</p>
</div>

</body>
</html>]]
end
_M.check_blocklist = function (ip)
    local red = _M.redis_connect()
    local block_key = _M.prefix_block..":"..ip
    local res, err = red:ttl(block_key)
    if res >= 0 then
--    if true then
        ngx.status = 999
        ngx.header['Content-Type'] = 'text/html'
        ngx.say(_M.deny_message(res, ip))
        ngx.exit(ngx.OK)
    end
end

_M.check_accesslist_and_ban = function (ip, block_group, maxaccess, bantime)
    local red = _M.redis_connect()
    local key = _M.prefix_access..":"..block_group..":"..ip..":*"
    local keys, err = red:keys(key)
    local total = 0
    for table_k, table_v  in pairs(keys) do
        local v, err = red:get(table_v)
        total = total + v
    end
    if total >= maxaccess then
        ngx.log(ngx.ERR, block_group .. ' access to mouch times: ' .. maxaccess)
        _M.add_to_blocklist(ip, bantime)
    end
    return ans
end

_M.add_to_blocklist = function (ip, bantime)
    local red = _M.redis_connect()
    local block_key = _M.prefix_block..":"..ip
    local ans, err = red:set(block_key, os.time())
    local ans, err = red:expire(block_key, bantime)
    ngx.log(ngx.ERR, 'Forbid ' .. ip .. ' ' .. bantime .. ' s')
end

_M.add_to_accesslist = function (ip, block_group, expiretime)
    local red = _M.redis_connect()
    local key = _M.prefix_access..":"..block_group..":"..ip..":"..os.time()
    local ans, err = red:incr(key)
    if not ans then
        ngx.say('failed to run set: ', err)
        return
    end
    local ans, err = red:expire(key, expiretime)
    if not ans then
        ngx.say('failed to run expire: ', err)
        return
    end
    
    return
end
return _M
