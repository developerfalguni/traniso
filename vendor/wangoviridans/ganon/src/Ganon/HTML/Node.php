<?php

namespace Wangoviridans\Ganon\HTML;

class Node {
	const NODE_ELEMENT = 0;
	const NODE_TEXT = 1;
	const NODE_COMMENT = 2;
	const NODE_CONDITIONAL = 3;
	const NODE_CDATA = 4;
	const NODE_DOCTYPE = 5;
	const NODE_XML = 6;
	const NODE_ASP = 7;
	const NODE_TYPE = self::NODE_ELEMENT;
	
	public $selectClass = '\Wangoviridans\Ganon\HTML\Selector'; //'HTML_Selector';
	public $parserClass = '\Wangoviridans\Ganon\HTML\Parser\HTML5';
	public $childClass = __CLASS__;
	public $childClass_Text = '\Wangoviridans\Ganon\HTML\Node\Text';
	public $childClass_Comment = '\Wangoviridans\Ganon\HTML\Node\Comment';
	public $childClass_Conditional = '\Wangoviridans\Ganon\HTML\Node\Conditional';
	public $childClass_CDATA = '\Wangoviridans\Ganon\HTML\Node\Cdata';
	public $childClass_Doctype = '\Wangoviridans\Ganon\HTML\Node\Doctype';
	public $childClass_XML = '\Wangoviridans\Ganon\HTML\Node\Xml';
	public $childClass_ASP = '\Wangoviridans\Ganon\HTML\Node\Asp';
	public $parent = null;
	public $attributes = array();
	public $attributes_ns = null;
	public $children = array();
	public $tag = '';
	public $tag_ns = null;
	public $self_close = false;
	public $self_close_str = ' /';
	public $attribute_shorttag = true;
	public $filter_map = array(
		'root' => 'filter_root',
		'nth-child' => 'filter_nchild',
		'eq' => 'filter_nchild',
		'gt' => 'filter_gt',
		'lt' => 'filter_lt',
		'nth-last-child' => 'filter_nlastchild',
		'nth-of-type' => 'filter_ntype',
		'nth-last-of-type' => 'filter_nlastype',
		'odd' => 'filter_odd',
		'even' => 'filter_even',
		'every' => 'filter_every',
		'first-child' => 'filter_first',
		'last-child' => 'filter_last',
		'first-of-type' => 'filter_firsttype',
		'last-of-type' => 'filter_lasttype',
		'only-child' => 'filter_onlychild',
		'only-of-type' => 'filter_onlytype',
		'empty' => 'filter_empty',
		'not-empty' => 'filter_notempty',
		'has-text' => 'filter_hastext',
		'no-text' => 'filter_notext',
		'lang' => 'filter_lang',
		'contains' => 'filter_contains',
		'has' => 'filter_has',
		'not' => 'filter_not',
		'element' => 'filter_element',
		'text' => 'filter_text',
		'comment' => 'filter_comment'
	);
	
	public function __construct($tag, $parent) {
		$this->parent = $parent;
		if (is_string($tag)) {
			$this->tag = $tag;
		} else {
			$this->tag = $tag['tag_name'];
			$this->self_close = $tag['self_close'];
			$this->attributes = $tag['attributes'];
		}
	}

	public function __destruct() {
		$this->delete();
	}

	public function __toString() {
		return (($this->tag === '~root~') ? $this->toString(true, true, 1) : $this->tag);
	}

	public function __get($attribute) {
		return $this->getAttribute($attribute);
	}

	public function __set($attribute, $value) {
		$this->setAttribute($attribute, $value);
	}

	public function __isset($attribute) {
		return $this->hasAttribute($attribute);
	}

	public function __unset($attribute) {
		return $this->deleteAttribute($attribute);
	}

	public function __invoke($query = '*', $index = false, $recursive = true, $check_self = false) {
		return $this->select($query, $index, $recursive, $check_self);
	}

	public function dumpLocation() {
		return (($this->parent) ? (($p = $this->parent->dumpLocation()) ? $p.' > ' : '').$this->tag.'('.$this->typeIndex().')' : '');
	}

	protected function toString_attributes() {
		$s = '';
		foreach($this->attributes as $a => $v) {
			$s .= ' '.$a.(((!$this->attribute_shorttag) || ($this->attributes[$a] !== $a)) ? '="'.htmlspecialchars($this->attributes[$a], ENT_QUOTES, '', false).'"' : '');
		}
		return $s;
	}

	protected function toString_content($attributes = true, $recursive = true, $content_only = false) {
		$s = '';
		foreach($this->children as $c) {
			$s .= $c->toString($attributes, $recursive, $content_only);
		}
		return $s;
	}

	public function toString($attributes = true, $recursive = true, $content_only = false) {
		if ($content_only) {
			if (is_int($content_only)) {
				--$content_only;
			}
			return $this->toString_content($attributes, $recursive, $content_only);
		}
		$s = '<'.$this->tag;
		if ($attributes) {
			$s .= $this->toString_attributes();
		}
		if ($this->self_close) {
			$s .= $this->self_close_str.'>';
		} else {
			$s .= '>';
			if($recursive) {
				$s .= $this->toString_content($attributes);
			}
			$s .= '</'.$this->tag.'>';
		}
		return $s;
	}

	public function getOuterText() {
		return html_entity_decode($this->toString(), ENT_QUOTES);
	}

	public function setOuterText($text, $parser = null) {
		if (trim($text)) {
			$index = $this->index();
			if ($parser === null) {
				$parser = new $this->parserClass();
			}
			$parser->setDoc($text);
			$parser->parse_all();
			$parser->root->moveChildren($this->parent, $index);
		}
		$this->delete();
		return (($parser && $parser->errors) ? $parser->errors : true);
	}

	public function html() {
		return $this->toString();
	}

	public function getInnerText() {
		return html_entity_decode($this->toString(true, true, 1), ENT_QUOTES);
	}

	public function setInnerText($text, $parser = null) {
		$this->clear();
		if (trim($text)) {
			if ($parser === null) {
				$parser = new $this->parserClass();
			}
			$parser->root =& $this;
			$parser->setDoc($text);
			$parser->parse_all();
		}
		return (($parser && $parser->errors) ? $parser->errors : true);
	}

	public function getPlainText() {
		return preg_replace('`\s+`', ' ', html_entity_decode($this->toString(true, true, true), ENT_QUOTES));
	}

	public function getPlainTextUTF8() {
		$txt = $this->getPlainText();
		$enc = $this->getEncoding();
		if ($enc !== false) {
			$txt = mb_convert_encoding($txt, "UTF-8", $enc);
		}
		return $txt;
	}

	public function setPlainText($text) {
		$this->clear();
		if (trim($text)) {
			$this->addText(htmlentities($text, ENT_QUOTES));
		}
	}

	public function delete() {
		if (($p = $this->parent) !== null) {
			$this->parent = null;
			$p->deleteChild($this);
		} else {
			$this->clear();
		}
	}

	public function detach($move_children_up = false) {
		if (($p = $this->parent) !== null) {
			$index = $this->index();
			$this->parent = null;
			if ($move_children_up) {
				$this->moveChildren($p, $index);
			}
			$p->deleteChild($this, true);
		}
	}

	public function clear() {
		foreach($this->children as $c) {
			$c->parent = null;
			$c->delete();
		}
		$this->children = array();
	}

	public function getRoot() {
		$r = $this->parent;
		$n = ($r === null) ? null : $r->parent;
		while ($n !== null) {
			$r = $n;
			$n = $r->parent;
		}
		return $r;
	}

	public function changeParent($to, &$index = null) {
		if ($this->parent !== null) {
			$this->parent->deleteChild($this, true);
		}
		$this->parent = $to;
		if ($index !== false) {
			$new_index = $this->index();
			if (!(is_int($new_index) && ($new_index >= 0))) {
				$this->parent->addChild($this, $index);
			}
		}
	}

	public function hasParent($tag = null, $recursive = false) {
		if ($this->parent !== null) {
			if ($tag === null) {
				return true;
			} elseif (is_string($tag)) {
				return (($this->parent->tag === $tag) || ($recursive && $this->parent->hasParent($tag)));
			} elseif (is_object($tag)) {
				return (($this->parent === $tag) || ($recursive && $this->parent->hasParent($tag)));
			}
		}
		return false;
	}

	public function isParent($tag, $recursive = false) {
		return ($this->hasParent($tag, $recursive) === ($tag !== null));
	}

	public function isText() {
		return false;
	}

	public function isComment() {
		return false;
	}

	public function isTextOrComment() {
		return false;
	}

	public function move($to, &$new_index = -1) {
		$this->changeParent($to, $new_index);
	}

	public function moveChildren($to, &$new_index = -1, $start = 0, $end = -1) {
		if ($end < 0) {
			$end += count($this->children);
		}
		for ($i = $start; $i <= $end; $i++) {
			$this->children[$start]->changeParent($to, $new_index);
		}
	}

	public function index($count_all = true) {
		if (!$this->parent) {
			return -1;
		} elseif ($count_all) {
			return $this->parent->findChild($this);
		} else{
			$index = -1;
			foreach(array_keys($this->parent->children) as $k) {
				if (!$this->parent->children[$k]->isTextOrComment()) {
					++$index;
				}
				if ($this->parent->children[$k] === $this) {
					return $index;
				}
			}
			return -1;
		}
	}

	public function setIndex($index) {
		if ($this->parent) {
			if ($index > $this->index()) {
				--$index;
			}
			$this->delete();
			$this->parent->addChild($this, $index);
		}
	}

	public function typeIndex() {
		if (!$this->parent) {
			return -1;
		} else {
			$index = -1;
			foreach(array_keys($this->parent->children) as $k) {
				if (strcasecmp($this->tag, $this->parent->children[$k]->tag) === 0) {
					++$index;
				}
				if ($this->parent->children[$k] === $this) {
					return $index;
				}
			}
			return -1;
		}
	}

	public function indent() {
		return (($this->parent) ? $this->parent->indent() + 1 : -1);
	}

	public function getSibling($offset = 1) {
		$index = $this->index() + $offset;
		if (($index >= 0) && ($index < $this->parent->childCount())) {
			return $this->parent->getChild($index);
		} else {
			return null;
		}
	}

	public function getNextSibling($skip_text_comments = true) {
		$offset = 1;
		while (($n = $this->getSibling($offset)) !== null) {
			if ($skip_text_comments && ($n->tag[0] === '~')) {
				++$offset;
			} else {
				break;
			}
		}
		return $n;
	}

	public function getPreviousSibling($skip_text_comments = true) {
		$offset = -1;
		while (($n = $this->getSibling($offset)) !== null) {
			if ($skip_text_comments && ($n->tag[0] === '~')) {
				--$offset;
			} else {
				break;
			}
		}
		return $n;
	}

	public function getNamespace() {
		if ($this->tag_ns === null) {
			$a = explode(':', $this->tag, 2);
			if (empty($a[1])) {
				$this->tag_ns = array('', $a[0]);
			} else {
				$this->tag_ns = array($a[0], $a[1]);
			}
		}
		return $this->tag_ns[0];
	}
	public function setNamespace($ns) {
		if ($this->getNamespace() !== $ns) {
			$this->tag_ns[0] = $ns;
			$this->tag = $ns.':'.$this->tag_ns[1];
		}
	}

	public function getTag() {
		if ($this->tag_ns === null) {
			$this->getNamespace();
		}
		return $this->tag_ns[1];
	}

	public function setTag($tag, $with_ns = false) {
		$with_ns = $with_ns || (strpos($tag, ':') !== false);
		if ($with_ns) {
			$this->tag = $tag;
			$this->tag_ns = null;
		} elseif ($this->getTag() !== $tag) {
			$this->tag_ns[1] = $tag;
			$this->tag = (($this->tag_ns[0]) ? $this->tag_ns[0].':' : '').$tag;
		}
	}

	public function getEncoding() {
		$root = $this->getRoot();
		if ($root !== null) {
			if ($enc = $root->select('meta[charset]', 0, true, true)) {
				return $enc->getAttribute("charset");
			} elseif ($enc = $root->select('"?xml"[encoding]', 0, true, true)) {
				return $enc->getAttribute("encoding");
			} elseif ($enc = $root->select('meta[content*="charset="]', 0, true, true)) {
				$enc = $enc->getAttribute("content");
				return substr($enc, strpos($enc, "charset=")+8);
			}
		}
		return false;
	}

	public function childCount($ignore_text_comments = false) {
		if (!$ignore_text_comments) {
			return count($this->children);
		} else{
			$count = 0;
			foreach(array_keys($this->children) as $k) {
				if (!$this->children[$k]->isTextOrComment()) {
					++$count;
				}
			}
			return $count;
		}
	}

	public function findChild($child) {
		return array_search($child, $this->children, true);
	}

	public function hasChild($child) {
		return ((bool) $this->findChild($child));
	}

	public function &getChild($child, $ignore_text_comments = false) {
		if (!is_int($child)) {
			$child = $this->findChild($child);
		} elseif ($child < 0) {
			$child += $this->childCount($ignore_text_comments);
		}
		if ($ignore_text_comments) {
			$count = 0;
			$last = null;
			foreach(array_keys($this->children) as $k) {
				if (!$this->children[$k]->isTextOrComment()) {
					if ($count++ === $child) {
						return $this->children[$k];
					}
					$last = $this->children[$k];
				}
			}
			return (($child > $count) ? $last : null);
		} else {
			return $this->children[$child];
		}
	}

	public function &addChild($tag, &$offset = null) {
		if (!is_object($tag)) {
			$tag = new $this->childClass($tag, $this);
		} elseif ($tag->parent !== $this) {
			$index = false;
			$tag->changeParent($this, $index);
		}
		if (is_int($offset) && ($offset < count($this->children)) && ($offset !== -1)) {
			if ($offset < 0) {
				$offset += count($this->children);
			}
			array_splice($this->children, $offset++, 0, array(&$tag));
		} else {
			$this->children[] =& $tag;
		}
		return $tag;
	}

	public function &firstChild($ignore_text_comments = false) {
		return $this->getChild(0, $ignore_text_comments);
	}

	public function &lastChild($ignore_text_comments = false) {
		return $this->getChild(-1, $ignore_text_comments);
	}

	public function &insertChild($tag, $index) {
		return $this->addChild($tag, $index);
	}

	public function &addText($text, &$offset = null) {
		return $this->addChild(new $this->childClass_Text($this, $text), $offset);
	}

	public function &addComment($text, &$offset = null) {
		return $this->addChild(new $this->childClass_Comment($this, $text), $offset);
	}

	public function &addConditional($condition, $hidden = true, &$offset = null) {
		return $this->addChild(new $this->childClass_Conditional($this, $condition, $hidden), $offset);
	}

	public function &addCDATA($text, &$offset = null) {
		return $this->addChild(new $this->childClass_CDATA($this, $text), $offset);
	}

	public function &addDoctype($dtd, &$offset = null) {
		return $this->addChild(new $this->childClass_Doctype($this, $dtd), $offset);
	}

	public function &addXML($tag = 'xml', $text = '', $attributes = array(), &$offset = null) {
		return $this->addChild(new $this->childClass_XML($this, $tag, $text, $attributes), $offset);
	}

	public function &addASP($tag = '', $text = '', $attributes = array(), &$offset = null) {
		return $this->addChild(new $this->childClass_ASP($this, $tag, $text, $attributes), $offset);
	}

	public function deleteChild($child, $soft_delete = false) {
		if (is_object($child)) {
			$child = $this->findChild($child);
		} elseif ($child < 0) {
			$child += count($this->children);
		}
		if (!$soft_delete) {
			$this->children[$child]->delete();
		}
		unset($this->children[$child]);
		$tmp = array();
		foreach(array_keys($this->children) as $k) {
			$tmp[] =& $this->children[$k];
		}
		$this->children = $tmp;
	}

	public function wrap($node, $wrap_index = -1, $node_index = null) {
		if ($node_index === null) {
			$node_index = $this->index();
		}
		if (!is_object($node)) {
			$node = $this->parent->addChild($node, $node_index);
		} elseif ($node->parent !== $this->parent) {
			$node->changeParent($this->parent, $node_index);
		}
		$this->changeParent($node, $wrap_index);
		return $node;
	}

	public function wrapInner($node, $start = 0, $end = -1, $wrap_index = -1, $node_index = null) {
		if ($end < 0) {
			$end += count($this->children);
		}
		if ($node_index === null) {
			$node_index = $end + 1;
		}
		if (!is_object($node)) {
			$node = $this->addChild($node, $node_index);
		} elseif ($node->parent !== $this) {
			$node->changeParent($this->parent, $node_index);
		}
		$this->moveChildren($node, $wrap_index, $start, $end);
		return $node;
	}

	public function attributeCount() {
		return count($this->attributes);
	}

	protected function findAttribute($attr, $compare = 'total', $case_sensitive = false) {
		if (is_int($attr)) {
			if ($attr < 0) {
				$attr += count($this->attributes);
			}
			$keys = array_keys($this->attributes);
			return $this->findAttribute($keys[$attr], 'total', true);
		} else if ($compare === 'total') {
			$b = explode(':', $attr, 2);
			if ($case_sensitive) {
				$t =& $this->attributes;
			} else {
				$t = array_change_key_case($this->attributes);
				$attr = strtolower($attr);
			}
			if (isset($t[$attr])) {
				$index = 0;
				foreach($this->attributes as $a => $v) {
					if (($v === $t[$attr]) && (strcasecmp($a, $attr) === 0)) {
						$attr = $a;
						$b = explode(':', $attr, 2);
						break;
					}
					++$index;
				}
				if (empty($b[1])) {
					return array(array('', $b[0], $attr, $index));
				} else {
					return array(array($b[0], $b[1], $attr, $index));
				}
			} else {
				return false;
			}
		} else {
			if ($this->attributes_ns === null) {
				$index = 0;
				foreach($this->attributes as $a => $v) {
					$b = explode(':', $a, 2);
					if (empty($b[1])) {
						$this->attributes_ns[$b[0]][] = array('', $b[0], $a, $index);
					} else {
						$this->attributes_ns[$b[1]][] = array($b[0], $b[1], $a, $index);
					}
					++$index;
				}
			}
			if ($case_sensitive) {
				$t =& $this->attributes_ns;
			} else {
				$t = array_change_key_case($this->attributes_ns);
				$attr = strtolower($attr);
			}
			if ($compare === 'namespace') {
				$res = array();
				foreach($t as $ar) {
					foreach($ar as $a) {
						if ($a[0] === $attr) {
							$res[] = $a;
						}
					}
				}
				return $res;
			} elseif ($compare === 'name') {
				return ((isset($t[$attr])) ? $t[$attr] : false);
			} else {
				trigger_error('Unknown comparison mode');
			}
		}
	}

	public function hasAttribute($attr, $compare = 'total', $case_sensitive = false) {
		return ((bool) $this->findAttribute($attr, $compare, $case_sensitive));
	}

	public function getAttributeNS($attr, $compare = 'name', $case_sensitive = false) {
		$f = $this->findAttribute($attr, $compare, $case_sensitive);
		if (is_array($f) && $f) {
			if (count($f) === 1) {
				return $this->attributes[$f[0][0]];
			} else {
				$res = array();
				foreach($f as $a) {
					$res[] = $a[0];
				}
				return $res;
			}
		} else {
			return false;
		}
	}

	public function setAttributeNS($attr, $namespace, $compare = 'name', $case_sensitive = false) {
		$f = $this->findAttribute($attr, $compare, $case_sensitive);
		if (is_array($f) && $f) {
			if ($namespace) {
				$namespace .= ':';
			}
			foreach($f as $a) {
				$val = $this->attributes[$a[2]];
				unset($this->attributes[$a[2]]);
				$this->attributes[$namespace.$a[1]] = $val;
			}
			$this->attributes_ns = null;
			return true;
		} else {
			return false;
		}
	}

	public function getAttribute($attr, $compare = 'total', $case_sensitive = false) {
		$f = $this->findAttribute($attr, $compare, $case_sensitive);
		if (is_array($f) && $f){
			if (count($f) === 1) {
				return $this->attributes[$f[0][2]];
			} else {
				$res = array();
				foreach($f as $a) {
					$res[] = $this->attributes[$a[2]];
				}
				return $res;
			}
		} else {
			return null;
		}
	}

	public function setAttribute($attr, $val, $compare = 'total', $case_sensitive = false) {
		if ($val === null) {
			return $this->deleteAttribute($attr, $compare, $case_sensitive);
		}
		$f = $this->findAttribute($attr, $compare, $case_sensitive);
		if (is_array($f) && $f) {
			foreach($f as $a) {
				$this->attributes[$a[2]] = (string) $val;
			}
		} else {
			$this->attributes[$attr] = (string) $val;
		}
	}

	public function addAttribute($attr, $val) {
		$this->setAttribute($attr, $val, 'total', true);
	}

	public function deleteAttribute($attr, $compare = 'total', $case_sensitive = false) {
		$f = $this->findAttribute($attr, $compare, $case_sensitive);
		if (is_array($f) && $f) {
			foreach($f as $a) {
				unset($this->attributes[$a[2]]);
				if ($this->attributes_ns !== null) {
					unset($this->attributes_ns[$a[1]]);
				}
			}
		}
	}

	public function hasClass($className) {
		return ($className && preg_match('`\b'.preg_quote($className).'\b`si', $class = $this->class));
	}

	public function addClass($className) {
		if (!is_array($className)) {
			$className = array($className);
		}
		$class = $this->class;
		foreach ($className as $c) {
			if (!(preg_match('`\b'.preg_quote($c).'\b`si', $class) > 0)) {
				$class .= ' '.$c;
			}
		}
		$this->class = $class;
	}

	public function removeClass($className) {
		if (!is_array($className)) {
			$className = array($className);
		}
		$class = $this->class;
		foreach ($className as $c) {
			$class = reg_replace('`\b'.preg_quote($c).'\b`si', '', $class);
		}
		if ($class) {
			$this->class = $class;
		} else {
			unset($this->class);
		}
	}

	public function getChildrenByCallback($callback, $recursive = true, $check_self = false) {
		$count = $this->childCount();
		if ($check_self && $callback($this)) {
			$res = array($this);
		} else {
			$res = array();
		}
		if ($count > 0) {
			if (is_int($recursive)) {
				$recursive = (($recursive > 1) ? $recursive - 1 : false);
			}
			for ($i = 0; $i < $count; $i++) {
				if ($callback($this->children[$i])) {
					$res[] = $this->children[$i];
				}
				if ($recursive) {
					$res = array_merge($res, $this->children[$i]->getChildrenByCallback($callback, $recursive));
				}
			}
		}
		return $res;
	}

	public function getChildrenByMatch($conditions, $recursive = true, $check_self = false, $custom_filters = array()) {
		$count = $this->childCount();
		if ($check_self && $this->match($conditions, true, $custom_filters)) {
			$res = array($this);
		} else {
			$res = array();
		}
		if ($count > 0) {
			if (is_int($recursive)) {
				$recursive = (($recursive > 1) ? $recursive - 1 : false);
			}
			for ($i = 0; $i < $count; $i++) {
				if ($this->children[$i]->match($conditions, true, $custom_filters)) {
					$res[] = $this->children[$i];
				}
				if ($recursive) {
					$res = array_merge($res, $this->children[$i]->getChildrenByMatch($conditions, $recursive, false, $custom_filters));
				}
			}
		}
		return $res;
	}

	protected function match_tags($tags) {
		$res = false;
		foreach($tags as $tag => $match) {
			if (!is_array($match)) {
				$match = array(
					'match' => $match,
					'operator' => 'or',
					'compare' => 'total',
					'case_sensitive' => false
				);
			} else {
				if (is_int($tag)) {
					$tag = $match['tag'];
				}
				if (!isset($match['match'])) {
					$match['match'] = true;
				}
				if (!isset($match['operator'])) {
					$match['operator'] = 'or';
				}
				if (!isset($match['compare'])) {
					$match['compare'] = 'total';
				}
				if (!isset($match['case_sensitive'])) {
					$match['case_sensitive'] = false;
				}
			}
			if (($match['operator'] === 'and') && (!$res)) {
				return false;
			} elseif (!($res && ($match['operator'] === 'or'))) {
				if ($match['compare'] === 'total') {
					$a = $this->tag;
				} elseif ($match['compare'] === 'namespace') {
					$a = $this->getNamespace();
				} elseif ($match['compare'] === 'name') {
					$a = $this->getTag();
				}
				if ($match['case_sensitive']) {
					$res = (($a === $tag) === $match['match']);
				} else {
					$res = ((strcasecmp($a, $tag) === 0) === $match['match']);
				}
			}
		}
		return $res;
	}

	protected function match_attributes($attributes) {
		$res = false;
		foreach($attributes as $attribute => $match) {
			if (!is_array($match)) {
				$match = array(
					'operator_value' => 'equals',
					'value' => $match,
					'match' => true,
					'operator_result' => 'or',
					'compare' => 'total',
					'case_sensitive' => false
				);
			} else {
				if (is_int($attribute)) {
					$attribute = $match['attribute'];
				}
				if (!isset($match['match'])) {
					$match['match'] = true;
				}
				if (!isset($match['operator_result'])) {
					$match['operator_result'] = 'or';
				}
				if (!isset($match['compare'])) {
					$match['compare'] = 'total';
				}
				if (!isset($match['case_sensitive'])) {
					$match['case_sensitive'] = false;
				}
			}
			if (is_string($match['value']) && (!$match['case_sensitive'])) {
				$match['value'] = strtolower($match['value']);
			}
			if (($match['operator_result'] === 'and') && (!$res)) {
				return false;
			} elseif (!($res && ($match['operator_result'] === 'or'))) {
				$possibles = $this->findAttribute($attribute, $match['compare'], $match['case_sensitive']);
				$has = (is_array($possibles) && $possibles);
				$res = (($match['value'] === $has) || (($match['match'] === false) && ($has === $match['match'])));
				if ((!$res) && $has && is_string($match['value'])) {
					foreach($possibles as $a) {
						$val = $this->attributes[$a[2]];
						if (is_string($val) && (!$match['case_sensitive'])) {
							$val = strtolower($val);
						}
						switch($match['operator_value']) {
							case '%=':
							case 'contains_regex':
								$res = ((preg_match('`'.$match['value'].'`s', $val) > 0) === $match['match']);
								if ($res) break 1; else break 2;
							case '|=':
							case 'contains_prefix':
								$res = ((preg_match('`\b'.preg_quote($match['value']).'[\-\s]?`s', $val) > 0) === $match['match']);
								if ($res) break 1; else break 2;
							case '~=':
							case 'contains_word':
								$res = ((preg_match('`\b'.preg_quote($match['value']).'\b`s', $val) > 0) === $match['match']);
								if ($res) break 1; else break 2;
							case '*=':
							case 'contains':
								$res = ((strpos($val, $match['value']) !== false) === $match['match']);
								if ($res) break 1; else break 2;
							case '$=':
							case 'ends_with':
								$res = ((substr($val, -strlen($match['value'])) === $match['value']) === $match['match']);
								if ($res) break 1; else break 2;
							case '^=':
							case 'starts_with':
								$res = ((substr($val, 0, strlen($match['value'])) === $match['value']) === $match['match']);
								if ($res) break 1; else break 2;
							case '!=':
							case 'not_equal':
								$res = (($val !== $match['value']) === $match['match']);
								if ($res) break 1; else break 2;
							case '=':
							case 'equals':
								$res = (($val === $match['value']) === $match['match']);
								if ($res) break 1; else break 2;
							case '>=':
							case 'bigger_than':
								$res = (($val >= $match['value']) === $match['match']);
								if ($res) break 1; else break 2;
							case '<=':
							case 'smaller_than':
								$res = (($val >= $match['value']) === $match['match']);
								if ($res) break 1; else break 2;
							default:
								trigger_error('Unknown operator "'.$match['operator_value'].'" to match attributes!');
								return false;
						}
					}
				}
			}
		}
		return $res;
	}

	protected function match_filters($conditions, $custom_filters = array()) {
		foreach($conditions as $c) {
			$c['filter'] = strtolower($c['filter']);
			if (isset($this->filter_map[$c['filter']])) {
				if (!$this->{$this->filter_map[$c['filter']]}($c['params'])) {
					return false;
				}
			} elseif (isset($custom_filters[$c['filter']])) {
				if (!call_user_func($custom_filters[$c['filter']], $this, $c['params'])) {
					return false;
				}
			} else {
				trigger_error('Unknown filter "'.$c['filter'].'"!');
				return false;
			}
		}
		return true;
	}

	public function match($conditions, $match = true, $custom_filters = array()) {
		$t = isset($conditions['tags']);
		$a = isset($conditions['attributes']);
		$f = isset($conditions['filters']);
		if (!($t || $a || $f)) {
			if (is_array($conditions) && $conditions) {
				foreach($conditions as $c) {
					if ($this->match($c, $match)) {
						return true;
					}
				}
			}
			return false;
		} else {
			if (($t && (!$this->match_tags($conditions['tags']))) === $match) {
				return false;
			}
			if (($a && (!$this->match_attributes($conditions['attributes']))) === $match) {
				return false;
			}
			if (($f && (!$this->match_filters($conditions['filters'], $custom_filters))) === $match) {
				return false;
			}
			return true;
		}
	}

	public function getChildrenByAttribute($attribute, $value, $mode = 'equals', $compare = 'total', $recursive = true) {
		if ($this->childCount() < 1) {
			return array();
		}
		$mode = explode(' ', strtolower($mode));
		$match = ((isset($mode[1]) && ($mode[1] === 'not')) ? 'false' : 'true');
		return $this->getChildrenByMatch(
			array(
				'attributes' => array(
					$attribute => array(
						'operator_value' => $mode[0],
						'value' => $value,
						'match' => $match,
						'compare' => $compare
					)
				)
			),
			$recursive
		);
	}

	public function getChildrenByTag($tag, $compare = 'total', $recursive = true) {
		if ($this->childCount() < 1) {
			return array();
		}
		$tag = explode(' ', strtolower($tag));
		$match = ((isset($tag[1]) && ($tag[1] === 'not')) ? 'false' : 'true');
		return $this->getChildrenByMatch(
			array(
				'tags' => array(
					$tag[0] => array(
						'match' => $match,
						'compare' => $compare
					)
				)
			),
			$recursive
		);
	}

	public function getChildrenByID($id, $recursive = true) {
		return $this->getChildrenByAttribute('id', $id, 'equals', 'total', $recursive);
	}

	public function getChildrenByClass($class, $recursive = true) {
		return $this->getChildrenByAttribute('class', $class, 'equals', 'total', $recursive);
	}

	public function getChildrenByName($name, $recursive = true) {
		return $this->getChildrenByAttribute('name', $name, 'equals', 'total', $recursive);
	}

	public function select($query = '*', $index = false, $recursive = true, $check_self = false) {
		$s = new $this->selectClass($this, $query, $check_self, $recursive);
		$res = $s->result;
		unset($s);
		if (is_array($res) && ($index === true) && (count($res) === 1)) {
			return $res[0];
		} elseif (is_int($index) && is_array($res)) {
			if ($index < 0) {
				$index += count($res);
			}
			return ($index < count($res)) ? $res[$index] : null;
		} else {
			return $res;
		}
	}

	protected function filter_root() {
		return (strtolower($this->tag) === 'html');
	}

	protected function filter_nchild($n) {
		return ($this->index(false) === (int) $n);
	}

	protected function filter_gt($n) {
		return ($this->index(false) > (int) $n);
	}

	protected function filter_lt($n) {
		return ($this->index(false) < (int) $n);
	}

	protected function filter_nlastchild($n) {
		if ($this->parent === null) {
			return false;
		} else {
			return ($this->parent->childCount(true) - 1 - $this->index(false) === (int) $n);
		}
	}

	protected function filter_ntype($n) {
		return ($this->typeIndex() === (int) $n);
	}

	protected function filter_nlastype($n) {
		if ($this->parent === null) {
			return false;
		} else {
			return (count($this->parent->getChildrenByTag($this->tag, 'total', false)) - 1 - $this->typeIndex() === (int) $n);
		}
	}

	protected function filter_odd() {
		return (($this->index(false) & 1) === 1);
	}

	protected function filter_even() {
		return (($this->index(false) & 1) === 0);
	}

	protected function filter_every($n) {
		return (($this->index(false) % (int) $n) === 0);
	}

	protected function filter_first() {
		return ($this->index(false) === 0);
	}

	protected function filter_last() {
		if ($this->parent === null) {
			return false;
		} else {
			return ($this->parent->childCount(true) - 1 === $this->index(false));
		}
	}

	protected function filter_firsttype() {
		return ($this->typeIndex() === 0);
	}

	protected function filter_lasttype() {
		if ($this->parent === null) {
			return false;
		} else {
			return (count($this->parent->getChildrenByTag($this->tag, 'total', false)) - 1 === $this->typeIndex());
		}
	}

	protected function filter_onlychild() {
		if ($this->parent === null) {
			return false;
		} else {
			return ($this->parent->childCount(true) === 1);
		}
	}

	protected function filter_onlytype() {
		if ($this->parent === null) {
			return false;
		} else {
			return (count($this->parent->getChildrenByTag($this->tag, 'total', false)) === 1);
		}
	}

	protected function filter_empty() {
		return ($this->childCount() === 0);
	}

	protected function filter_notempty() {
		return ($this->childCount() !== 0);
	}

	protected function filter_hastext() {
		return ($this->getPlainText() !== '');
	}

	protected function filter_notext() {
		return ($this->getPlainText() === '');
	}

	protected function filter_lang($lang) {
		return ($this->lang === $lang);
	}

	protected function filter_contains($text) {
		return (strpos($this->getPlainText(), $text) !== false);
	}

	protected function filter_has($selector) {
		$s = $this->select((string) $selector, false);
		return (is_array($s) && (count($s) > 0));
	}

	protected function filter_not($selector) {
		$s = $this->select((string) $selector, false, true, true);
		return ((!is_array($s)) || (array_search($this, $s, true) === false));
	}

	protected function filter_element() {
		return true;
	}

	protected function filter_text() {
		return false;
	}

	protected function filter_comment() {
		return false;
	}
}