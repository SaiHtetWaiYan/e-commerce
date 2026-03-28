<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Contracts\View\View;

class UserController extends Controller
{
    public function index(): View
    {
        $customers = User::query()
            ->customers()
            ->latest()
            ->paginate(15, ['*'], 'customers_page')
            ->withQueryString();

        $vendors = User::query()
            ->vendors()
            ->with('vendorProfile')
            ->latest()
            ->paginate(15, ['*'], 'vendors_page')
            ->withQueryString();

        $deliveryAgents = User::query()
            ->deliveryAgents()
            ->latest()
            ->paginate(15, ['*'], 'delivery_agents_page')
            ->withQueryString();

        return view('admin.users.index', [
            'customers' => $customers,
            'vendors' => $vendors,
            'deliveryAgents' => $deliveryAgents,
        ]);
    }
}
