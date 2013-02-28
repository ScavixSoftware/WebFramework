<?

/**
 * Specifies that a resource file is needed.
 * 
 * Note this above a class to force the resouce to be automatically searched and added the resulting output.
 * <code>
 * <at>attribute[Resource('myownscript.js')]
 * <at>attribute[Resource('ineed/thisstyle.css')]
 * </code>
 */
class ResourceAttribute extends System_Attribute
{
	var $Path;
	
	function __construct($path)
	{
		$this->Path = $path;
	}
	
	/**
	 * Resolves this resource to a URL
	 * 
	 * Will call <resFile>() to do so, so result will be callable from the current location.
	 * @return string URL to resource
	 */
	function Resolve()
	{
		return resFile($this->Path);
	}
	
	/**
	 * Will collect all Resource attributes from a given classname
	 * 
	 * Will also step down the inheritance graph to collect Resources from there.
	 * @param string|object $classname Classname or object to collect resources for
	 * @return array Array of resource attributes
	 */
	public static function Collect($classname)
	{
		$ref = System_Reflector::GetInstance($classname);
		$attrs = $ref->GetClassAttributes(array('Resource','ExternalResource'));
		$ref = $ref->getParentClass();
		$parents = $ref?self::Collect($ref->getName()):array();
		$attrs = array_merge($parents,$attrs);
		return $attrs;
	}
	
	/**
	 * Resolves an array of ResourceAttributes
	 * 
	 * Calls <ResourceAttribute::Resolve>() for each and returns an array of resolved URLs
	 * @param array $array_of_res_attr Resources to be resolved
	 * @return array An array of URLs to the resources
	 */
	public static function ResolveAll($array_of_res_attr)
	{
		$res = array();
		foreach( $array_of_res_attr as $a )
			$res[] = $a->Resolve();
		return array_unique($res);
	}
}
