<?php
$is_dev=$this->utils->getConfig('is_vue_dev');
$uri_prefix=$is_dev ? '/resources/vue/dev' : '/resources/vue/live';
?>

<style type="text/css">
/*========hack template css==========*/
#page-content-wrapper{
    /* width: 100%; */
    /* padding-top: 52px; */
}
ul.navbar-nav > li > a{
padding-top: 6px;
padding-bottom: 6px;
}
.navbar-header .navbar-brand{
    height: 34px;
    padding-top: 6px;
    padding-bottom: 4px;
}
#gotomemberinfo{
    padding-top: 2px;
    padding-bottom: 2px;
}
/*========hack template css==========*/

</style>
<?php
if(!$is_dev){
?>

<link href="<?=$this->utils->processAnyUrl('/css/app.css', $uri_prefix)?>" rel=preload as=style>
<link href="<?=$this->utils->processAnyUrl('/css/chunk-vendors.css', $uri_prefix)?>" rel=preload as=style>
<link href="<?=$this->utils->processAnyUrl('/js/app.js',$uri_prefix)?>" rel=preload as=script>
<link href="<?=$this->utils->processAnyUrl('/js/chunk-vendors.js',$uri_prefix)?>" rel=preload as=script>
<link href="<?=$this->utils->processAnyUrl('/css/app.css', $uri_prefix)?>" rel=stylesheet>
<link href="<?=$this->utils->processAnyUrl('/css/chunk-vendors.css', $uri_prefix)?>" rel=stylesheet>

<div id=app></div>

<script src="<?=$this->utils->processAnyUrl('/js/chunk-vendors.js', $uri_prefix)?>"></script>
<script src="<?=$this->utils->processAnyUrl('/js/app.js', $uri_prefix)?>"></script>

<?php if($this->utils->isEnabledFeature('use_pwa_loader')){ ?>
<script>
  if('serviceWorker' in navigator) {
    navigator.serviceWorker.register('<?=$this->utils->processAnyUrl('/sw.js', '/resources/vue')?>')
      .then(function() {
        console.log('Service Worker Registered');
    });
  }
</script>
<?php }?>

<?php
}else{
?>
<link href="<?=$uri_prefix?>/app.js" rel="preload" as="script"></head>

<div id=app></div>

<script type="text/javascript" src="<?=$uri_prefix?>/app.js"></script>

<?php
}
?>

<style type="text/css">
/*========hack element css==========*/
#main_content .el-menu-item{
height: 50px;
line-height: 50px;
}
#main_content #main_container{
    min-height: 600px;
}
#main_content .el-aside{
    color: #fff;
    background-color: rgb(34, 34, 34);
}
#main_content .el-aside .el-menu{
    border-right: 0px;
}
.switch_sidebar{
    /* color: #fff; */
    margin-left: 12px;
    margin-top: 8px;
}
.sidebar_menu .el-menu-item .fa-icon{
    margin-right: 10px;
}
#main_content .adjust_table_header{
    padding: 1px 0;
    font-size: 14px;
}
#main_content .adjust_table_cell{
    padding: 1px 0;
}
#main_content .el-table .caret-wrapper{
    height: 33px;
}
#main_content .el-table .cell{
    padding-left: 6px;
    padding-right: 6px;
}
#main_content .el-table__fixed-footer-wrapper .el-table__footer td{
    text-align: right;
}
/* fix bug for total row */
#main_content .el-table{
    overflow: auto;
}
#main_content .el-table__body-wrapper,
#main_content .el-table__header-wrapper,
#main_content .el-table__footer-wrapper{
   overflow:visible;
}
#main_content .el-table::after{
   position: relative !important;
}
#main_content .el-row {
    margin-bottom: 6px;
}
/*========hack element css==========*/
</style>
