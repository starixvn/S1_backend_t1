smartbackend js lib
=========

events 事件
------

### `callback_after_build_login_form`


will call this function after build login form

这个函数将在登录窗口创建完后调用

#### parameters 参数

`container` login form jQuery object 登录窗口的jquery 对象
`$` it's jQuery 是jQuery本身

### `callback_after_build_logged_form`

will call this function after build logged form

这个函数将在已登录的玩家信息窗口创建完后调用

#### parameters 参数

`container` logged form jQuery object 已登录玩家信息窗口的jquery 对象
`$` it's jQuery 是jQuery本身

class 主类
-----

### `_export_smartbackend`

* `$` it's jQuery 是jQuery本身

* `variables` variables 各种状态
	* `VIP_group` vip name 玩家所属vip组名
	* `playerUsername` player's username 玩家的用户名
	* `logged` logged status 登录状态 
