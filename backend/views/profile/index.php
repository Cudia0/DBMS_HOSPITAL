<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var common\models\User $user */
/** @var object|null $roleModel */

$this->title = 'Profile Settings';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="profile-settings">
    <div class="row">
        <div class="col-md-8 mx-auto">
            <h1 class="mb-4"><i class="fas fa-user-cog"></i> <?= Html::encode($this->title) ?></h1>

            <?php if (Yii::$app->session->hasFlash('success')): ?>
                <div class="alert alert-success"><?= Yii::$app->session->getFlash('success') ?></div>
            <?php endif; ?>
            <?php if (Yii::$app->session->hasFlash('error')): ?>
                <div class="alert alert-danger"><?= Yii::$app->session->getFlash('error') ?></div>
            <?php endif; ?>

            <!-- Account Info -->
            <div class="card mb-4">
                <div class="card-header bg-primary text-white"><h5 class="mb-0"><i class="fas fa-id-card"></i> Account Information</h5></div>
                <div class="card-body">
                    <table class="table table-borderless">
                        <tr><th width="150">Role:</th><td><span class="badge bg-info"><?= $user->getRoleLabel() ?></span></td></tr>
                        <tr><th>Username:</th><td><strong><?= Html::encode($user->username) ?></strong></td></tr>
                        <tr><th>Email:</th><td><?= Html::encode($user->email) ?></td></tr>
                    </table>
                </div>
            </div>

            <!-- Change Username -->
            <div class="card mb-4">
                <div class="card-header bg-info text-white"><h5 class="mb-0"><i class="fas fa-user-edit"></i> Change Username</h5></div>
                <div class="card-body">
                    <form method="post" action="<?= Yii::$app->urlManager->createUrl(['profile/update-username']) ?>">
                        <input type="hidden" name="<?= Yii::$app->request->csrfParam ?>" value="<?= Yii::$app->request->csrfToken ?>">
                        <div class="mb-3">
                            <label class="form-label">New Username</label>
                            <input type="text" name="username" class="form-control" value="<?= Html::encode($user->username) ?>" required>
                        </div>
                        <button type="submit" class="btn btn-info"><i class="fas fa-save"></i> Update Username</button>
                    </form>
                </div>
            </div>

            <!-- Change Password -->
            <div class="card mb-4">
                <div class="card-header bg-warning text-dark"><h5 class="mb-0"><i class="fas fa-lock"></i> Change Password</h5></div>
                <div class="card-body">
                    <form method="post" action="<?= Yii::$app->urlManager->createUrl(['profile/update-password']) ?>">
                        <input type="hidden" name="<?= Yii::$app->request->csrfParam ?>" value="<?= Yii::$app->request->csrfToken ?>">
                        <div class="mb-3"><label class="form-label">Current Password</label><input type="password" name="current_password" class="form-control" required></div>
                        <div class="mb-3"><label class="form-label">New Password (min 6 characters)</label><input type="password" name="new_password" class="form-control" minlength="6" required></div>
                        <div class="mb-3"><label class="form-label">Confirm New Password</label><input type="password" name="confirm_password" class="form-control" minlength="6" required></div>
                        <button type="submit" class="btn btn-warning"><i class="fas fa-save"></i> Update Password</button>
                    </form>
                </div>
            </div>

            <?php if ($user->isPatient() && $roleModel): ?>
            <!-- Patient Personal Info -->
            <div class="card mb-4">
                <div class="card-header bg-success text-white"><h5 class="mb-0"><i class="fas fa-user"></i> Personal Information</h5></div>
                <div class="card-body">
                    <form method="post" action="<?= Yii::$app->urlManager->createUrl(['profile/update-patient-info']) ?>">
                        <input type="hidden" name="<?= Yii::$app->request->csrfParam ?>" value="<?= Yii::$app->request->csrfToken ?>">
                        <div class="row">
                            <div class="col-md-4 mb-3"><label class="form-label">First Name *</label><input type="text" name="TblPatient[first_name]" class="form-control" value="<?= Html::encode($roleModel->first_name ?? '') ?>" required></div>
                            <div class="col-md-4 mb-3"><label class="form-label">Middle Name</label><input type="text" name="TblPatient[middle_name]" class="form-control" value="<?= Html::encode($roleModel->middle_name ?? '') ?>"></div>
                            <div class="col-md-4 mb-3"><label class="form-label">Last Name *</label><input type="text" name="TblPatient[last_name]" class="form-control" value="<?= Html::encode($roleModel->last_name ?? '') ?>" required></div>
                        </div>
                        <button type="submit" class="btn btn-success"><i class="fas fa-save"></i> Update Information</button>
                    </form>
                </div>
            </div>
            <?php endif; ?>

            <?php if (($user->isDoctor() || $user->isReceptionist() || $user->isDirector()) && $roleModel): ?>
            <!-- Staff Name Edit -->
            <div class="card mb-4">
                <div class="card-header bg-success text-white"><h5 class="mb-0"><i class="fas fa-user"></i> Personal Information</h5></div>
                <div class="card-body">
                    <form method="post" action="<?= Yii::$app->urlManager->createUrl(['profile/update-' . $user->role . '-info']) ?>">
                        <input type="hidden" name="<?= Yii::$app->request->csrfParam ?>" value="<?= Yii::$app->request->csrfToken ?>">
                        <div class="row">
                            <div class="col-md-4 mb-3"><label class="form-label">First Name *</label><input type="text" name="first_name" class="form-control" value="<?= Html::encode($roleModel->first_name ?? '') ?>" required></div>
                            <div class="col-md-4 mb-3"><label class="form-label">Middle Name</label><input type="text" name="middle_name" class="form-control" value="<?= Html::encode($roleModel->middle_name ?? '') ?>"></div>
                            <div class="col-md-4 mb-3"><label class="form-label">Last Name *</label><input type="text" name="last_name" class="form-control" value="<?= Html::encode($roleModel->last_name ?? '') ?>" required></div>
                        </div>
                        <button type="submit" class="btn btn-success"><i class="fas fa-save"></i> Update Name</button>
                    </form>
                </div>
            </div>
            <?php endif; ?>

        </div>
    </div>
</div>