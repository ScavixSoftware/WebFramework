<?

class Blog extends HtmlPage
{
	function __initialize()
	{
		parent::__initialize();
		$ds = model_datasource('system');
		$ds->ExecuteSql("CREATE TABLE IF NOT EXISTS blog(id INTEGER,title VARCHAR(50),body TEXT,PRIMARY KEY(id))");
		
		$this->content(new Anchor( buildQuery('blog','newpost'), 'New post' ));
	}
	
	function Index()
	{
		$ds = model_datasource('system');
		foreach( $ds->Query('blog')->orderBy('id','desc') as $post )
		{
			$tpl = Template::Make('post')
				->set('title',$post->title)
				->set('body',$post->body);
			$this->content($tpl);
		}
	}
	
	function NewPost()
	{
		log_debug("New Post");
		$this->content( Template::Make('newpostform') );
	}
	
	/**
	 * @attribute[RequestParam('title','string')]
	 * @attribute[RequestParam('body','string')]
	 */
	function AddPost($title,$body)
	{
		log_debug("Add Post");
		$ds = model_datasource('system');
		$ds->ExecuteSql("INSERT INTO blog(title,body)VALUES(?,?)",array($title,$body));
		redirect('blog','index');
	}
}