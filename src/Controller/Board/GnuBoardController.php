<?php

namespace EvansKim\GnuMigration\Controller\Board;

use EvansKim\GnuMigration\GnuModel\GnuBoard;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class GnuBoardController extends Controller
{
    public function index(Request $request)
    {

        return['message'=>__METHOD__];
    }

    public function store(Request $request)
    {
        return['message'=>__METHOD__];
    }

    public function show(Request $request, $board)
    {
        $config = GnuBoard::where('bo_table', $board)
            ->select(['gr_id','bo_subject','bo_table','bo_skin'])
            ->selectLevelConfig()
            ->first();

        $config->checkAuthenticated("list");

        return $config;
    }

    public function update(Request $request, GnuBoard $gnuBoard)
    {
        return['message'=>__METHOD__];
    }

    public function destroy(GnuBoard $gnuBoard)
    {
        return['message'=>__METHOD__];
    }
}
