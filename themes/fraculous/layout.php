<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
  "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<title>Frac :: <?php $this->eprint($this->pagename); ?></title>
	<meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
	<link rel="stylesheet" href="<?php $this->eprint($this->themepath); ?>/stylesheet.css" type="text/css" />
	<script type="text/javascript">
	<!--
		themepath="<?php $this->eprint($this->themepath); ?>";
		basepath="<?php $this->eprint($this->basepath); ?>";
	//-->
	</script>
	<script src="<?php $this->eprint($this->themepath); ?>/utils.js" type="text/javascript"></script>
	<script src="<?php $this->eprint($this->themepath); ?>/fraculous.js" type="text/javascript"></script>
<?php if(isset($this->headextra)) echo $this->headextra; ?>
</head>
<body>
	<?php $this->display("header.php"); ?>
	<div id="content">
		<div id="container">
		<?php if(isset($this->flashmsg)): ?>
		<div id="flashmsg<?php switch($this->flashmsg["type"]) { case "error": print "e"; break; case "warning": print "w"; break; case "success": print "s"; break; }?>">
		<?php $this->eprint($this->flashmsg["message"]); ?>
		</div>
		<?php endif; ?>
		<?php $this->display($this->view); ?>
		</div>
	</div>
	<?php $this->display("footer.php"); ?>
</body>
</html>
