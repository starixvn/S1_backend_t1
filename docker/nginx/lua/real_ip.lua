local ngx_re = require "ngx.re"
local headers = {
    ngx.var.http_ss_client_addr,
    ngx.var.http_true_client_ip,
    ngx.var.http_client_ip,
    ngx.var.http_x_client_ip,
    ngx.var.http_x_forwarded_for,
    ngx.var.http_x_cluster_client_ip
};
local function is_valid_ip(ip)
    if ip == '127.0.0.1' then
        return false
    end
    return true
end
get_real_ip = function ()
    local ip = ngx.var.remote_addr
    for _, header in ipairs(headers) do
        local res, err = ngx_re.split(header, "(,)")
        if is_valid_ip(res[1]) then
            ip = res[1]
            break
        else
            if is_valid_ip(res[2]) then
                ip = res[2]
                break
            end
        end
    end
    return ip
end
return get_real_ip()
