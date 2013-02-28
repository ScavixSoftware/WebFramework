<?

/**
 * @attribute[Resource('marked.js')]
 */
class Preview extends HtmlPage
{
	function Init()
	{
		$this->Linked(false);
	}
	
	/**
	 * @attribute[RequestParam('f','string')]
	 */
	function Linked($f)
	{
		if( $f )
			$md = file_get_contents(__DIR__."/out/$f.md");
		else 
			$md = "# Scavix WDF Home
- [Alphabetical function listing](functions)
- [Alphabetical class listing](classes)
- [Inheritance tree](inheritance)
- [Interfaces](interfaces)
- [Folder tree](foldertree)";
		
		$q = buildQuery('Preview','Linked');
		$s  = "$('.markdown-body').html(marked(".json_encode($md)."));";
		$s .= "$('.markdown-body a[id]').each(function(){ $(this).attr('id','wiki-'+$(this).attr('id')); });";
		$s .= "$('.markdown-body a[href]').each(function(){ if( $(this).attr('href').match(/^http/)) return; $(this).attr('href','$q?f='+$(this).attr('href')); });";
		$s .= "$('.markdown-body a[id='+location.hash.substr(1)+']').get(0).scrollIntoView();";
		
		$this->addDocReady("$s");
	}
}
