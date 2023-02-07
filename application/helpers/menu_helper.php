<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

function ajaxMenu($company_id, $perm, $is_admin, $return = 'json') {
	$CI =& get_instance();

	if (isset($perm[$company_id]))
		$perm = $perm[$company_id];
		$menu = $CI->config->item('menus');
		$submenu_url = function($result, $submenus, $permission = 0, $parent = false, $display_name = false) use (&$submenu_url, &$perm, &$is_admin) {
		foreach ($submenus as $menu => $items) {
			
			if (isset($items['hide'])) 
				continue;

			if (isset($items['url']) AND 
				($is_admin OR ($permission | isset($perm[$menu])))
			) {
				$result[] = [
					'parent' => ($display_name ? $display_name . ' > ' : ''),
					'url'    => $items['url'],
					'name'   => $items['name'],
					'sms'    => isset($items['sms']) ? $items['sms'] : false,
					'email'  => isset($items['email']) ? $items['email'] : false,
				];
			}

			if (isset($items['nodes'])) {
				if ($parent)
					$result += $submenu_url(
						$result, 
						$items['nodes'], ($permission | (isset($perm[$menu]) ? $perm[$menu] : 0)), 
						$menu, 
						$display_name .' > ' . $items['name']
					);
				else
					$result += $submenu_url(
						$result, 
						$items['nodes'], ($permission | (isset($perm[$menu]) ? $perm[$menu] : 0)), 
						$menu, 
						$items['name']
					);
			}
		}
		return $result;
	};
	$result = $submenu_url([], $menu);

	if ($return == 'json')
		return json_encode($result, JSON_UNESCAPED_UNICODE);
	else
		return $result;
}
