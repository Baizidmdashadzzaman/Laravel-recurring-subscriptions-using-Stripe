<!DOCTYPE html>
<html lang="en">
<head>
  <title>Bootstrap Example</title>
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
  <style type="text/css">
    .error{
      color: red;
    }
  </style>
</head>
<body>
<div class="container">

 <center>
 <!-- <img src="https://www.pngitem.com/pimgs/m/291-2918799_stripe-payment-icon-png-transparent-png.png" style="height: 200px!important;"> -->
    <h2><b>STRIPE PAYMENT FORM</b></h2></center> 
    <form class="payment-card-wrapper" action="{{route('pay')}}" id="payment-form">
        @csrf
        <input type="hidden" name="stripe_token" id="stripe_token" value="">
        <input type="hidden" id="stripe_pk" value="{{env('STRIPE_PK')}}">
        <input type="hidden" name="plan_id" value="{{$plan->uuid}}">
        <div class="intake_card_wrapper">
            <div class="intake_card">
                <div class="row payment-info-wrap">
                <div class="col-md-8">
                <div class="col-md-12">
                        <div class="mb-4">
                            <label class="form-label  label-modify">Card Number <span class="text-danger">*</span></label>
                            <input type="tel" size="20" data-stripe="number" id="cart_number" data-msg-required="Please enter card number." class="form-control cc-card-bg card-number cc-number check_token" autocomplete="cc-number" name="card_number" placeholder="**** **** **** 4242">
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="mb-4">
                            <label  class="form-label  label-modify text-left">Expiration Date <span class="text-danger">*</span></label>
                            <input type="tel" size="2" data-stripe="exp_month" id="card_expiry" data-msg-required="Please enter card expiry date." class="form-control card-expiry cc-exp check_token" autocomplete="cc-exp" name="card_expiry" placeholder="MM/YYYY">
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="mb-4">
                            <label class="form-label  label-modify text-left">CVV <span class="text-danger">*</span></label>
                            <input type="tel" size="4" data-stripe="cvc" data-msg-required="Please enter cvv number." class="form-control card-cvc cc-cvc check_token" autocomplete="off" name="card_cvv" id="card_cvv" placeholder="ex.548">
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="mb-4">
                            <label  class="form-label  label-modify text-left">Address <span class="text-danger">*</span></label>
                            <input type="text" name="address"  class="form-control" data-rule-required="true" data-msg-required="Please enter address." placeholder="Address">
                        </div>
                    </div>

                    <div class="col-md-6 col-lg-4">
                        <div class="mb-4">
                            <label class="form-label  label-modify text-left">City <span class="text-danger">*</span></label>
                            <input type="text" name="city" id="city" class="form-control " data-rule-required="true" data-msg-required="Please enter city." placeholder="City">
                        </div>
                    </div>

                    <div class="col-md-6 col-lg-4">
                        <div class="mb-4">
                            <label  class="form-label  label-modify text-left">State <span class="text-danger">*</span></label>
                            <input type="text" name="state" id="state" class="form-control " data-rule-required="true" data-msg-required="Please enter state." placeholder="State">
                        </div>
                    </div>

                    <div class="col-md-6 col-lg-4">
                        <div class="mb-4">
                            <label class="form-label  label-modify text-left">Zipcode <span class="text-danger">*</span></label>
                            <input type="text" name="zip_code" id="zipcode" class="form-control" data-rule-required="true" data-msg-required="Please enter zipcode." placeholder="Zipcode">
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="col-md-12"><br>
                            <ul class="list-group mb-3">
                                <li class="list-group-item d-flex justify-content-between lh-condensed">
                                <div>
                                    <h6 class="my-0">Product name</h6>
                                    <small class="text-muted">{{$plan->plan_name}}</small>
                                </div>
                                <span class="text-muted">${{$plan->plan_price}}</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between">
                                <span>Total (USD)</span>
                                <strong>${{$plan->plan_price}}</strong>
                                </li>
                            </ul>
                    </div>
                </div>
                    
                </div>
            </div>
        </div><br>
        <div class="card-btn-wrap">
            <button type="submit" class="btn btn-primary" id="pay_button" value="SUBMIT">
                <span class="spinner-border submit-btn-loader" role="status" style="display:none"></span>
                <span class="submit-btn-text">Pay</span>
            </button>
        </div>
    </form>
</div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.5/jquery.validate.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.payment/3.0.0/jquery.payment.min.js" ></script>
<script src="https://js.stripe.com/v2/"></script>
<script>

$(document).ready(function(){

    $(document).on("click",'.payment_type',function(){
        var url = $(this).data("card_pyament");
        window.location=url
    })
    // stripe code start
    $('[data-numeric]').payment('restrictNumeric');
    $('.cc-number').payment('formatCardNumber');
    $('.cc-exp').payment('formatCardExpiry');
    $('.cc-cvc').payment('formatCardCVC');

    })
    $.validator.addMethod('cardNumber', function(value, element, param){
    return $.payment.validateCardNumber(value);
    }, 'Invalid Card Number');

    $.validator.addMethod('cardExpiry', function(value, element, param){
    return $.payment.validateCardExpiry(param);
    }, 'Invalid Expiry Date');

    $.validator.addMethod('cardCVC', function(value, element, param){
    return $.payment.validateCardCVC(value, param);
    }, 'Invalid CVC Number');

    // error invalid-feedback jnj_form_text_cap
    var stripeCardPaymentForm = $('#payment-form');
    stripeCardPaymentForm.validate({
        debug: false,
        errorClass: "error invalid-feedback jnj_form_text_cap",
        errorElement: "span",
        rules: {
        amount :{
            required:true
        },
        card_number: {
            required: true,
            cardNumber: true
        },
        card_expiry: {
            required: true,
            cardExpiry: function(element){
            return $(element).payment('cardExpiryVal');
            }
        },
        card_cvv: {
            required: true,
            cardCVC: function(element){
                return $.payment.cardType($(element).parents('.stripe-card').find('.card-number').val());
            } 
        },
        address: {
            required:true
        },
        state: {
            required:true
        },
        city: {
            required:true
        },
        zip_code: {
            required:true
        },
        order_type : {
            required :true
        }
    },
    messages: {
        cardnumber: {
            required: "Please enter card number.",
            cardNumber: "Please enter valid card number."
        },
        cardexpiry: {
            required: "Please enter card expiry details.",
            cardExpiry: "Please enter valid dxpiry details."
        },
        cardcvc: {
        required: "Please Enter card cvc number.",
            cardCVC: "Please enter valid cvc number."
        }
    },
    submitHandler: function (form) {
        var URL = $(form).attr('action');
        var formData = new FormData($(form)[0]);
        let token = $('meta[name="csrf-token"]').attr('content');
        formData.append("_token",token);
        $.ajax({
            url: URL,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            beforeSend: function(){
                $('#pay_button').css('pointer-events','none');
                $('body').css('pointer-events','none');
                $('#pay_button').text("Loding.....");
            },
            success: function (res) {
                if(res.status){
                    alert(res.message)
                    window.location.href="{{route('myPlan')}}"
                }else{
                    alert(res.message)
                }
            },
            error: function (error) {
                console.log('errr',error)
            }
        });
    }
    });

    $(document).on("change",".check_token",function(){
    let card_number = $("#cart_number").val();
    let cardDate = $("#card_expiry").val()
    let cvvNumber = $("#card_cvv").val();
    if(card_number!='' && cardDate!='' && cvvNumber!=''){
        GenerateStripeToken();
    }
    })
    function GenerateStripeToken(){
    var cardDate = $("#card_expiry").val()
    var cvvNumber = $("#card_cvv").val();
    const dateMonthArry = cardDate.split(" / ");

    Stripe.setPublishableKey($("#stripe_pk").val());
    var card_number = $("#cart_number").val();
    var exp_month   = dateMonthArry[0];
    var exp_year    = dateMonthArry[1];
    var cvv_number  = cvvNumber;
    
    Stripe.createToken({
        number: card_number?card_number:'',
        cvc: cvv_number?cvv_number:'',
        exp_month: exp_month?exp_month:'',
        exp_year: exp_year?exp_year:''
    }, stripeHandleResponse);
    }

    /**
    * stripeHandleResponse
    *
    * @return void
    */
    function stripeHandleResponse(status, response) {
    if (response.error) {
        console.log(response.error.message);
    } else {
        var token = response['id'];
        $("#stripe_token").val(token);
        console.log(token);
    }
    }

    

</script>
</body>
</html>