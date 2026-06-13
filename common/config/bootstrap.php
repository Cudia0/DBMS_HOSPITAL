<?php

declare(strict_types=1);

Yii::setAlias('@common', dirname(__DIR__));
Yii::setAlias('@frontend', dirname(dirname(__DIR__)) . '/frontend');
Yii::setAlias('@backend', dirname(dirname(__DIR__)) . '/backend');
Yii::setAlias('@console', dirname(dirname(__DIR__)) . '/console');

Yii::setAlias('@frontendUrl', 'http://vince.com/frontend/index.php/site');
Yii::setAlias('@backendUrl', 'http://vince.com/backend/index.php/site');