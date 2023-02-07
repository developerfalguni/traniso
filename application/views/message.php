<?php
foreach($messages as $m) {
	echo '<span class="tiny">'
		. $m['created'] . '&nbsp;&nbsp;&nbsp;' 
		. anchor('#message-' . $m['id'], '<span class="tiny">Reply</span>', 'data-toggle="modal"') . ' | ' 
		. anchor('', '<span class="tiny">Forward</span>') . '<br />'
		. anchor($m['url'], 'From ' . humanize($m['from_user']) . ' ' . $m['action'] . ' ' . humanize($m['table_name']) . '-' . $m['row_id'])	. ($m['message'] ? '<br />' 
		. $m['message'] : '')
		. '</span>
		<hr />';

}
?>