<?php $this->_extends('_layouts/default_layout'); ?>

<?php $this->_block('contents'); ?>

<h2 class="line_top" style="text-align: center;padding-top: 20px;">Hi, QeePHP</h2>

<?php dump($vars); ?>


<?php echo "&lt?php echo url('system::public/index');?&gt; => "; echo url('system::public/index');?>

<?php $this->_endblock('contents'); ?>
