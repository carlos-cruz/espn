<?php namespace Espn; 

/**
 * 
 */
class Team
{

	public $name;
	public $logo;
	
	function __construct(String $name)
	{
		$this->name = $name;
	}
}

?>