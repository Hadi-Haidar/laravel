<?php

namespace App\Http\Controllers\Admin\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Get dashboard statistics for admin manager
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getStatistics(Request $request)
    {
        // Get count of admins by role
        $adminsByRole = Admin::select('role', DB::raw('count(*) as count'))
                            ->groupBy('role')
                            ->get()
                            ->pluck('count', 'role')
                            ->toArray();
        
        // Get newly registered admins (last 7 days)
        $newAdmins = Admin::where('created_at', '>=', now()->subDays(7))
                        ->count();
        
        // You can add more statistics as needed
        // For example, if you have other models:
        // $totalUsers = User::count();
        // $totalProducts = Product::count();
        // etc.
        
        return response()->json([
            'admin_stats' => [
                'total' => Admin::count(),
                'by_role' => $adminsByRole,
                'new_last_week' => $newAdmins,
            ],
            // Add more statistics sections as needed
            // 'user_stats' => [...],
            // 'order_stats' => [...],
        ]);
    }
    
    /**
     * Get recent activity for the dashboard
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getRecentActivity(Request $request)
    {
        // Recent admin creations
        $recentAdmins = Admin::orderBy('created_at', 'desc')
                            ->take(5)
                            ->get(['id', 'name', 'email', 'role', 'created_at']);
        
        return response()->json([
            'recent_activity' => [
                'admins' => $recentAdmins,
                // Add other recent activities as needed
            ]
        ]);
    }
} 