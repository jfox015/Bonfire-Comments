<?php if (validation_errors()) : ?>
<div class="notification error">
	<?php echo validation_errors(); ?>
</div>
<?php endif; ?>

<div class="admin-box">

    <h3>[Module] Settings</h3>

    <?php echo form_open($this->uri->uri_string(), 'class="form-horizontal"'); ?>

    <fieldset>
        <legend><?php echo lang('mod_settings_title'); ?></legend>
	
		<div class="control-group <?php echo form_error('[field_name]') ? 'error' : '' ?>">
			<label class="control-label" for="[field_name]"><?php echo lang('mod_field_name'); ?></label>
			<div class="controls">
				 <input name="[field_name]" id="[field_name]" />
				<?php if (form_error('[field_name]')) echo '<span class="help-inline">'. form_error('[field_name]') .'</span>'; ?>
			</div>
		</div>
	
	</fieldset>
	
	<div class="form-actions">
		<input type="submit" name="submit" class="btn btn-primary" value="<?php echo lang('bf_action_save'); ?>" />
	</div>
	
	<?php echo form_close(); ?>
</div>

<script type="text/javascript">
    head.ready(function(){
        $(document).ready(function() {

        });
    });

</script>

