<?php if (!function_exists('form_textarea')) : $this->load->helper('form'); endif ?>
<fieldset>
		<!-- Add Comment -->
	<div class="control-group">
		 <label class="control-label"><?php echo lang('cm_add_comment') ?></label>
		<div class="controls">
			<?php echo form_textarea( array( 'name' => 'comment_txt', 'id' => 'comment_txt', 'rows' => '5', 'class'=>'span7','cols' => '80' ));?>
			<span class="help-inline"></span>
		</div>
	</div>
	<?php if ($anonymous == 'true') { ?>
	<div class="control-group">
		 <label class="control-label"><?php echo lang('cm_email') ?></label>
		<div class="controls">
			<input type="text" class="span6" id="anonymous_email" name="anonymous_email" />
			<span class="help-inline"></span>
		</div>
	</div>
	<?php } ?>
	<div class="span6 right">
		<input type="submit" name="submit_comment" id="submit_comment" class="btn" value="<?php echo lang('us_add_comment') ?>" />
	</div>
</fieldset>