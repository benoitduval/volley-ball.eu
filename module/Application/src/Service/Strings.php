<?php

namespace Application\Service;

class Strings
{
	public static function skipAccent($string)
	{
	    $str = strtolower($string);
	    $str = htmlentities($str, ENT_NOQUOTES, 'utf-8');
	    $str = preg_replace('#&([A-za-z])(?:acute|cedil|caron|circ|grave|orn|ring|slash|th|tilde|uml);#', '\1', $str);
	    $str = preg_replace('#&([A-za-z]{2})(?:lig);#', '\1', $str); // pour les ligatures e.g. '&oelig;'
	    $str = preg_replace('#&[^;]+;#', '', $str); // supprime les autres caractères
	    return $str;
	}
}