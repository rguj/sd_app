<?php
require_once('_autoload.php');

$msgs = get_msg(true);
//$_SESSION['msgs'] ?? [];
//unset($_SESSION['msgs']);

?>


<script>

const log = console.log.bind(console);

$(function(){
	// display session messages
	const msgs = <?= json_encode($msgs) ?>;
	if (Array.isArray(msgs)) {
		msgs.forEach(([type, message]) => {
			// success, info, warning, error
			toastr[type](message);
		});
	}
	
});
</script>