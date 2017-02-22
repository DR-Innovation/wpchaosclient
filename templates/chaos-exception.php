<div style="font-size:12px;border:1px solid gray;padding:10px;overflow-x:auto;color:#eee;background:rgba(0,0,0,.75);">
	<h1 style="font-size:16px;color:#eee;">Woups ... Something unexpected happend in the CHAOS:\_</h1>
	<?php if(!WP_DEBUG):?>
		<p>Don't you worrie - this is probably not your fault.</p>
	<?php endif;?>
	<?php if($exception):?>
		<p><?php echo htmlentities($exception->getMessage()) ?></p>
		<pre><?php echo htmlentities($exception->getTraceAsString()) ?></pre>
	<?php else:?>
		<em>The exception stacktrace was removed, see <?= $traceDumpFile ?> for details.</em>
	<?php endif;?>
</div>
