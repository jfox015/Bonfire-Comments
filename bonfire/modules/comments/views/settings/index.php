<?php if (validation_errors()) : ?>
<div class="notification error">
	<?php echo validation_errors(); ?>
</div>
<?php endif; ?>

<div class="admin-box">

    <h3><?php echo lang('mod_settings_title'); ?></h3>

    <?php echo form_open($this->uri->uri_string(), 'class="form-horizontal"'); ?>

    <fieldset>
        <legend><?php echo lang('mod_settings_options'); ?></legend>
	
			<!-- Require Approval -->
		<div class="control-group <?php echo form_error('require_approval') ? 'error' : '' ?>">
			<label class="control-label" for="require_approval"><?php echo lang('cm_require_approval'); ?></label>
			<div class="controls">
				<input type="checkbox" name="require_approval" id="require_approval" value="1" <?php echo $settings['comments.require_approval'] == 1 ? 'checked="checked"' : set_checkbox('comments.require_approval', 1); ?> />
				<span class="help-inline"><?php if (form_error('require_approval')) echo form_error('require_approval'); else echo lang('cm_require_approval_note'); ?></span>
			</div>
		</div>
			
			<!-- Allow Anonymous Comments -->
		<div class="control-group <?php echo form_error('anonymous_comments') ? 'error' : '' ?>">
			<label class="control-label" for="anonymous_comments"><?php echo lang('cm_anonymous_comments'); ?></label>
			<div class="controls">
				<input type="checkbox" name="anonymous_comments" id="anonymous_comments" value="1" <?php echo $settings['comments.anonymous_comments'] == 1 ? 'checked="checked"' : set_checkbox('comments.anonymous_comments', 1); ?> />
				<?php if (form_error('anonymous_comments')) echo '<span class="help-inline">'. form_error('anonymous_comments') .'</span>'; ?>
			</div>
		</div>
		
			<!-- Moderator Level -->
		<?php
		if (isset($roles) && is_array($roles) && count($roles)) :
			$selection = ( isset ($settings['comments.moderator_level']) ) ? (int) $settings['comments.moderator_level'] : 1;
			echo form_dropdown('moderator_level', $roles, $selection , lang('cm_moderator_level'), 'class="chzn-select" id="moderator_level"');
		else:
			echo('<div class="well">'.lang('cm_no_moderator_levels_found').'</div>');
		endif;
		?>
	
	</fieldset>
	
	<div class="form-actions">
		<input type="submit" name="submit" class="btn btn-primary" value="<?php echo lang('bf_action_save'); ?>" />
	</div>
	
	<?php echo form_close(); ?>
</div>

