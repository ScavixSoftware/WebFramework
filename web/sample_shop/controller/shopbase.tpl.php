<div id="page">
	<div id="navigation">
		<a href="<?=buildQuery('Products')?>">Products</a>
		<a href="<?=buildQuery('Basket')?>">Basket</a>
		<a href="<?=buildQuery('Admin')?>">Administration (normally hidden)</a>
	</div>
	<div id="content">
		<? foreach( $content as $c ) echo $c; ?>	
	</div>
</div>
