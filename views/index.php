<?php 
if (isset($comments) && is_array($comments) && count($comments)) : ?>
<?php	
	foreach($comments as $comment) : ?>
<div class="well">
	<b><?php echo find_author($comment->created_by) ." ". date('Y-d-m h:i:s A',$comment->created_on); ?></b>
	<p>
	<?php echo $comment->comment; ?>
	</p>
</div>
<?php
	endforeach;?>
<?php
else: ?>
<div class="well">
	<?php echo lang('us_no_comments'); ?>
</div>
<?php
endif;
?>
<?php echo form_open(site_url(SITE_AREA.'/content/comments/'), 'class="form-horizontal"'); ?>
<fieldset>
		<!-- Add Comment -->
	<div class="control-group <?php echo form_error('comment') ? 'error' : '' ?>">
		 <label class="control-label"><?php echo lang('us_add_comment') ?></label>
		<div class="controls">
			<?php echo form_textarea( array( 'name' => 'comment', 'id' => 'comment', 'rows' => '5', 'class'=>'span6','cols' => '80', 'value' => set_value('comment') ));?>
			<?php if (form_error('comment')) echo '<span class="help-inline">'. form_error('comment') .'</span>'; ?>
		</div>
	</div>
</fieldset>
<div class="form-actions">
	<input type="submit" name="submit_comment" id="submit_comment" class="btn" value="<?php echo lang('us_add_comment') ?>" />
</div>
<?php echo (isset($return_page) && !empty($return_page)) : 
	echo form_hidden('return_page',$return_page); 
endif; ?>
<?php echo form_close(); ?>