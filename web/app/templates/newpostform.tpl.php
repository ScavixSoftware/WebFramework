<form action="<?=buildQuery('blog','addpost')?>" method="post">
	Title: <input type="text" name="title"/><br/>
	Text: <textarea name="body"></textarea><br/>
	<input type="submit" value="Create post"/>
</form>