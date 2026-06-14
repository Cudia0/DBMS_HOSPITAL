<?php

use yii\helpers\Html;
use common\models\TblPatient;

/** @var yii\web\View $this */
/** @var common\models\User $user */
/** @var TblPatient $roleModel */

$this->title = 'Profile Settings';
$this->params['breadcrumbs'][] = $this->title;

$patient = TblPatient::findOne($user->patient_id);
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
                        <tr><th width="150">Role:</th><td><span class="badge bg-info">Patient</span></td></tr>
                        <tr><th>Username:</th><td><strong><?= Html::encode($user->username) ?></strong></td></tr>
                        <tr><th>Email:</th><td><?= Html::encode($user->email) ?> <span class="badge bg-secondary">Cannot be changed</span></td></tr>
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
                        <div class="mb-3">
                            <label class="form-label">Current Password</label>
                            <input type="password" name="current_password" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">New Password (min 6 characters)</label>
                            <input type="password" name="new_password" class="form-control" minlength="6" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Confirm New Password</label>
                            <input type="password" name="confirm_password" class="form-control" minlength="6" required>
                        </div>
                        <button type="submit" class="btn btn-warning"><i class="fas fa-save"></i> Update Password</button>
                    </form>
                </div>
            </div>

            <!-- Personal Information (Patient) -->
            <?php if ($patient): ?>
            <div class="card mb-4">
                <div class="card-header bg-success text-white"><h5 class="mb-0"><i class="fas fa-user"></i> Personal Information</h5></div>
                <div class="card-body">
                    <form method="post" action="<?= Yii::$app->urlManager->createUrl(['profile/update-patient-info']) ?>">
                        <input type="hidden" name="<?= Yii::$app->request->csrfParam ?>" value="<?= Yii::$app->request->csrfToken ?>">
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label">First Name *</label>
                                <input type="text" name="TblPatient[first_name]" class="form-control" value="<?= Html::encode($patient->first_name) ?>" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Middle Name</label>
                                <input type="text" name="TblPatient[middle_name]" class="form-control" value="<?= Html::encode($patient->middle_name ?? '') ?>">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Last Name *</label>
                                <input type="text" name="TblPatient[last_name]" class="form-control" value="<?= Html::encode($patient->last_name) ?>" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Sex</label>
                                <select name="TblPatient[sex]" class="form-select">
                                    <option value="">-- Select --</option>
                                    <option value="Male" <?= $patient->sex === 'Male' ? 'selected' : '' ?>>Male</option>
                                    <option value="Female" <?= $patient->sex === 'Female' ? 'selected' : '' ?>>Female</option>
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Date of Birth</label>
                                <input type="date" name="TblPatient[date_of_birth]" class="form-control" value="<?= $patient->date_of_birth ?>" max="<?= date('Y-m-d') ?>">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Age</label>
                                <input type="text" class="form-control" readonly value="<?= $patient->getAgeDisplay() ?>">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Country Code</label>
                                <select name="TblPatient[country_code]" class="form-select">
                                    <option value="">-- Select --</option>
                                    <option value="+63" <?= $patient->country_code === '+63' ? 'selected' : '' ?>>+63 (Philippines)</option>
                                    <option value="+1" <?= $patient->country_code === '+1' ? 'selected' : '' ?>>+1 (USA)</option>
                                </select>
                            </div>
                            <div class="col-md-8 mb-3">
                                <label class="form-label">Phone Number</label>
                                <input type="text" name="TblPatient[phone_num]" class="form-control" value="<?= Html::encode($patient->phone_num ?? '') ?>">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control" value="<?= Html::encode($patient->email) ?>" readonly>
                            <small class="text-danger">Email cannot be changed.</small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Address</label>
                            <textarea name="TblPatient[address]" class="form-control" rows="3"><?= Html::encode($patient->address ?? '') ?></textarea>
                        </div>
                        <button type="submit" class="btn btn-success"><i class="fas fa-save"></i> Update Information</button>
                    </form>
                </div>
            </div>
            <?php endif; ?>

        </div>
    </div>
</div>