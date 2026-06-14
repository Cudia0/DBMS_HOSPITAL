<?php

declare(strict_types=1);

namespace frontend\controllers;

use common\models\LoginForm;
use common\models\User;
use frontend\models\ContactForm;
use frontend\models\PasswordResetRequestForm;
use frontend\models\ResendVerificationEmailForm;
use frontend\models\ResetPasswordForm;
use frontend\models\SignupForm;
use frontend\models\VerifyEmailForm;
use Yii;
use yii\base\InvalidArgumentException;
use yii\captcha\CaptchaAction;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\Html;
use yii\mail\MailerInterface;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\web\ErrorAction;
use yii\web\Response;

/**
 * Site controller - FRONTEND (Patients Only)
 */
class SiteController extends Controller
{
    public function __construct(
        $id,
        $module,
        private readonly MailerInterface $mailer,
        $config = [],
    ) {
        parent::__construct($id, $module, $config);
    }

    public function behaviors(): array
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only' => ['logout', 'signup'],
                'rules' => [
                    [
                        'actions' => ['signup'],
                        'allow' => true,
                        'roles' => ['?'],
                    ],
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    public function actions(): array
    {
        return [
            'error' => [
                'class' => ErrorAction::class,
            ],
            'captcha' => [
                'class' => CaptchaAction::class,
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     * Homepage for patients
     */
    public function actionIndex(): string
    {
        $user = Yii::$app->user->identity;
        
        return $this->render('index', [
            'user' => $user,
        ]);
    }

    /**
     * Login - Frontend only for patients
     */
    public function actionLogin(): string|Response
    {
        if (!Yii::$app->user->isGuest) {
            $user = Yii::$app->user->identity;
            
            // If staff logged in to frontend, log them out and redirect to backend
            if ($user->canAccessBackend()) {
                Yii::$app->user->logout();
                Yii::$app->session->setFlash('info', 'Staff members must use the backend portal.');
                return $this->redirect(Yii::$app->params['backendUrl'] ?? 'http://vince.com/backend/web/index.php?r=site/login');
            }
            
            return $this->goHome();
        }

        $model = new LoginForm();

        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            $user = Yii::$app->user->identity;
            
            if ($user->canAccessBackend()) {
                Yii::$app->user->logout();
                Yii::$app->session->setFlash('info', 'Staff members must use the backend portal.');
                return $this->redirect(Yii::$app->params['backendUrl'] ?? 'http://vince.com/backend/web/index.php?r=site/login');
            }
            
            Yii::$app->session->setFlash('success', 'Welcome, ' . $user->getFullName() . '!');
            return $this->goBack();
        }

        $model->password = '';

        return $this->render('login', [
            'model' => $model,
        ]);
    }

    public function actionLogout(): Response
    {
        Yii::$app->user->logout();
        return $this->goHome();
    }

    /**
     * Patient Registration
     */
    public function actionSignup(): string|Response
    {
        $model = new SignupForm();

        if ($model->load(Yii::$app->request->post())) {
            $user = $model->signup();
            
            if ($user) {
                $emailSent = $model->sendEmail($user);
                
                if ($emailSent) {
                    Yii::$app->session->setFlash('success', 
                        '✅ Registration successful!<br><br>' .
                        'Thank you for registering. <strong>Please check your email</strong> (' . Html::encode($model->email) . ') ' .
                        'and click the verification link to activate your account.'
                    );
                } else {
                    Yii::$app->session->setFlash('warning', 
                        '⚠️ Registration completed. Please check your email to verify your account.'
                    );
                }
                return $this->goHome();
            }
            
            Yii::$app->session->setFlash('error', 'Registration failed. Please check the errors below.');
        }

        return $this->render('signup', ['model' => $model]);
    }

    public function actionAbout(): string
    {
        return $this->render('about');
    }

    public function actionContact(): string|Response
    {
        $model = new ContactForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $model->sendEmail($this->mailer, Yii::$app->params['adminEmail'], Yii::$app->params['senderEmail'], Yii::$app->params['senderName']);
            Yii::$app->session->setFlash('success', 'Thank you for contacting us.');
            return $this->refresh();
        }
        return $this->render('contact', ['model' => $model]);
    }

    public function actionRequestPasswordReset(): string|Response
    {
        $model = new PasswordResetRequestForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $model->sendEmail($this->mailer, Yii::$app->params['supportEmail'], Yii::$app->name);
            Yii::$app->session->setFlash('success', 'Check your email for further instructions.');
            return $this->goHome();
        }
        return $this->render('requestPasswordResetToken', ['model' => $model]);
    }

    public function actionResetPassword(string $token): string|Response
    {
        try {
            $model = new ResetPasswordForm($token);
        } catch (InvalidArgumentException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }
        if ($model->load(Yii::$app->request->post()) && $model->validate() && $model->resetPassword()) {
            Yii::$app->session->setFlash('success', 'New password saved.');
            return $this->goHome();
        }
        return $this->render('resetPassword', ['model' => $model]);
    }

    public function actionVerifyEmail(string $token): Response
    {
        try {
            $model = new VerifyEmailForm($token);
        } catch (InvalidArgumentException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }
        if ($model->verifyEmail()) {
            Yii::$app->session->setFlash('success', '✅ Your email has been confirmed! You can now login and book appointments.');
            return $this->goHome();
        }
        Yii::$app->session->setFlash('error', 'Sorry, we are unable to verify your account.');
        return $this->goHome();
    }

    public function actionResendVerificationEmail(): string|Response
    {
        $model = new ResendVerificationEmailForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $model->sendEmail($this->mailer, Yii::$app->params['supportEmail'], Yii::$app->name);
            Yii::$app->session->setFlash('success', 'Check your email for further instructions.');
            return $this->goHome();
        }
        return $this->render('resendVerificationEmail', ['model' => $model]);
    }
}