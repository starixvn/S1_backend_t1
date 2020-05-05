<?php
$cnt_message = $this->utils->countUnreadAdminMessage();
?>
        <a href="#" class="dropdown-toggle" data-toggle="dropdown" href="#"><i class="fa fa-bell-o"></i>
        <?php if ($cnt_message > 0) {?> <span class="badge"><?php echo $cnt_message; ?></span><?php }?>
        </a>
        <ul class="dropdown-menu user-option" role="menu">
            <?php

$admin_messages = $this->utils->getAllAdminMessage();
if (!empty($admin_messages)) {
    foreach ($admin_messages as $msg) {
?>
            <li ><a target="_blank" href="<?php echo site_url('home/view_messages/'.$msg['id']); ?>">
            <?php if ($msg['status'] == 3) {?>
                <i class="fa fa-newspaper-o text-warning"></i>
            <?php }?>
            <?php echo $this->utils->truncateWith($msg['content'], 40); ?>
            </a></li>
            <?php

    }
} else {

?>
            <li><a href="#"><?php echo lang('Empty'); ?></a></li>
<?php

}
?>
        </ul>
