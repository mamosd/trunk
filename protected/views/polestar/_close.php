<script type="text/javascript">
parent.jQuery.colorbox.close();
<?php if (isset($redirectTo)): ?>
parent.location.href = "<?php echo $redirectTo; ?>";    
<?php endif; ?>
</script>