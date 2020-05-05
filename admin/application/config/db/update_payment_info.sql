update external_system set live_url='https://pay.ips.com.cn/ipayment.aspx',
sandbox_url='http://pay.ips.net.cn/ipayment.aspx',
live_key='',
live_secret='',
sandbox_key='000015',
sandbox_secret='GDgLwwdK270Qj1w4xho8lyTpRQZV9Jm5x4NwWOTThUa4fMhEBK9jOXFrKRT6xhlJuU2FEa89ov0ryyjfJuuPkcGzO5CeVx5ZIrkkt1aBlZV36ySvHOMcNv8rncRiy3DQ',
live_mode=0
where id=4;

update external_system set
second_url=''
where id=4;

/*BOFO*/
update external_system
set live_url='https://gw.baofoo.com/payindex',
sandbox_url='https://tgw.baofoo.com/payindex',
live_account='',
live_key='',
live_secret='',
sandbox_account='100000178',
sandbox_key='10000001',
sandbox_secret='abcdefg',
live_mode=0
where id=9;

update external_system_list
set live_url='https://gw.baofoo.com/payindex',
sandbox_url='https://tgw.baofoo.com/payindex',
live_account='',
live_key='',
live_secret='',
sandbox_account='100000178',
sandbox_key='10000001',
sandbox_secret='abcdefg',
live_mode=0
where id=9;

/*GOPAY*/
update external_system
set live_url='https://gateway.gopay.com.cn/Trans/WebClientAction.do',
sandbox_url='https://mertest.gopay.com.cn/PGServer/Trans/WebClientAction.do',
live_account='',
live_key='',
live_secret='',
sandbox_account='100000178',
sandbox_key='10000001',
sandbox_secret='abcdefg',
live_mode=0
where id=5;

update external_system_list
set live_url='https://gateway.gopay.com.cn/Trans/WebClientAction.do',
sandbox_url='https://mertest.gopay.com.cn/PGServer/Trans/WebClientAction.do',
live_account='',
live_key='',
live_secret='',
sandbox_account='100000178',
sandbox_key='10000001',
sandbox_secret='abcdefg',
live_mode=0
where id=5;

/* LEFU */
update external_system
set live_url='https://pay.lefu8.com/gateway/trade.htm',
sandbox_url='https://qa.lefu8.com/gateway/trade.htm',
live_account='',
live_key='',
live_secret='',
sandbox_account='',
sandbox_key='8611146479',
sandbox_secret='23b052c76f2d45428e5db872bf7a12c9',
live_mode=0
where id=18;

update external_system_list
set live_url='https://pay.lefu8.com/gateway/trade.htm',
sandbox_url='https://qa.lefu8.com/gateway/trade.htm',
live_account='',
live_key='',
live_secret='',
sandbox_account='',
sandbox_key='8611146479',
sandbox_secret='23b052c76f2d45428e5db872bf7a12c9',
live_mode=0
where id=18;
