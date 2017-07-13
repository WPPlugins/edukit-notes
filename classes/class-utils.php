<?php


class ekNotesUtils
{
	
	static function formatNote($noteContent)
	{
		$noteContent = stripslashes($noteContent);
		$noteContent = convert_chars($noteContent);
		$noteContent = wptexturize($noteContent);		
		return $noteContent;
	}
	
	static function validateInputNumber($input)
	{
		$output="";
		if(is_numeric($input)){$output = $input;}
		return $output;
	}
	


}
?>