local _M = require("acl")
local ip = ngx.var.remote_addr
_M.add_to_accesslist(ip, 'group1')
_M.check_accesslist(ip, 'group1')
