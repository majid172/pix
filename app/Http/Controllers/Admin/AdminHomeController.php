<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;

class AdminHomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        
        $data['orders']  = Order::latest()->limit(5)->get();
        $data['total_orders']  = Order::latest()->get();
        $data['unpaid_amount'] = Order::where('is_paid',0)->sum('price');
        $data['today_orders'] = Order::whereDate('created_at', now()->toDateString())->get();
        return view('admin.dashboard',$data);
    }

    /**
     * Admin profile
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function profile()
    {   $countries = Country::where('status', 'Active')->get();
        return view('admin.profile',compact('countries'));
    }

    /**
     * Admin profile update
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function profileUpdate(Request $request)
    {
        $data = $request->except('_token');
        $user = User::find(auth()->user()->id);
        $user->name = $data['name'];
        $user->phone = $data['phone'];
        $user->address = $data['address'];
        $user->city = $data['city'];
        $user->state = $data['state'];
        $user->country_id = $data['country_id'];
        $user->post_code = $data['post_code'];
        $user->save();

        return redirect('/admin/profile')->with('success', __('Address updated successfully'));
    }
}
