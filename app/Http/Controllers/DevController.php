<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Models\User;

/**
 * Controller for developers use only
 * To track execution time use $this-t()
 * To dump values use $this->d
 * Set up _authorize() to you needs
 *
 */
class DevController extends Controller
{
    private $d = [];
    private $timings = [];
    private $user;
    private $fullStart;
    private $start;

    public function __construct()
    {
        $this->user = auth()->user();
        $this->fullStart = $this->start = microtime(true);
    }

    public function action($slug)
    {
        if (!method_exists($this, $slug)) {
            dd('ERROR: action not found'); //? 404 instead
        }

        if ($slug != 'public') {
            $this->_authorize();
        }

        $result = $this->{$slug}();

        // dump $timings only if we set some
        if ($this->timings) {
            $this->timings['timings-finish'] = microtime(true) - $this->fullStart;
            dump('TIMINGS', $this->timings);
        }

        // dump $d only if we set some
        if ($this->d) {
            dump('RESULT DUMP', $this->d);
        }

        return $result;
    }

    // dummy public method.
    // can be used to showcase some functionality to external user.
    private function public()
    {
        return "Hello from devs!";
    }

    // dummy method
    private function test()
    {
        $this->d[] = 'creating 1000 els array and collection...';

        $array = range(-500, 500);
        shuffle($array);

        $colleciton = collect($array);

        $this->d[] = 'starting sorting...';

        $this->setFullStart();

        sort($array);

        $this->t('array_sort');

        $colleciton->sort();

        $this->t('collection_sort');

        $this->d[] = 'sorting done.';

        return $array;
    }

    // test emails
    private function email()
    {
        $t = request()->type;
        $email = request()->email;

        if ($t == 'feedback') {
            $f = \App\Models\Feedback::first();
            $mail = new \App\Mail\FeedbackMail($f);
        }

        // other emails test here...

        if (!$mail) {
            dd('ERROR: mail not found');
        }

        if ($email) {
            Mail::to($email)->send($mail);
        }

        return $mail;
    }

    // login to user by ID (login to admin by default)
    private function login()
    {
        $user = request()->user;

        if (!$user) {
            $user = User::whereIn('email', ['admin@mail.com', 'admin@admin.com'])->first();
            if (!$user) {
                // todo add belongsTo relation check
                $user = User::whereHas('roles', function ($q) {
                    $q->where('name', 'admin');
                })->first();
            }
            if (!$user) {
                dump('Admin user not found. Please provide user_id manualy');
                dd(User::all());
            }
        } else {
            $user = User::find($user);
        }

        auth()->login($user);

        return redirect('/');
    }

    // get phpinfo
    private function phpinfo()
    {
        phpinfo();
    }

    // get client IP
    private function ip()
    {
        $ip = request()->ip();

        return "Your ip is: $ip";
    }

    // helper to store execution time
    private function t($key=null)
    {
        $t = microtime(true) - $this->start;

        if ($key) {
            $this->timings[$key] = $t;
        } else {
            $this->timings[] = $t;
        }

        $this->start = microtime(true);
    }

    // reset start time
    private function setFullStart($key=null)
    {
        $this->fullStart = $this->start = microtime(true);
    }

    // authorize access to methods
    private function _authorize()
    {
        $ok = true || isdev() || $this->user?->isAdmin();

        abort_if(!$ok, 403);
    }
}
