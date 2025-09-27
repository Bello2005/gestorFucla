<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AuditoriaController extends Controller
{
    public function index()
    {
    $order = request('order', 'desc');
    $logs = DB::table('audit_log')->orderBy('created_at', $order)->limit(50)->get();
    return view('auditoria', compact('logs'));
    }
}
