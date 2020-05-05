<?php
// if (isset($currentLang)) {$currentLang = '2';}
?>
<select name="language" id="lang_select" class="form-control input-sm" >
   <option value="1" <?php echo ($currentLang == '1') ? 'selected' : ''; ?> >English</option>
   <option value="2" <?php echo ($currentLang == '2') ? 'selected' : ''; ?> >中文</option>
   <option value="3" <?php echo ($currentLang == '3') ? 'selected' : ''; ?> >Indonesian</option>
   <option value="4" <?php echo ($currentLang == '4') ? 'selected' : ''; ?> >Vietnamese</option>
</select>