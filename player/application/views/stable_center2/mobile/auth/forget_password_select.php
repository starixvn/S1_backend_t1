<style type="text/css">
/*---- DEFAULT CSS FOR FORGOT PW SECTION ------*/
.fp-wrapper {
  text-align: center;
  margin-top: 10%;
  margin-bottom: 10%;
}
.fp-wrapper .panel {
  box-shadow: 0 0 0 rgba(0,0,0,0);
  border: 0;
}
.fp-wrapper .panel-heading {
  border: 0;
}
.fp-wrapper .panel-heading h2 {
  margin: 0;
  font-size: 18px;
}
.fp-wrapper .panel-body input[type="button"] {
  background: #d5d5d5;
  color: #000;
  border: 1px #a4a4a4 solid;
  width: 100%;
  margin-bottom: 15px;
}
</style>


<div class="col-md-4 col-md-offset-4 fp-wrapper">
    <div class="panel panel-default">
        <div class="panel-heading">
            <h2><?=lang('lang.forgotpasswd')?></h2>
        </div>
        <div class="panel-body">
        <?php if ($password_recovery_option_1) : ?>
            <a href="<?=$password_recovery_option_1_url?>" class="btn btn-primary"><?=lang('Find password by security question')?></a>
        <?php endif; ?>
        <?php if ($password_recovery_option_2) : ?>
            <a href="<?=$password_recovery_option_2_url?>" class="btn btn-primary"><?=lang('Find password by SMS')?></a>
        <?php endif; ?>
        <?php if ($password_recovery_option_3) : ?>
            <a href="<?=$password_recovery_option_3_url?>" class="btn btn-primary"><?=lang('Find password by email')?></a>
        <?php endif; ?>
        </div>
    </div>
</div>


<script type="text/javascript">
  $(document).ready(function() {
    $("body").addClass("pwd_recovery");
  });
</script>