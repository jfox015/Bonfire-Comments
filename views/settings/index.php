<?php if (validation_errors()) : ?>
<div class="notification error">
	<?php echo validation_errors(); ?>
</div>
<?php endif; ?>

<div class="admin-box">

    <h3>Comments Settings</h3>

    <?php echo form_open($this->uri->uri_string(), 'class="form-horizontal"'); ?>

    <fieldset>
        <legend><?php echo lang('mod_settings_title'); ?></legend>
	
		<div class="control-group <?php echo form_error('anonymous_comments') ? 'error' : '' ?>">
			<label class="control-label" for="anonymous_comments"><?php echo lang('cm_anonymous_comments'); ?></label>
			<div class="controls">
				<input type="checkbox" name="anonymous_comments" id="anonymous_comments" value="1" <?php echo $settings['comments.anonymous_comments'] == 1 ? 'checked="checked"' : set_checkbox('comments.anonymous_comments', 1); ?> />
				<?php if (form_error('anonymous_comments')) echo '<span class="help-inline">'. form_error('anonymous_comments') .'</span>'; ?>
			</div>
		</div>
	
	</fieldset>
	
	<div class="form-actions">
		<input type="submit" name="submit" class="btn btn-primary" value="<?php echo lang('bf_action_save'); ?>" />
	</div>
	
	<?php echo form_close(); ?>
</div>

