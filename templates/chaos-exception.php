<h1 class="text-error">Woups ... Something unexpected happend in the CHAOS:\_</h1>
<?php if(WP_DEBUG):?>
	<h2 class="text-error">As WordPress is running in debug mode - this is probably your fault.</h2>
<?php else:?>
	<h2 class="text-error">Don't you worrie - this is probably not your fault.</h2>
<?php endif;?>
<?php if($exception):?>
	<p class="text-error"><?php echo htmlentities($exception->getMessage()) ?></p>
	<pre class="text-error"><?php echo htmlentities($exception->getTraceAsString()) ?></pre>
<?php else:?>
	<p class="text-error">The exception stacktrace was removed.</p>
<?php endif;?>