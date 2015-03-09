<?php

/**
 * CREATE A BEAN FILE.
 *
 * @param name=beanName
 */

define("CRT_ROOT_DIRECTORY", realpath(dirname(__FILE__).'/..'));
define("CRT_APPLICATION_DIRECTORY", CRT_ROOT_DIRECTORY.'/application');
define("CRT_MODEL_DIRECTORY", CRT_APPLICATION_DIRECTORY.'/model');

// retrieve parameter
$beanName = $database = $attributes = NULL;
foreach($argv as $arg) {
	if(strpos($arg, 'name=') === 0) {
		$beanName = ucfirst(substr($arg, 5));
	}
	if(strpos($arg, 'database=') === 0) {
		$database = substr($arg, 9);
	}
	if(strpos($arg, 'attributes=') === 0) {
		$attributes = explode(',', substr($arg, 11));
	}
}
if(empty($beanName) or empty($attributes) or empty($database)) {
	echo showUsage();
	exit;
}

// Create shell file
$file = CRT_MODEL_DIRECTORY."/".$beanName.".shell.php";

$attributesDefinition = '';
$attributesGetterSetter = '';

foreach($attributes as $attribute) {
	// Generate definition
	$attributesDefinition .= '	protected $'.$attribute.';
';

	// Generate Getter Setter
	$attributesGetterSetter .= '	public function get'.ucfirst($attribute).'() {
		return $this->'.$attribute.';
	}

	public function set'.ucfirst($attribute).'($'.$attribute.') {
		$this->markModified(\''.$attribute.'\');
		$this->'.$attribute.' = $'.$attribute.';
	}

';
}

echo "\n  ->  Create file $file";
file_put_contents($file, '<?php
/**
 * This bean shell has been generated by CRT tools using :
 * '.implode(' ', $argv).'
 * !!! Do not edit this file manually !!!
 *
 * Sincerely,
 * CRT
 */
class '.$beanName.'Shell extends Bean {

	protected $database = "'.$database.'";

'.$attributesDefinition.'
'.$attributesGetterSetter.'}
');

// Create empty bean file
$file = CRT_MODEL_DIRECTORY."/".$beanName.".bean.php";

echo "\n  ->  Create file $file\n\n";
file_put_contents($file, '<?php
class '.$beanName.' extends '.$beanName.'Shell {

	// Insert bean code here !

}
');

function showUsage() {
	return "\nUsage: php bean.php name=<beanName> attributes=<attributeList,> database=<databaseName>\n\n";
}
