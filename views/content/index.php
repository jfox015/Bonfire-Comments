<div class="admin-box">
	<h3><?php echo lang('cm_custom_header') ?></h3>

	<ul class="nav nav-tabs" >
		<li <?php echo $filter=='' ? 'class="active"' : ''; ?>><a href="<?php echo $current_url; ?>">Approved</a></li>
		<li <?php echo $filter=='submitted' ? 'class="active"' : ''; ?>><a href="<?php echo $current_url .'?filter=submitted'; ?>">Submitted</a></li>
		<li <?php echo $filter=='flagged' ? 'class="active"' : ''; ?>><a href="<?php echo $current_url .'?filter=flagged'; ?>">Flagged</a></li>
		<li <?php echo $filter=='spam' ? 'class="active"' : ''; ?>><a href="<?php echo $current_url .'?filter=spam'; ?>">Spam</a></li>
		<li <?php echo $filter=='rejected' ? 'class="active"' : ''; ?>><a href="<?php echo $current_url .'?filter=rejected'; ?>">Rejected</a></li>
		<li <?php echo $filter=='deleted' ? 'class="active"' : ''; ?>><a href="<?php echo $current_url .'?filter=deleted'; ?>">Deleted</a></li>
		<li <?php echo $filter=='module' ? 'class="active"' : ''; ?> class="dropdown">
			<a href="#" class="drodown-toggle" data-toggle="dropdown">
				By Module <?php echo isset($filter_module) ? ": $filter_module" : ''; ?>
				<b class="caret light-caret"></b>
			</a>
			<ul class="dropdown-menu">
			<?php if (isset($modules)) { foreach ($modules as $module) : ?>
				<li>
					<a href="<?php echo $current_url .'?filter=module&module_name='. $module; ?>">
						<?php echo $module; ?>
					</a>
				</li>
			<?php endforeach; } ?>
			</ul>
		</li>
		
	</ul>

	<?php echo form_open(current_url()) ;?>

	<table class="table table-striped">
		<thead>
			<tr>
				<th style="width: 5%" class="column-check"><input class="check-all" type="checkbox" /></th>
				<th style="width: 5%"><?php echo lang('bf_id'); ?></th>
				<th style="width: 45%"><?php echo lang('cm_comment'); ?></th>
				<th style="width: 10%"><?php echo lang('cm_added_by'); ?></th>
				<th style="width: 10%"><?php echo lang('cm_added_on'); ?></th>
				<th style="width: 8%"><?php echo lang('cm_module'); ?></th>
				<th style="width: 7%"><?php echo lang('cm_status'); ?></th>
			</tr>
		</thead>
		<?php if (isset($comments) && is_array($comments) && count($comments)) : ?>
		<tfoot>
			<tr>
				<td colspan="8">
					<?php echo lang('bf_with_selected') ?>
					<input type="submit" name="submit" class="btn-success" value="<?php echo lang('cm_action_approve') ?>">
					<input type="submit" name="submit" class="btn-warning" value="<?php echo lang('cm_action_flag') ?>">
					<input type="submit" name="submit" class="btn-primary" value="<?php echo lang('cm_action_spam') ?>">
					<input type="submit" name="submit" class="btn-danger" value="<?php echo lang('cm_action_reject') ?>">
					<input type="submit" name="submit" class="btn-danger" id="delete-me" value="<?php echo lang('bf_action_delete') ?>" onclick="return confirm('<?php echo lang('sl_delete_confirm'); ?>')">
				</td>
			</tr>
		</tfoot>
		<?php endif; ?>
		<tbody>

		<?php if (isset($comments) && is_array($comments) && count($comments)) : ?>
			<?php foreach ($comments as $comment) : ?>
			<tr>
				<td>
					<input type="checkbox" name="checked[]" value="<?php echo $comment->id ?>" />
				</td>
				<td><?php echo $comment->id ?></td>
				<td><?php echo $comment->comment ?></a></td>
				<td><?php if (isset($comment->created_by) && !empty($comment->created_by)) :
					echo find_author_name($comment->created_by);
				elseif (isset($comment->anonymous_email) && !empty($comment->anonymous_email)) :
					echo $comment->anonymous_email;
				endif;
				?></td>
                <td><?php echo (isset($comment->created_on) && !empty($comment->created_on) && $comment->created_on != 0) ? date('m/d/Y',$comment->created_on) : ''- -''; ?></td>
                <td><?php echo $comment->module; ?></td>
				<td><?php
					$class = '';
					switch ($comment->status_id)
					{
						case 1: // Submitted
							$class = '';
							break;
						case 5: // Locked
							$class = " label-important";
							break;
						case 4: // spam
							$class = " label-inverse";
							break;
						case 3: // flagged
							$class = " label-warning";
							break;
						case 2: // Approved
						default:
							$class = " label-success";
							break;
					}
					?>
					<span class="label<?php echo($class); ?>">
					<?php echo($comment->status_name);?>
					</span>
				</td>
			</tr>
			<?php endforeach; ?>
		<?php else: ?>
			<tr>
				<td colspan="6"><?php echo lang('cm_no_matches_found') ?></td>
			</tr>
		<?php endif; ?>
		</tbody>
	</table>
	<?php echo form_close(); ?>

	<?php echo $this->pagination->create_links(); ?>

</div>