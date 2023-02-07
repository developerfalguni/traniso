<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

function start_panel($title = 'Untitled', $icon = false, $class = false, $links = false) {
	return '
	<div class="card card-default">
		<div class="card-header">
			<span class="card-icon">' . ($icon ? $icon : '') . '</span>
			<span class="card-links">' . ($links ? $links : '') . '</span>
			<h6 class="card-title"> <i class="fa fa-paperclip pr-1"></i> ' . $title . '</h6>
		</div>
		<div class="card-body ' . ($class ? $class : '') . '">';
}

function start_panel_tabs($tabs = '', $icon = false, $class = false, $links = false) {
	return '
	<div class="card card-default">
		<div class="card-header">
			<span class="card-icon">' . ($icon ? $icon : '') . '</span>
			<span class="card-links">' . ($links ? $links : '') . '</span>
			<span>' . $tabs . '</span>
		</div>
		<div class="card-body">';
}

function start_panel_footer() {
	return '</div>
	<div class="card-footer">';
}


function end_sl_panel() {
	return '</div>';
}

function end_panel() {
	return '</div>';
}
