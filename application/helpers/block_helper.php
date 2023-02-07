<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

function start_panel($title = 'Untitled', $icon = false, $class = false, $links = false) {
	return '
	<div class="widget-box">
		<div class="widget-title">
			' . ($icon ? $icon : '') . '
			<h5>' . $title . '</h5>
			' . ($links ? $links : '') . '
		</div>
		<div class="widget-content ' . ($class ? $class : '') . '">';
}

function start_panel_tabs($tabs = '', $icon = false, $class = false, $links = false) {
	return '
	<div class="widget-box">
		<div class="widget-title">
			' . ($icon ? $icon : '') . '
			' . $tabs . '
			' . ($links ? $links : '') . '
		</div>
		<div class="widget-content ' . ($class ? $class : '') . '">';
}

function end_panel() {
	return '</div>
</div>';
}
