local _M = require("acl")
local ip = ngx.var.remote_addr
_M.check_blocklist(ip)
