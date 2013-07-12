<?php echo $header; ?>
<div id="content">
  <div class="breadcrumb">
    <?php foreach ($breadcrumbs as $breadcrumb) { ?>
    <?php echo $breadcrumb['separator']; ?><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a>
    <?php } ?>
  </div>
  <?php if ($error_warning) { ?>
  <div class="warning"><?php echo $error_warning; ?></div>
  <?php } ?>
  <div class="box">
    <div class="heading">
      <h1><img src="view/image/payment.png" alt="" /> <?php echo $heading_title; ?></h1>
      <div class="buttons"><a onclick="$('#form').submit();" class="button"><?php echo $button_save; ?></a><a onclick="location = '<?php echo $cancel; ?>';" class="button"><?php echo $button_cancel; ?></a></div>
    </div>
    <div class="content">
      <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form">
        <table class="form">
		<tr>
            <td><span class="required">*</span> <?php echo $entry_account; ?></td>
            <td><input type="text" name="alipay_account" value="<?php echo $alipay_account; ?>" />
              <?php if ($error_account) { ?>
              <span class="error"><?php echo $error_account; ?></span>
              <?php } ?></td>
          </tr>
                    
          <tr>
            <td><span class="required">*</span> <?php echo $entry_merchant; ?></td>
            <td><input type="text" name="alipay_merchant" value="<?php echo $alipay_merchant; ?>" />
              <?php if ($error_merchant) { ?>
              <span class="error"><?php echo $error_merchant; ?></span>
              <?php } ?></td>
          </tr>
          <tr>
            <td><span class="required">*</span> <?php echo $entry_signature; ?></td>
            <td><input type="text" name="alipay_signature" value="<?php echo $alipay_signature; ?>" />
              <?php if ($error_signature) { ?>
              <span class="error"><?php echo $error_signature; ?></span>
              <?php } ?></td>
          </tr>
          
          <tr>
            <td><span class="required">*</span> <?php echo $entry_alipay_callback_base_url; ?></td>
            <td><input type="text" name="alipay_callback_base_url" value="<?php echo $alipay_callback_base_url; ?>" />
              <?php if ($error_alipay_callback_base_url) { ?>
              <span class="error"><?php echo $error_alipay_callback_base_url; ?></span>
              <?php } ?></td>
          </tr>
          
          <tr>
            <td><?php echo $entry_debug; ?></td>
            <td><select name="alipay_debug">
                <?php if ($alipay_debug) { ?>
                <option value="1" selected="selected"><?php echo $text_enabled; ?></option>
                <option value="0"><?php echo $text_disabled; ?></option>
                <?php } else { ?>
                <option value="1"><?php echo $text_enabled; ?></option>
                <option value="0" selected="selected"><?php echo $text_disabled; ?></option>
                <?php } ?>
              </select></td>
          </tr>
          
          <tr>
            <td><?php echo $entry_type; ?></td>
            <td><select name="alipay_type">
                <?php if ($alipay_type == 'alipay') { ?>
                <option value="alipay" selected="selected"><?php echo $text_pay; ?></option>
                <?php } else { ?>
                <option value="alipay"><?php echo $text_pay; ?></option>
                <?php } ?>
                <?php if ($alipay_type == 'card') { ?>
                <option value="card" selected="selected"><?php echo $text_card; ?></option>
                <?php } else { ?>
                <option value="card"><?php echo $text_card; ?></option>
                <?php } ?>
              </select></td>
          </tr>
          <tr>
            <td><?php echo $entry_total; ?></td>
            <td><input type="text" name="alipay_total" value="<?php echo $alipay_total; ?>" /></td>
          </tr>          
          <tr>
            <td><?php echo $entry_alipay_wait_buyer_pay_status; ?></td>
            <td><select name="alipay_wait_buyer_pay_status_id">
                <?php foreach ($order_statuses as $order_status) { ?>
                <?php if ($order_status['order_status_id'] == $alipay_wait_buyer_pay_status_id) { ?>
                <option value="<?php echo $order_status['order_status_id']; ?>" selected="selected"><?php echo $order_status['name']; ?></option>
                <?php } else { ?>
                <option value="<?php echo $order_status['order_status_id']; ?>"><?php echo $order_status['name']; ?></option>
                <?php } ?>
                <?php } ?>
              </select></td>
          </tr>
          
          <tr>
            <td><?php echo $entry_alipay_wait_seller_send_goods_status; ?></td>
            <td><select name="alipay_wait_seller_send_goods_status_id">
                <?php foreach ($order_statuses as $order_status) { ?>
                <?php if ($order_status['order_status_id'] == $alipay_wait_seller_send_goods_status_id) { ?>
                <option value="<?php echo $order_status['order_status_id']; ?>" selected="selected"><?php echo $order_status['name']; ?></option>
                <?php } else { ?>
                <option value="<?php echo $order_status['order_status_id']; ?>"><?php echo $order_status['name']; ?></option>
                <?php } ?>
                <?php } ?>
              </select></td>
          </tr>
          
         <tr>
            <td><?php echo $entry_alipay_wait_buyer_confirm_goods_status; ?></td>
            <td><select name="alipay_wait_buyer_confirm_goods_status_id">
                <?php foreach ($order_statuses as $order_status) { ?>
                <?php if ($order_status['order_status_id'] == $alipay_wait_buyer_confirm_goods_status_id) { ?>
                <option value="<?php echo $order_status['order_status_id']; ?>" selected="selected"><?php echo $order_status['name']; ?></option>
                <?php } else { ?>
                <option value="<?php echo $order_status['order_status_id']; ?>"><?php echo $order_status['name']; ?></option>
                <?php } ?>
                <?php } ?>
              </select></td>
          </tr>
          
          <tr>
            <td><?php echo $entry_alipay_trade_finished_status; ?></td>
            <td><select name="alipay_trade_finished_status_id">
                <?php foreach ($order_statuses as $order_status) { ?>
                <?php if ($order_status['order_status_id'] == $alipay_trade_finished_status_id) { ?>
                <option value="<?php echo $order_status['order_status_id']; ?>" selected="selected"><?php echo $order_status['name']; ?></option>
                <?php } else { ?>
                <option value="<?php echo $order_status['order_status_id']; ?>"><?php echo $order_status['name']; ?></option>
                <?php } ?>
                <?php } ?>
              </select></td>
          </tr>
          
         <tr>
            <td><?php echo $entry_alipay_trade_closed_status; ?></td>
            <td><select name="alipay_trade_closed_status_id">
                <?php foreach ($order_statuses as $order_status) { ?>
                <?php if ($order_status['order_status_id'] == $alipay_trade_closed_status_id) { ?>
                <option value="<?php echo $order_status['order_status_id']; ?>" selected="selected"><?php echo $order_status['name']; ?></option>
                <?php } else { ?>
                <option value="<?php echo $order_status['order_status_id']; ?>"><?php echo $order_status['name']; ?></option>
                <?php } ?>
                <?php } ?>
              </select></td>
          </tr>
          
          <tr>
            <td><?php echo $entry_alipay_trade_error_status; ?></td>
            <td><select name="alipay_trade_error_status_id">
                <?php foreach ($order_statuses as $order_status) { ?>
                <?php if ($order_status['order_status_id'] == $alipay_trade_error_status_id) { ?>
                <option value="<?php echo $order_status['order_status_id']; ?>" selected="selected"><?php echo $order_status['name']; ?></option>
                <?php } else { ?>
                <option value="<?php echo $order_status['order_status_id']; ?>"><?php echo $order_status['name']; ?></option>
                <?php } ?>
                <?php } ?>
              </select></td>
          </tr>
          
          <tr>
            <td><?php echo $entry_geo_zone; ?></td>
            <td><select name="alipay_geo_zone_id">
                <option value="0"><?php echo $text_all_zones; ?></option>
                <?php foreach ($geo_zones as $geo_zone) { ?>
                <?php if ($geo_zone['geo_zone_id'] == $alipay_geo_zone_id) { ?>
                <option value="<?php echo $geo_zone['geo_zone_id']; ?>" selected="selected"><?php echo $geo_zone['name']; ?></option>
                <?php } else { ?>
                <option value="<?php echo $geo_zone['geo_zone_id']; ?>"><?php echo $geo_zone['name']; ?></option>
                <?php } ?>
                <?php } ?>
              </select></td>
          </tr>
          <tr>
            <td><?php echo $entry_status; ?></td>
            <td><select name="alipay_status">
                <?php if ($alipay_status) { ?>
                <option value="1" selected="selected"><?php echo $text_enabled; ?></option>
                <option value="0"><?php echo $text_disabled; ?></option>
                <?php } else { ?>
                <option value="1"><?php echo $text_enabled; ?></option>
                <option value="0" selected="selected"><?php echo $text_disabled; ?></option>
                <?php } ?>
              </select></td>
          </tr>
          <tr>
            <td><?php echo $entry_sort_order; ?></td>
            <td><input type="text" name="alipay_sort_order" value="<?php echo $alipay_sort_order; ?>" size="1" /></td>
          </tr>
        </table>
      </form>
    </div>
  </div>
</div>
<?php echo $footer; ?>