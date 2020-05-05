<div class="row">
    <div class="col-md-12">
        <div class="panel panel-og">
            <div class="panel-heading">
                <h4 class="panel-title pull-left"><span class="glyphicon glyphicon-edit"></span> <?=lang('promo.featuredPromotion');?></h4>
                <div class="clearfix"></div>
            </div>

            <div class="panel-body">
                <div id="promotion" class="row">
                    <!-- display promo history -->
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function(){
        get_featured_promo_pages(0);
    });

    function get_featured_promo_pages(segment) {
        var xmlhttp = GetXmlHttpObject();

        if (xmlhttp == null) {
            alert("Browser does not support HTTP Request");
            return;
        }

        url = base_url + "iframe_module/featured_promotions/"+ segment ;

        var div = document.getElementById("promotion");

        xmlhttp.onreadystatechange = function() {
            if (xmlhttp.readyState == 4) {
                div.innerHTML = xmlhttp.responseText;
            }

            if (xmlhttp.readyState != 4) {
                div.innerHTML = '<table class="table table-hover"><tr><td align="center" valign="middle" style="border:0; height:358px; width: 220px; text-align: center; font-size: 11px"><img src="' + imgloader + '"><br/>Loading. Please wait.</td></tr></table>';
            }
        }
        xmlhttp.open("GET", url, true);
        xmlhttp.send(null);
    }
</script>
