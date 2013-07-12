<form id="alipaysubmit" name="alipaysubmit" action="<?php echo $action; ?>" method="<?php echo $method; ?>">
<?php while (list ($key, $val) = each ($para)) {?>
<input type="hidden" name="<?php echo $key; ?>"  value="<?php echo $val; ?>"/>

<?php }?>
  <div class="buttons">
    <div class="right">
      <input type="submit" value="<?php echo $button_confirm; ?>" class="button" />
    </div>
  </div>
</form>