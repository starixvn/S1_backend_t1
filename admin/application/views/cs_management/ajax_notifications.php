<a class='notificationRefreshList' href="#new">
        <div class="col-md-2 notificationDashboard hover-shadow <?= ($chatNew != 0) ? 'notDboard-active' : ''?>" id="notificationDashboard-new" style="background-color: #fcf8e3;">
            <?= lang('cs.total'); ?><br/><span class="notificationDashboardTxt" id="notificationDashboard-new"><?= $chatNew ?></span><br/>
            <span class="notificationDashboardLabel" id="notificationDashboard-new"> <?= lang('cs.unassign'); ?> </span>
        </div>
    </a>            

<a class='notificationRefreshList' href="#unread">
    <div class="col-md-2 notificationDashboard hover-shadow <?= ($chatUnread != 0) ? 'notDboard-active' : ''?>" id="notificationDashboard-unread" style="background-color: #d9edf7;">
        <?= lang('cs.total'); ?><br/><span class="notificationDashboardTxt" id="notificationDashboard-unread"><?= $chatUnread ?></span><br/>
        <span class="notificationDashboardLabel" id="notificationDashboard-unread"> <?= lang('cs.new'); ?> </span>
    </div>
</a>