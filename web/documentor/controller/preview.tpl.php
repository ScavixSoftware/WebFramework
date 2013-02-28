<div style="text-align: center; font-size: 18px; font-weight: bold">
	<a href="javascript: history.back();">Back</a>
	&nbsp;&nbsp;&nbsp;
	<a href="<?=buildQuery('DocMain')?>">Close preview</a>
</div>
<div style="text-align: center; font-size: 18px; font-weight: bold">
	<a href="<?=buildQuery('Preview','Linked','f=functions')?>">Functions</a>
	&nbsp;&nbsp;
	<a href="<?=buildQuery('Preview','Linked','f=classes')?>">Classes</a>
	&nbsp;&nbsp;
	<a href="<?=buildQuery('Preview','Linked','f=inheritance')?>">Inheritance</a>
	&nbsp;&nbsp;
	<a href="<?=buildQuery('Preview','Linked','f=interfaces')?>">Interfaces</a>
	&nbsp;&nbsp;
	<a href="<?=buildQuery('Preview','Linked','f=foldertree')?>">Folder tree</a>
</div>
<div class="markdown-body">
<? foreach($content as $c)echo$c;?>
</div>