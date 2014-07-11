<div id="waitload" class="well center" style="display:none;">
    <img src="<?php echo(Assets::assets_url().('images/ajax-loader.gif'));?>" width="28" height="28" border="0" align="absmiddle" /><br />Operation in progress. Please wait...
</div>
<div id="ajaxStatusBox" style="display:none;"><div id="ajaxStatus" class="alert"></div></div>
<div class="right"><a href="#" class="btn" id="reload_comments"><i class="icon-refresh"></i> Update</a></div>
<div id="comments">
<?php 
if (isset($comments) && is_array($comments) && count($comments)) : ?>
<?php	
	$count = 1;
	foreach($comments as $comment) : ?>
	<div class="well">
		<div data-toggle="collapse" href="#comment<?php echo $count; ?>">
			<b><?php echo $comment->creator ." ". $comment->created; ?></b>
		</div>
		<div id="comment<?php echo $count; ?>">
			<p><?php echo $comment->comment; ?></p>
		</div>
	</div>
<?php
	$count++;
	endforeach;?>
<?php
else: ?>
	<div class="well">
		<?php echo lang('us_no_comments'); ?>
	</div>
<?php
endif;
?>
</div>