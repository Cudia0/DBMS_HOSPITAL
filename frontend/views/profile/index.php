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
                <div class="alert alert-success alert-dismissible fade show"><?= Yii::$app->session->getFlash('success') ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
            <?php endif; ?>
            <?php if (Yii::$app->session->hasFlash('error')): ?>
                <div class="alert alert-danger alert-dismissible fade show"><?= Yii::$app->session->getFlash('error') ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
            <?php endif; ?>

            <!-- Account Information -->
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
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-user"></i></span>
                                <input type="text" name="username" class="form-control" value="<?= Html::encode($user->username) ?>" required>
                            </div>
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
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-key"></i></span>
                                <input type="password" name="current_password" class="form-control" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">New Password (min 6 characters)</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                <input type="password" name="new_password" class="form-control" minlength="6" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Confirm New Password</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                <input type="password" name="confirm_password" class="form-control" minlength="6" required>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-warning"><i class="fas fa-save"></i> Update Password</button>
                    </form>
                </div>
            </div>

            <!-- Patient Personal Information -->
            <?php if ($roleModel): ?>
            <div class="card mb-4">
                <div class="card-header bg-success text-white"><h5 class="mb-0"><i class="fas fa-user"></i> Personal Information</h5></div>
                <div class="card-body">
                    <form method="post" action="<?= Yii::$app->urlManager->createUrl(['profile/update-patient-info']) ?>">
                        <input type="hidden" name="<?= Yii::$app->request->csrfParam ?>" value="<?= Yii::$app->request->csrfToken ?>">
                        <input type="hidden" name="TblPatient[patient_id]" value="<?= $roleModel->patient_id ?? '' ?>">
                        
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label">First Name *</label>
                                <input type="text" name="TblPatient[first_name]" class="form-control" value="<?= Html::encode($roleModel->first_name ?? '') ?>" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Middle Name</label>
                                <input type="text" name="TblPatient[middle_name]" class="form-control" value="<?= Html::encode($roleModel->middle_name ?? '') ?>">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Last Name *</label>
                                <input type="text" name="TblPatient[last_name]" class="form-control" value="<?= Html::encode($roleModel->last_name ?? '') ?>" required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Sex</label>
                                <select name="TblPatient[sex]" class="form-select">
                                    <option value="">-- Select --</option>
                                    <option value="Male" <?= ($roleModel->sex ?? '') === 'Male' ? 'selected' : '' ?>>Male</option>
                                    <option value="Female" <?= ($roleModel->sex ?? '') === 'Female' ? 'selected' : '' ?>>Female</option>
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Date of Birth</label>
                                <input type="date" name="TblPatient[date_of_birth]" class="form-control" value="<?= $roleModel->date_of_birth ?? '' ?>" max="<?= date('Y-m-d') ?>">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Age</label>
                                <input type="text" class="form-control" readonly value="<?= $roleModel->date_of_birth ? (date('Y') - date('Y', strtotime($roleModel->date_of_birth))) . ' years old' : '' ?>" placeholder="Auto-calculated">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Country Code</label>
                                <select name="TblPatient[country_code]" class="form-select">
                                    <option value="">-- Select --</option>
                                    <option value="+63" <?= ($roleModel->country_code ?? '') === '+63' ? 'selected' : '' ?>>+63 (Philippines)</option>
                                    <option value="+1" <?= ($roleModel->country_code ?? '') === '+1' ? 'selected' : '' ?>>+1 (USA)</option>
                                    <option value="+44" <?= ($roleModel->country_code ?? '') === '+44' ? 'selected' : '' ?>>+44 (UK)</option>
                                    <option value="+81" <?= ($roleModel->country_code ?? '') === '+81' ? 'selected' : '' ?>>+81 (Japan)</option>
                                    <option value="+86" <?= ($roleModel->country_code ?? '') === '+86' ? 'selected' : '' ?>>+86 (China)</option>
                                </select>
                            </div>
                            <div class="col-md-8 mb-3">
                                <label class="form-label">Phone Number</label>
                                <input type="text" name="TblPatient[phone_num]" class="form-control" value="<?= Html::encode($roleModel->phone_num ?? '') ?>">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control" value="<?= Html::encode($roleModel->email ?? '') ?>" readonly>
                            <small class="text-danger">Email cannot be changed. Contact administrator for assistance.</small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Address</label>
                            <textarea name="TblPatient[address]" class="form-control" rows="3"><?= Html::encode($roleModel->address ?? '') ?></textarea>
                        </div>

                        <button type="submit" class="btn btn-success"><i class="fas fa-save"></i> Update Personal Information</button>
                    </form>
                </div>
            </div>
            <?php endif; ?>

        </div>
    </div>
</div>