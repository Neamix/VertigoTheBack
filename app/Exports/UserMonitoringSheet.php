<?php

namespace App\Exports;


namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class UserMonitoringSheet implements FromView
{
    protected $users;

    public  function __construct($users) {
        $this->users = $users;
    }

    public function view(): View
    {
        return view('exports.users', [
            'users' => $this->users
        ]);
    }
}
