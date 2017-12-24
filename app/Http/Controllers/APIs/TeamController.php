<?php

namespace App\Http\Controllers\APIs;

use App\Http\Controllers\Controller;
use App\Team;
use Illuminate\Support\Facades\DB;

class TeamController extends Controller
{
    /**
     * Get list of IT teams
     *
     * @return mixed
     */

    public function get_teams()
    {
        $teams = DB::table('teams')->paginate();
        return $teams;
    }
}
