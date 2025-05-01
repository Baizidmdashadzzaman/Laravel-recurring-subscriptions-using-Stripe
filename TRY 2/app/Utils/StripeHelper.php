<?php
 
namespace App\Utils;

use Exception;
class StripeHelper {
 
   //Function for get existing stripe customer detail
   public static function getOneCustomer($customer_id){
       $stripe = new \Stripe\StripeClient(ENV('STRIPE_SK') );
       try {
           $allCustomers = $stripe->customers->retrieve($customer_id);
           return [
               'status' => true,
               'error' => null,
               'data' => $allCustomers
           ];
       } catch (\Stripe\Exception\InvalidRequestException $e) {
           return [
               'status' => false,
               'error' => $e->getMessage()
           ];
       }catch (\Stripe\Exception\AuthenticationException $e) {
           return [
               'status' => false,
               'error' => $e->getMessage()
           ];
       } catch(Exception $e) {
           return [
               'status' => false,
               'error' => $e->getMessage()
           ];
       }
   }
 
   //Function for create new stripe customer
   public static function createCustomer($data,$token){
       $stripe = new \Stripe\StripeClient( ENV('STRIPE_SK') );
       try {
           $created = $stripe->customers->create($data);
           return [
               'status' => true,
               'error' => null,
               'data' => $created
           ];
       } catch (\Stripe\Exception\InvalidRequestException $e) {
           return [
               'status' => false,
               'error' => $e->getMessage()
           ];
       }catch (\Stripe\Exception\AuthenticationException $e) {
           return [
               'status' => false,
               'error' => $e->getMessage()
           ];
       } catch(Exception $e) {
           return [
               'status' => false,
               'error' => $e->getMessage()
           ];
       }
   }
 
   //Function for create new subscription
   public static function createSubscription($customer_id,$pirce_plan_id,$couponCode=NULL){
       $stripe = new \Stripe\StripeClient( ENV('STRIPE_SK') );
       try {
           $created = $stripe->subscriptions->create([
               'customer' => $customer_id,
               'items' => [
                  ['price' => $pirce_plan_id],
               ],
               'coupon'=>$couponCode
             ]);
            //  $referralAmount = 5;
             

            //  $stripe->invoiceItems->create([
            //     'customer' => $customer_id,
            //     'amount' => -$referralAmount * 100, // Convert referral amount to cents
            //     'currency' => 'usd', // Replace with the appropriate currency code
            //     'description' => 'Referral Discount', // Description for the invoice item
            //  ]);
            
           return [
               'status' => true,
               'error' => null,
               'data' => $created
           ];
       } catch (\Stripe\Exception\InvalidRequestException $e) {
           return [
               'status' => false,
               'error' => $e->getMessage()
           ];
       }catch (\Stripe\Exception\AuthenticationException $e) {
           return [
               'status' => false,
               'error' => $e->getMessage()
           ];
       } catch(Exception $e) {
           return [
               'status' => false,
               'error' => $e->getMessage()
           ];
       }
   }
   //Function for cancel stripe subscription
   public static function cancelSubscription($sub_id){
       $stripe = new \Stripe\StripeClient( ENV('STRIPE_SK') );
       try {
           $cancelled = $stripe->subscriptions->cancel($sub_id);
           return [
               'status' => true,
               'error' => null,
               'data' => $cancelled
           ];
       } catch (\Stripe\Exception\InvalidRequestException $e) {
           return [
               'status' => false,
               'error' => $e->getMessage()
           ];
       }catch (\Stripe\Exception\AuthenticationException $e) {
           return [
               'status' => false,
               'error' => $e->getMessage()
           ];
       } catch(Exception $e) {
           return [
               'status' => false,
               'error' => $e->getMessage()
           ];
       }
   }
}
