<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Plan;
use App\Models\StripeUser;
use App\Utils\StripeHelper;
use Illuminate\Http\Request;
use App\Models\UserSubscription;
use App\Models\SubscriptionDetail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\StripeSubscriptionRequest;

class StripeController extends Controller
{
    /**
     * Show stripe plans
     *
     * @return void
     */
    public function plan()
    {
        try {
            $planDetails = Plan::select('id','plan_name','uuid','plan_price')->get();
            return view('payment.plan',compact('planDetails'));
        } catch (Exception $e) {
            return redirect()->back()->with([
                'alert-type' => 'error',
                'message'    => $e->getMessage()
            ]);
        }
    }
    
    /**
     * checkout process
     *
     * @param  mixed $planId
     * @return void
     */
    public function checkOut($planId){
        try{
            $planDetails = Plan::select('uuid','plan_name','plan_price')->where(['uuid'=>$planId])->active()->first();
            if($planDetails){
                return view ('payment.payout',['plan'=>$planDetails]);
            }else{
                return redirect()->route('plan')->with([
                    'alert-type' => 'error',
                    'message'    => 'This plan not valid'
                ]);
            }
        }catch (Exception $e){
            return redirect()->back()->with([
                'alert-type' => 'error',
                'message'    => $e->getMessage()
            ]);
        }
    }
        
    /**
     * pay
     *
     * @param  mixed $request
     * @return void
     */
    public function pay(StripeSubscriptionRequest $request){
        try{
             $planId       = $request->plan_id;
             $stripeToken  = $request->stripe_token;
             $user= auth()->user();
            DB::beginTransaction();
            $planDetails  = Plan::select('*')->where('uuid',$planId)->first();
            if($planDetails){
                $stripePlanId = $planDetails->stripe_plan_id;
                $request->request->add(['plan_id' => $planDetails->id]);
                // check stripe customer in db;
                if($user->stripeUser){
                    $customerId =$user->stripeUser->stripe_customer_id;
                    $request->request->add(['stripe_user_id' => $user->stripeUser->id]);
                }else{
                    $clientObject = array(
                        'name'   => $user->name,
                        'email'  => $user->email,
                        "source" => $stripeToken,
                    );
                    // Create customer in stripe 
                    $customerRes=StripeHelper::createCustomer($clientObject,$stripeToken);
                    if($customerRes['status'] == false) {   
                        return response()->json(['status'=>false,'message' =>$customerRes['error']]);
                    }
                    $customerId =$customerRes['data']->id;
                    $stripeUser=StripeUser::create([
                        'user_id'=>$user->id,
                        'stripe_customer_id'=>$customerId
                    ]);
                    $request->request->add(['stripe_user_id' => $stripeUser->id]);
                }
                // // Create subscription in stripe
                 $subscriptionResult = StripeHelper::createSubscription($customerId,$stripePlanId);
                if($subscriptionResult['status']==false){
                    return response()->json(['status'=>false,'message' =>$subscriptionResult['error']]);
                }
                // Insert subscription details in db
                $this->userSubscription($subscriptionResult,$request);
                DB::commit();
                return response()->json(['status'=>true,'message'=>'Your subscription created successfully']);
            }else{
                return response()->json(['status'=>false,'message'=>'Invalid Plan Id']);
            }
        }catch (Exception $e) {
            DB::rollback();
            return response()->json(['status'=>false,'message' => $e->getMessage()]);
        }
    }
   
    /**
     * userSubscription
     *
     * @param  mixed $data
     * @param  mixed $request
     * @return void
     */
    public function userSubscription($data,$request=NULL){
        $subscriptionResult =$data;
        $startDate = date('Y-m-d H:i:s',$subscriptionResult['data']->current_period_start);
        $endDate   = date('Y-m-d H:i:s',$subscriptionResult['data']->current_period_end);
        $createSubscrArray = array(
            'user_name'       => $request->user_name,
            'email'           => $request->email,
            'phone_number'    => $request->phone_number,
            'plan_id'         => $request->plan_id,
            'subscription_id' => $subscriptionResult['data']->id,
            'stripe_user_id'  => $request->stripe_user_id,
            'start_date'      => $startDate,
            'end_date'        => $endDate,
            'plan_amount'     => $subscriptionResult['data']->plan->amount/100,
            'current_status'  =>UserSubscription::IS_ACTIVE_SUBSCRIPTION,
        );
        UserSubscription::create($createSubscrArray);
    }
    
     /**
     * myPlan
     *
     * @return void
     */
    public function myPlan(){
        try {
            $user =auth()->user();
            $userPlan=$user->stripeUser->activeSubscription->stripePlan->plan_name;
            $response= ['user_plan'=>$userPlan];
            return view('payment.my-plan',$response);
        } catch (Exception $e) {

            return redirect()->back()->with([
                'alert-type' => 'error',
                'message'    => $e->getMessage()
            ]);
        }
    }
    
    /**
     * cancelSubscription
     *
     * @param  mixed $request
     * @return void
     */
    public function cancelSubscription(Request $request){

        $user= auth()->user();
        $subcriptionId =$user->stripeUser->activeSubscription->subscription_id;
        $subscription = StripeHelper::cancelSubscription($subcriptionId);
        if($subscription['status']==false){
            $exception = array('status'=>false,'message' => $subscription['error']);
            return response()->json($exception);
        }
        return response()->json(['status'=>true,'message'=>'Your subscription cancel successfully']);
    }

    /**
     * logout
     *
     * @return void
     */
    public function logout(){
        Auth::logout();
        return redirect('/login');
    }

}
