
  <div id="main">
    <div id="main_con">
       <div class="pub_txt">请选择您的充值方式</div>
    </div>
    <div id="main_con" class="clearfix">
       <div class="box_left">
       		<ul>
            	<li><a href="javascript:void(0)" class="actived deposite_mode">网银通道</a></li>
            	<li><a href="javascript:void(0)" class=" deposite_mode">在线支付通道</a></li>
            	<li><a href="javascript:void(0)" class=" deposite_mode">ATM通道</a></li>
          </ul>
       </div>
       <div class="box_right">
       		<div class="right_title">网银通道</div>
            <div class="right_cont">
            	<ul class="type_bank_info">
                	<li><span>存款金额：</span><input type="text" class="type_input"/><i>* 单笔最低存款50.00元，单笔最高存款100000.00元</i></li>
                	<li class="clearfix"><span>选择银行：</span>
                    	<div class="bank_list">
                        	<ul>
                            	<li><a href="javascript:void(0)"><img src="/ttt/images/bank_zs.jpg" /></a>
                                  <div class="tips_rim">
                                    <div class="title_box clearfix"><img src="/ttt/images/bank_ico_sj.gif"><span>招商银行</span><span class="right">充值教程</span></div>
                                    <div class="tips_cont">
                                      <ul>
                                        <li><span>账户名：</span>张三</li>
                                        <li><span>账　号：</span>6225365656498994946644</li>
                                        <li><span>开户行：</span>招商银行北京支行</li>
                                        <li><em>3</em>秒自动到账，存款成功注意交易流水号</li>
                                      </ul>
                                    </div>
                                  </div>
                              </li>
                            	<li><a href="javascript:void(0)"><img src="/ttt/images/bank_gs.jpg" /></a>
                                  <div class="tips_rim">
                                    <div class="title_box clearfix"><img src="/ttt/images/bank_ico_sj.gif"><span>招商银行</span><span class="right">充值教程</span></div>
                                    <div class="tips_cont">
                                      <ul>
                                        <li><span>账户名：</span>张三</li>
                                        <li><span>账　号：</span>6225365656498994946644</li>
                                        <li><span>开户行：</span>招商银行北京支行</li>
                                        <li><em>3</em>秒自动到账，存款成功注意交易流水号</li>
                                      </ul>
                                    </div>
                                  </div>
                              </li>
                            	<li><a href="javascript:void(0)"><img src="/ttt/images/bank_ny.jpg" /></a>
                                  <div class="tips_rim">
                                    <div class="title_box clearfix"><img src="/ttt/images/bank_ico_sj.gif"><span>招商银行</span><span class="right">充值教程</span></div>
                                    <div class="tips_cont">
                                      <ul>
                                        <li><span>账户名：</span>张三</li>
                                        <li><span>账　号：</span>6225365656498994946644</li>
                                        <li><span>开户行：</span>招商银行北京支行</li>
                                        <li><em>3</em>秒自动到账，存款成功注意交易流水号</li>
                                      </ul>
                                    </div>
                                  </div>
                              </li>
                            </ul>
                        </div>
                    </li>
                	<li><span>收款账户名：</span><input type="text" class="type_input"/><i>* 填写收款账户名</i></li>
                	<li><span>收款银行：</span><input type="text" class="type_input"/><i>* 选择收款银行</i></li>
                	<li><span>存款人姓名：</span><input type="text" class="type_input"/><i>* 填写存款人姓名</i></li>
                  <li><span>交易流水号：</span><input type="text" class="type_input"/><i>* 填写转账交易流水编号</i></li>
                  <li><span>老虎机优惠：</span>
                      <div class="radio">
                        <label>
                          <input type="radio" name="optionsRadios" id="optionsRadios1" value="option1">
                          申请
                        </label>
                      </div>
                      <div class="radio">
                        <label>
                          <input type="radio" name="optionsRadios" id="optionsRadios2" value="option2">
                          不申请
                        </label>
                      </div>
                  <i class="bz_info">注：请遵守相关优惠政策投注倍数</i></li>
                	<li><input type="button" value="确定" class="btn post_info" /></li>
                </ul>
                
                <ul class="bank_info">
                	<li><span>收款账户名：</span><input type="text"  class="type_input"/><i>* 填写收款账户名</i></li>
                	<li><span>收款账户：</span><input type="text" class="type_input"/><i>* 填写收款账户</i></li>
                	<li><span>收款银行名称：</span><input type="text" class="type_input"/><i>* 请填写收款银行名称</i></li>
                	<li><span>收款银行：</span><input type="text" class="type_input"/><i>* 请填写收款银行</i></li>
                	<li><span>存款金额：</span><input type="text" class="type_input"/><i>* 请填写存款金额</i></li>
                	<li><span>存款人姓名：</span><input type="text" class="type_input"/><i>* 请填写存款人姓名</i></li>
                	<li><span>交易流水号：</span><input type="text" class="type_input"/><i>* 请填写交易流水号</i></li>
                	<li><input type="button" value="确定" class="btn post_deposit" /></li>
                </ul>
                
                
            </div>
            
       </div>
    </div>
    
    <div class="main_foot"></div>
  </div><!--main-->

  <script>
   
   $(document).ready(function(){
      //replace title depend on what mode
      $('.deposite_mode').on('click',function(){
         $('.deposite_mode').removeClass( "actived" ); 
         $(this).addClass( "actived" );
          //$(this).text();
          $('.right_title').text($(this).text());
      });

    });
  </script>


