<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DeployController extends Controller
{
    public function handle(Request $request)
    {
        $secret = env('DEPLOY_SECRET');

        if (!$secret || $request->header('X-Deploy-Token') !== $secret) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $output = shell_exec('cd /var/www/cafepro && git pull origin master 2>&1 && php artisan config:cache 2>&1 && php artisan route:cache 2>&1 && php artisan view:cache 2>&1');

        return response()->json(['success' => true, 'output' => $output]);
    }
}
