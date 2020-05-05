<div class="container dashboar-container">
    <?php include $template_path . '/includes/components/news.php';?>
    <div class="member-center">
        <div class="col-md-12 mc-content nopadding">
            <?=$main_content?>
        </div>
    </div>
</div>
<script type="text/javascript">
    $(function(){
        // show_loading();
        $('.dashboar-container .mc-content iframe').on('load', function(){
            // stop_loading();
        })
    })
</script>