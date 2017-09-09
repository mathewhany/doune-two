<?php

namespace Doune\Installer;

use Slim\Slim;
use Illuminate\Database\Schema\Blueprint;

class Installer
{
    /**
     * Stores slim instance.
     *
     * @var Slim
     */
    protected $container;

    /**
     * Is the app is installing right now?
     * 
     * @var bool
     */
    protected $installing = false;

    /**
     * Constructor
     *
     * @param Slim $app
     */
    public function __construct(Slim $app)
    {
        $this->app = $app;
    }

    public function isInstalled()
    {
        return file_exists($this->app->paths['root'] . '/.installed');
    }

    public function install()
    {
        $this->registerRoutes();

        $this->app->hook('slim.after', function () {
            if ( ! $this->installing ) {
                header('Location: ' . $this->app->urlFor('install.welcome'));
            }
        });
        
        exit($this->app->run());
    }
    
    public function registerRoutes()
    {
        $this->app->map('/install(/.*?)', function () {
            $this->installing = true;
            $this->app->pass();
        })->via('GET', 'POST');
        $this->app->get('/install/welcome', [$this, 'welcome'])->name('install.welcome');
        $this->app->get('/install', [$this, 'form'])->name('install.form');
        $this->app->post('/install', [$this, 'process'])->name('install.process');
    }

    public function validateForm()
    {
        $fields = $this->app->request->post();

        foreach ($fields as $name => $value) {
            if (empty($value)) {
                $message = 'Please enter all fields.';
            } elseif (mb_strlen($value) < 2) {
                $message = 'The ' . $name . ' field must be bigger than 2 characters.';
            }
        }

        if (isset($message)) {
            $this->app->flash('error', $message);
            redirect()->back();
        }
    }

    public function process()
    {
        // Create database tables.
        $this->createTables();

        $this->validateForm();

        $fields = array_only(
            $this->app->request->post(),
            ['title', 'keywords', 'description', 'email', 'password']
        );
        
        $email = $fields['email'];
        $password = md5($fields['password']);

        unset($fields['email'], $fields['password']);

        \User::create(compact('email', 'password'));

        $this->app->auth->login($email, $password);

        foreach ($fields as $name => $value) {
            \Setting::create(compact('name', 'value'));
        }

        \Setting::create(['name' => 'default_theme', 'value' => 'default']);

        file_put_contents($this->app->paths['root'] . '/.installed', null);

        $this->app->render('install/success.html.twig');
    }

    public function welcome()
    {
        return $this->app->render('install/welcome.html.twig');
    }

    public function form()
    {
        return $this->app->render('install/form.html.twig');
    }

    /**
     * Create the app tables.
     *
     * @return void
     */
    public function createTables()
    {
        $schemaBuilder = $this->getSchemaBuilder();

        // Create users table
        if ( ! $schemaBuilder->hasTable('users')) {
            $schemaBuilder->create('users', function (Blueprint $table) {
                $table->increments('id');
                $table->string('email')->unique();
                $table->string('password', 32);
                $table->timestamps();
            });
        }

        // Create articles table
        if ( ! $schemaBuilder->hasTable('articles')) {
            $schemaBuilder->create('articles', function (Blueprint $table) {
                $table->increments('id');
                $table->string('title');
                $table->text('content');
                $table->integer('user_id')->unsigned();
                $table->foreign('user_id')->references('id')->on('users');
                $table->timestamps();
            });
        }

        // Create tags table
        if ( ! $schemaBuilder->hasTable('tags')) {
            $this->getSchemaBuilder()->create('tags', function (Blueprint $table) {
                $table->increments('id');
                $table->string('name');
                $table->timestamps();
            });
        }

        // Create article_tag table
        if ( ! $schemaBuilder->hasTable('article_tag')) {
            $schemaBuilder->create('article_tag', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('article_id')->unsigned();
                $table->foreign('article_id')->references('id')->on('articles')->onDelete('cascade');
                $table->integer('tag_id')->unsigned();
                $table->foreign('tag_id')->references('id')->on('tags')->onDelete('cascade');
            });
        }

        // Create settings table
        if ( ! $schemaBuilder->hasTable('settings')) {
            $schemaBuilder->create('settings', function (Blueprint $table) {
                $table->increments('id');
                $table->string('name');
                $table->text('value');
            });
        }
    }

    protected function getSchemaBuilder()
    {
        return $this->app->container['db.capsule']->schema();
    }
}
