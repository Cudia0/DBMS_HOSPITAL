<?php

declare(strict_types=1);

namespace frontend\controllers;

use common\models\LoginForm;
use common\models\User;
use common\models\ContactForm;
use common\models\PasswordResetRequestForm;
use common\models\ResendVerificationEmailForm;
use common\models\ResetPasswordForm;
use common\models\SignupForm;
use common\models\VerifyEmailForm;
use Yii;
use yii\base\InvalidArgumentException;
use yii\captcha\CaptchaAction;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
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

    public function actionIndex(): string
    {
        return $this->render('index');
    }

    /**
     * Login action - FRONTEND ONLY FOR PATIENTS
     * Staff accounts will be redirected to backend
     */
    public function actionLogin(): string|Response
    {
        // If already logged in
        if (!Yii::$app->user->isGuest) {
            $user = Yii::$app->user->identity;
            
            // If staff somehow logged in to frontend, log them out and redirect to backend
            if ($user->canAccessBackend()) {
                Yii::$app->user->logout();
                Yii::$app->session->setFlash('info', 'Staff members must use the backend portal.');
                return $this->redirect(Yii::$app->params['backendUrl'] ?? 'http://vince.com/backend/index.php?r=site/login');
            }
            
            // Patient already logged in
            return $this->goHome();
        }

        $model = new LoginForm();

        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            $user = Yii::$app->user->identity;
            
            // If staff member, log them out from frontend and redirect to backend
            if ($user->canAccessBackend()) {
                Yii::$app->user->logout();
                Yii::$app->session->destroy();
                Yii::$app->session->setFlash('info', 'Staff members must use the backend portal. Redirecting...');
                return $this->redirect(Yii::$app->params['backendUrl'] ?? 'http://vince.com/backend/index.php?r=site/login');
            }
            
            // Patient logged in successfully
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

    public function actionContact(): string|Response
    {
        $model = new ContactForm();

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $sent = $model->sendEmail(
                $this->mailer,
                Yii::$app->params['adminEmail'],
                Yii::$app->params['senderEmail'],
                Yii::$app->params['senderName'],
            );

            if ($sent) {
                Yii::$app->session->setFlash('success', 'Thank you for contacting us. We will respond to you as soon as possible.');
            } else {
                Yii::$app->session->setFlash('error', 'There was an error sending your message.');
            }

            return $this->refresh();
        }

        return $this->render('contact', [
            'model' => $model,
        ]);
    }

    public function actionAbout(): string
    {
        return $this->render('about');
    }

    public function actionSignup(): string|Response
    {
        $model = new SignupForm();

        if ($model->load(Yii::$app->request->post())) {
            $signed = $model->signup(
                $this->mailer,
                Yii::$app->params['supportEmail'],
                Yii::$app->name,
            );

            if ($signed) {
                Yii::$app->session->setFlash('success', 'Thank you for registration! You can now login and complete your profile.');
                return $this->redirect(['login']);
            }
        }

        return $this->render('signup', [
            'model' => $model,
        ]);
    }

    public function actionRequestPasswordReset(): string|Response
    {
        $model = new PasswordResetRequestForm();

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $sent = $model->sendEmail(
                $this->mailer,
                Yii::$app->params['supportEmail'],
                Yii::$app->name,
            );

            if ($sent) {
                Yii::$app->session->setFlash('success', 'Check your email for further instructions.');
                return $this->goHome();
            }

            Yii::$app->session->setFlash('error', 'Sorry, we are unable to reset password for the provided email address.');
        }

        return $this->render('requestPasswordResetToken', [
            'model' => $model,
        ]);
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

        return $this->render('resetPassword', [
            'model' => $model,
        ]);
    }

    public function actionVerifyEmail(string $token): Response
    {
        try {
            $model = new VerifyEmailForm($token);
        } catch (InvalidArgumentException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }

        if ($model->verifyEmail()) {
            Yii::$app->session->setFlash('success', 'Your email has been confirmed!');
            return $this->goHome();
        }

        Yii::$app->session->setFlash('error', 'Sorry, we are unable to verify your account with provided token.');
        return $this->goHome();
    }

    public function actionResendVerificationEmail(): string|Response
    {
        $model = new ResendVerificationEmailForm();

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $sent = $model->sendEmail(
                $this->mailer,
                Yii::$app->params['supportEmail'],
                Yii::$app->name,
            );

            if ($sent) {
                Yii::$app->session->setFlash('success', 'Check your email for further instructions.');
                return $this->goHome();
            }

            Yii::$app->session->setFlash('error', 'Sorry, we are unable to resend verification email for the provided email address.');
        }

        return $this->render('resendVerificationEmail', [
            'model' => $model,
        ]);
    }
}