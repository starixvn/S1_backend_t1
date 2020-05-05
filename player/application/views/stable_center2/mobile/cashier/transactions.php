    <div class="mmenu">
        <div class="mm_header">
            <ul>
                <li class="current"><?=lang('lang.select')?></li>
            </ul>
            <div class="mm_exit"></div>
        </div>
        <div class="mm_main">
            <ul class="acmenu" style="display: none;">

                <li id="deposit" data-trans_type="<?=Transactions::DEPOSIT?>"><?=lang('deposit.records')?><i></i></li>
                <li id="withdrawal" data-trans_type="<?=Transactions::WITHDRAWAL?>"><?=lang('withdrawal.records')?><i></i></li>
                <li id="transfer" data-trans_type="<?=Transactions::TRANSFER_TO_SUB_WALLET_FROM_MAIN_WALLET . ',' . Transactions::TRANSFER_FROM_SUB_WALLET_TO_MAIN_WALLET?>">转账记录<i></i></li>
                <!-- <li id="pro" style="margin-bottom: 20px;">优惠记录<i></i></li> -->
            </ul>

            <ul class="range" style="display: none;">

                <li data-start="<?=date('Y-m-d 00:00:00', strtotime('-1 day'))?>" data-end="<?=date('Y-m-d 23:59:59', strtotime('-1 day'))?>"><?=lang('Yesterday')?><i></i></li>
                <li data-start="<?=date('Y-m-d 00:00:00', strtotime('-1 week'))?>" data-end="<?=date('Y-m-d 23:59:59', strtotime('-1 day'))?>"><?=lang('Last Week')?><i></i></li>
                <?php
                    $startLastMonth = mktime(0, 0, 0, date("m") - 1, 1, date("Y"));
                    $endLastMonth = mktime(0, 0, 0, date("m"), 0, date("Y"));
                ?>
                <li data-start="<?=date("Y-m-d 00:00:00", $startLastMonth)?>" data-end="<?=date("Y-m-d 23:59:59", $endLastMonth)?>"><?=lang('Last Month')?><i></i></li>
                <?php
                    $year = date('Y') - 1; // Get current year and subtract 1
                    $start = mktime(0, 0, 0, 1, 1, $year);
                    $end = mktime(0, 0, 0, 12, 31, $year);
                ?>
                <li data-start="<?=date("Y-m-d 00:00:00", $start)?>" data-end="<?=date("Y-m-d 23:59:59", $end)?>"><?=lang('dt.lastyear')?><i></i></li>
                <li data-start="<?=date("Y-m-d 00:00:00", time())?>" data-end="<?=date("Y-m-d 23:59:59", time())?>"><?=lang('Today')?><i></i></li>
                <li data-start="<?=date("Y-m-d 00:00:00", strtotime('monday this week'))?>" data-end="<?=date("Y-m-d 23:59:59", strtotime('sunday this week'))?>"><?=lang('lang.week')?><i></i></li>
                <?php
                    $first = date("Y-m-d 00:00:00", strtotime("first day of this month"));
                    $last = date("Y-m-d 23:59:59", strtotime("last day of this month"));
                ?>
                <li data-start="<?=$first?>" data-end="<?=$last?>"><?=lang('This Month')?><i></i></li>
                <?php
                    $firstDayYear = date("Y-m-d 00:00:00", strtotime("first day of this year"));
                    $lastDayYear = date("Y-m-d 23:59:59", strtotime("last day of this year"));
                ?>
                <li data-start="<?=date('Y-m-d 00:00:00',strtotime(date('Y-01-01')));?>" data-end="<?=date("Y-m-d 23:59:59", strtotime("Last day of December", strtotime(date('Y-01-01'))))?>"><?=lang('This Year')?><i></i></li>

            </ul>

        </div>
    </div>

    <!---------交易记录-------->
    <div class="jl_nav" style="">
        <div class="xlmenu" id="range" style="float: left; margin-left: 10px;color: #fff;width: 20%;">
            <div class="arrow"></div>
            <ul>
                <li id="rangetext"><?=lang('Date Range')?></li>
            </ul>
        </div>
        <div class="click xlmenu" id="acmenu">
            <div class="arrow"></div>
            <ul>
                <li id="jltext"><?=lang('deposit.records')?></li>
            </ul>
        </div>
    </div>
    <div id="recordbox" class="home" style="z-index: 2; background: #f2f2f2;">
        <div class="jlkb"></div>
            <div id="recordlist"></div>
       <br class="clear">
       <br class="clear">
       <br class="clear">
       <br class="clear">
    </div>

   <style type="text/css">
       .jl_nav .click, .sp_nav .click{

        float: right !important;margin-right: 10px !important;

       }

       .daterange{
            padding: 0px;
            border: 1px solid #323436;
            color: #fff;
       }
   </style>

   <script type="text/javascript" src="/<?=$this->utils->getPlayerCenterTemplate(FALSE)?>/js/transactions.js"></script>
   <script type="text/javascript">
       $(function(){
            $('#ht').html('<?=lang('pay.transactions')?>');
            Transactions.dateRangeValueStart = "<?=date('Y-m-d 00:00:00', time())?>";
            Transactions.dateRangeValueEnd = "<?=date('Y-m-d 23:59:59', time())?>";
            Transactions.getList('deposit', "<?=Transactions::DEPOSIT?>", "<?=site_url('api/transactions')?>", $('#recordlist'));

       });

       $(document).ready(function () {
            $(".xlmenu").click(function () {
                var xlmenu = ".mmenu";
                var xlmovie = "mmenu_movie";

                $('.mm_main').find('ul').hide();

                $('.' + $(this).attr('id')).show();

                $(xlmenu).addClass(xlmovie);
                choicemenu();
                $(".mm_exit").click(function () {
                    $(xlmenu).removeClass(xlmovie);
                });
                function choicemenu() {
                    $(".mm_main li").unbind("click");
                    $(".mm_main li").click(function () {
                        $(".mm_main li i").removeClass("border");
                        $(this).find("i").addClass("border");
                        $(xlmenu).removeClass(xlmovie);

                        if( $(this).data('start') != undefined ) {
                            var startDate = $(this).data('start'),
                                endDate = $(this).data('end');

                            $('#rangetext').text($(this).text());

                            Transactions.dateRangeValueStart = startDate;
                            Transactions.dateRangeValueEnd = endDate;
                            return;
                        }

                        mmenu = $(this).attr("id");
                        var trans_type = $(this).data('trans_type');
                        var title = $(this).text();
                        console.log(title);
                        $('#jltext').html(title);

                        Transactions.getList(mmenu, trans_type, "<?=site_url('api/transactions')?>", $('#recordlist'));

                    });
                };
            });
            // PageList(mmenu);
        });

   </script>
