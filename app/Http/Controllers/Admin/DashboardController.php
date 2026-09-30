<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\ContactMessage;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        $activeOrders = Order::where('status', '!=', 'cancelled');

        return Inertia::render('Admin/Dashboard', [
            'stats' => [
                'products'       => Product::count(),
                'categories'     => Category::count(),
                'customers'      => User::role('customer')->count(),
                'orders'         => Order::count(),
                'pending_orders' => Order::where('status', 'pending')->count(),
                'new_messages'   => ContactMessage::where('status', 'new')->count(),
                // Ventes = commandes non annulées ; encaissé = commandes marquées payées.
                'sales'          => (float) (clone $activeOrders)->sum('total'),
                'collected'      => (float) Order::where('payment_status', 'paid')
                    ->where('status', '!=', 'cancelled')
                    ->sum('total'),
            ],
            'recentOrders' => Order::with('user:id,name')
                ->latest()
                ->limit(5)
                // ->get(['id', 'reference', 'user_id', 'status', 'total', 'created_at']),
                ->get(['id', 'reference', 'user_id', 'status', 'payment_method', 'payment_status', 'total', 'created_at']),
            // Messages à traiter : nouveaux et en cours
            'recentMessages' => ContactMessage::with('order:id,reference')
                ->whereIn('status', ['new', 'in_progress'])
                ->latest()
                ->limit(5)
                ->get(['id', 'order_id', 'name', 'subject', 'status', 'created_at']),
            'lowStock' => Product::where('active', true)
                ->where('stock', '<=', 5)
                ->orderBy('stock')
                ->limit(5)
                ->get(['id', 'name', 'stock']),
        ]);
    }
}
