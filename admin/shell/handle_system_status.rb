#!/usr/bin/env ruby
if Process.uid != 0
  puts "Please use superuser run this."
  exit
end

time_wait_count = `netstat -antup 2>/dev/null | grep TIME_WAIT | grep :3306 | wc -l`
puts time_wait_count
if time_wait_count.to_i > 1000
    `service php7.2-fpm restart`
end

require "net/https"
require "uri"

host_domain_mapping = {"3tbet" => "3tbet.com", "lsbet" => "lesbet.com", "slotworldsapp" => "slotworlds.com", "jbyl777" => "jbyl777.com"}
hostname =  `hostname`.strip
domain = host_domain_mapping[hostname]
uri = URI.parse("http://admin.#{domain}/auth/login")
http = Net::HTTP.new(uri.host, uri.port)
#http.use_ssl = true

request = Net::HTTP::Get.new(uri.request_uri)
res = http.request(request)

if res.code == "502"
    `service php7.2-fpm restart`
end
