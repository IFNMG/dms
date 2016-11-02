<?php if (Yii::$app->session->getFlash('success')): ?>
    <div class="alert alert-success alert-dismissible">
          <button class="close" aria-hidden="true" data-dismiss="alert" type="button">×</button>
        <?php echo Yii::$app->session->getFlash('success'); ?>
    </div>
<?php endif; ?>

<?php if (Yii::$app->session->getFlash('error')): ?>
    <div class="alert alert-danger alert-dismissible">
          <button class="close" aria-hidden="true" data-dismiss="alert" type="button">×</button>
        <?php echo Yii::$app->session->getFlash('error'); ?>
    </div>

<?php endif; ?>