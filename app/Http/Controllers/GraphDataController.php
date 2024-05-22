<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Bakery;
use App\Models\Product;
use App\Models\Order;
use Carbon\Carbon;

class GraphDataController extends Controller
{
    // Method to get user registrations on daily and monthly basis
    public function getUserRegistrations()
    {
        try {
            $today = Carbon::now()->format('Y-m-d');
            $thisMonth = Carbon::now()->startOfMonth()->format('Y-m-d');
            $lastMonth = Carbon::now()->subMonth()->startOfMonth()->format('Y-m-d');

            $dailyRegistrations = User::whereDate('created_at', $today)->count();
            $monthlyRegistrations = User::whereDate('created_at', '>=', $thisMonth)->count();
            $lastMonthRegistrations = User::whereDate('created_at', '>=', $lastMonth)
                ->whereDate('created_at', '<', $thisMonth)
                ->count();

            return response()->json([
                'daily_registrations' => $dailyRegistrations,
                'monthly_registrations' => $monthlyRegistrations,
                'last_month_registrations' => $lastMonthRegistrations
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    // Method to get bakery registrations on daily and monthly basis
    public function getBakeryRegistrations()
    {
        try {
            $today = Carbon::now()->format('Y-m-d');
            $thisMonth = Carbon::now()->startOfMonth()->format('Y-m-d');
            $lastMonth = Carbon::now()->subMonth()->startOfMonth()->format('Y-m-d');

            $dailyRegistrations = Bakery::whereDate('created_at', $today)->count();
            $monthlyRegistrations = Bakery::whereDate('created_at', '>=', $thisMonth)->count();
            $lastMonthRegistrations = Bakery::whereDate('created_at', '>=', $lastMonth)
                ->whereDate('created_at', '<', $thisMonth)
                ->count();

            return response()->json([
                'daily_registrations' => $dailyRegistrations,
                'monthly_registrations' => $monthlyRegistrations,
                'last_month_registrations' => $lastMonthRegistrations
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    // Method to get product additions on daily and monthly basis
    public function getProductAdditions()
    {
        try {
            $today = Carbon::now()->format('Y-m-d');
            $thisMonth = Carbon::now()->startOfMonth()->format('Y-m-d');
            $lastMonth = Carbon::now()->subMonth()->startOfMonth()->format('Y-m-d');

            $dailyAdditions = Product::whereDate('created_at', $today)->count();
            $monthlyAdditions = Product::whereDate('created_at', '>=', $thisMonth)->count();
            $lastMonthAdditions = Product::whereDate('created_at', '>=', $lastMonth)
                ->whereDate('created_at', '<', $thisMonth)
                ->count();

            return response()->json([
                'daily_additions' => $dailyAdditions,
                'monthly_additions' => $monthlyAdditions,
                'last_month_additions' => $lastMonthAdditions
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    // Method to get orders on daily and monthly basis
    public function getOrderPlacements()
    {
        // try {
        //     $today = Carbon::now()->format('Y-m-d');
        //     $thisMonth = Carbon::now()->startOfMonth()->format('Y-m-d');
        //     $lastMonth = Carbon::now()->subMonth()->startOfMonth()->format('Y-m-d');

        //     $dailyOrders = Order::whereDate('created_at', $today)->count();
        //     $monthlyOrders = Order::whereDate('created_at', '>=', $thisMonth)->count();
        //     $lastMonthOrders = Order::whereDate('created_at', '>=', $lastMonth)
        //         ->whereDate('created_at', '<', $thisMonth)
        //         ->count();

        //     return response()->json([
        //         'daily_orders' => $dailyOrders,
        //         'monthly_orders' => $monthlyOrders,
        //         'last_month_orders' => $lastMonthOrders
        //     ], 200);
        // } catch (\Exception $e) {
        //     return response()->json(['error' => $e->getMessage()], 500);
        // }
    }
}
