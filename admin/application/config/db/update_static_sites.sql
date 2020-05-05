update static_sites set company_title='{"english":"GamingSoft", "chinese":"GamingSoft"}' where site_name='default';

update static_sites set logo_icon_filepath='http://www.staging.rtt365.net/img/GamingSoft-logo.png' where site_name='default';


<form id='<%- formId %>' action='<%- ui.loginUrl %>' method='POST' target='<%- ui.loginIframeName %>'>
<ul>
<li>
    <b style="width: 50px; color: white;">TTT</b>
    <input type="text" name="login" class="type_input J-verify" placeholder="<%- langText.form_field_username %>" style="width: 143px;" />
    <br />
    <input type="password" name="password" class="type_input J-verify" placeholder="<%- langText.form_field_password %>" style="width: 143px; margin-left: 31px;"/>
</li>
<li>
    <% if ( ui.captchaFlag ){ %>
    <input type="text" name="captcha" class="_captcha_input code_input" placeholder="验证码" />
    <!-- <img src="images/code_yan.gif" class="yz_code" /> -->
    <% } %>
    <a href="/player_center/forgot_password">
    <img src="/images/forget_password.gif" />
    忘记密码？
    </a>
</li>
<li>
    <input type="submit" value=" " class="btn_input J-submit" />
</li>
<li>
    <a class="account_btn J-regist-btn _register_player_link" href="/player_center/iframe_register"></a>
</li>
</ul>

<input type='hidden' name='act' value='<%- act %>'>
</form>
<iframe name="<%- ui.loginIframeName %>" id="<%- ui.loginIframeName %>" width="0" height="0" border="0" style="display:none;border:0px;width:0px;height:0px;"></iframe> | <p><i class="icon icon-member"></i>TTT<a href="/player_center" ><%- playerName %></a></p>
<p>
<a href="/player_center/profile" ><%- langText.header_information %></a> |
<a href="/player_center/iframe_makeDeposit" ><%- langText.header_deposit %></a> |
<a href="/player_center/withdraw" ><%- langText.header_withdrawal %></a> |
<a href="/player_center/dashboard"><%- langText.header_memcashier %></a> |
<a href="/player_center/transactions" ><%- langText.header_report %></a> |
<a href="/player_center2/messages" ><%- langText.header_messages %> (<%- ui.messageCount %>)</a>
<a class="login_out_btn" href="<%- ui.logoutUrl %>" target="<%- ui.logoutIframeName %>"><%- langText.button_logout %></a>
</p>
<iframe name="<%- ui.logoutIframeName %>" id="<%- ui.logoutIframeName %>" width="0" height="0" border="0" style="display:none;border:0px;width:0px;height:0px;"></iframe>
 | //og.local | <div id="<%- popupId %>" style="background-color: #fff;border-radius: 10px 10px 10px 10px;box-shadow: 0 0 25px 5px #999;color: #111;display: none;padding: 10px;margin-top:20px;">
    <span class="_og_popup_close" style="background-color: #2b91af;color: #fff;cursor: pointer;display: inline-block;text-align: center;text-decoration: none;border-radius: 10px 10px 10px 10px;box-shadow: none;font: bold 25px sans-serif;padding: 6px 6px 2px;position: absolute;right: -6px;top: -6px;height: 30px;width:30px">X</span>
    <div class="_og_popup_iframe_content" ></div>
</div> | <% _.each( game_types, function( listItem ) { %>
	<li<% if ( listItem.active ){ %> class="curr"<% } %>>
		<a href="<%- playerServerUri %>/game_description/index/<%- listItem.game_platform_id %>/<%- listItem.id %>"><%- listItem.game_type %></a>
	</li>
<% }); %> | <% _.each( game_descriptions, function( listItem ) { %>
	<li class="product">
		<div class="product-img"><img src="<%- listItem.game_image %>" /></div>
		<div class="product-name mt10"><%= listItem.game_name %></div>
		<div class="fn-clear mt10">
			<a class="fn-left ui-btn real" href="<%- playerServerUri%>/iframe_module/goto_ptgame/default/<%- listItem.game_code%>/real" data-mode="real" data-code="<%- listItem.game_code %>" target='_blank'>进入游戏</a>
			<% if (listItem.game_type_id !== '7' || listItem.offline_enabled === '1') { %>
			<a class="fn-left ui-btn ml10 trial" href="<%- playerServerUri%>/pub/goto_trial_ptgame/<%- listItem.game_code%>" data-mode="trial" data-code="<%- listItem.game_code %>">免费试玩</a>
			<% } %>
		</div>
	</li>
<% }); %>

