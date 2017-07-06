<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
    <meta name="renderer" content="webkit|ie-comp|ie-stand" />
    <title><?php echo $_TITLE ?? Q::ini('appini/meta/title'); ?></title>
    <link rel="stylesheet" href="<?php echo $_BASE_DIR;?>css/style.css?t=<?php echo microtime();?>" />
    <script src="<?php echo $_BASE_DIR;?>css/jquery.min.js"></script>
</head>
<body>


<div class="main_nav">

    <a class="left<?php __is($_UDI, 'system::portal/index');?>" href="<?php echo url('system::portal/index');?>">系统管理</a>
    <span class="left">/</span>
    <a class="left<?php __is($_UDI, 'system::portal/servers');?>" href="<?php echo url('system::portal/servers');?>">服务器监控</a>
    <a class="right" href="<?php echo url('port::auth/remove');?>">退出</a>
    <br class="clearfix" />
</div>

<?php if($_MSG):?><div class="info"><?php echo $_MSG;?></div><?php endif;?>

<?php $this->_block('contents'); ?><?php $this->_endblock(); ?>

</body>
</html>
