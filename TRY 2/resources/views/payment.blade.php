<!DOCTYPE html>
<html lang="en">
<head>
  <title>Bootstrap Example</title>
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
  <h2>Stripe Payment form</h2>
  <form action="/action_page.php">
    <input type='hidden' name='stripe_token' id="stripe_token" value=''/>
    <input type='hidden' name='stripe_month' id="stripe_month" value=''/>
    <input type='hidden' name='stripe_year' id="stripe_year" value=''/>
    <input type='hidden' name='plan_id' id="plan_id" value="{{$planDetails->id}}"/>
    <div class="row">
      <div class="col-md-8 ">
          <div class="form-group">
            <label for="email">Card Number</label>
            <input class="form-control cc-number" type="text" placeholder="XXXX XXXX XXXX 4525" name="cart_number" id="cart_number" autocomplete="cc-number"/>
            <span id="cart_number_error" class="error invalid-feedback" style="display: none;"></span>
          </div>
      </div>
      <div class="col-md-2 ">
          <div class="form-group">
            <label for="email">Expiry Date</label>
            <input class="form-control cc-exp card_date_field" type="text" placeholder="08/2023" name="expiry_date" id="expiry_date" autocomplete="cc-number" x-autocompletetype="cc-exp" autocomplete="off"/>
          </div>
      </div>
      <div class="col-md-2 ">
          <div class="form-group">
            <label for="email">CVV Number</label>
            <input class="form-control number_only" type="tel" placeholder="123" name="cvv_number" id="cvv_number" maxlength="3"  autocomplete="off"/>
          </div>
      </div>
      
      <div class="col-md-12">
        <div class="col-md-4 ">
            <div class="form-group">
                <label for="email">Coupon Code</label>
                <input class="form-control" type="text" placeholder="Coupon code" />
            </div>
        </div>
        <div class="col-md-2 ">
            <div class="form-group"><br>

                <button class="form-control btn-success" id="checkCoupon">Apply</button>
            </div>
        </div>
      </div>
      <div class="col-md-12 ">
          <div class="form-group">
            <label for="email">User name</label>
            <input class="form-control" type="text" placeholder="Enete user name" name="user_name" id="user_name" required />
            <span id="user_name_error" class="error invalid-feedback" style="display: none;"></span>
          </div>
      </div>
      <div class="col-md-12 ">
          <div class="form-group">
            <label for="email">User Email</label>
            <input class="form-control" type="email" placeholder="Enete user Email" name="user_email" id="user_email" required />
            <span id="user_email_error" class="error invalid-feedback" style="display: none;"></span>
          </div>
      </div>
      <div class="col-md-12 ">
          <div class="form-group">
            <label for="email">User mobile</label>
            <input class="form-control number_only" type="text" placeholder="Enete user mobile number" name="user_mobile" id="user_mobile" maxlength="10" required />
            <span id="user_mobile_error" class="error invalid-feedback" style="display: none;"></span>
          </div>
      </div>
    </div>
    <button type="button" class="btn btn-default" id="pay_button">Pay amount</button>
    <a href="/plan" class="btn btn-default"> cancel</a>
  </form>
</div>

<script src="https://js.stripe.com/v2/"></script>
<script src="https://www.mdnect.com/stylesheet/assets/js/jquery.payment.js"></script>
<script type="text/javascript">
  
$(document).ready(function(){
    $("#pay_button").click(function(e){
        e.preventDefault();
        var stripe_token   = $("#stripe_token").val();
        var user_name      = $("#user_name").val();
        var user_email     = $("#user_email").val();
        var user_mobile    = $("#user_mobile").val();
        var plan_id        = $("#plan_id").val();
        // apply validation
        if(stripe_token==''){
            if($("#cart_number_error").text()==''){
                formValidation("#cart_number_error",'The card number is required')
            }
        }
        if(user_name==''){
            formValidation("#user_name_error",'The user name is required')
        }
        if(user_email==''){
            formValidation("#user_email_error",'The email address is required')
        }
        if(user_mobile==''){
            formValidation("#user_mobile_error",'The mobile number is required')
        }
        // hide error message function
        hideErrorMessage();
        if(stripe_token!='' && user_name!=''&& user_email!=''&& user_mobile!=''){
            var formData = {
                stripe_token:stripe_token,
                user_name: user_name,
                email:user_email,
                phone_number: user_mobile,
                plan_id:plan_id,
                _token:"{{csrf_token()}}"
            };
            $.ajax({
                url: '{{ route("subscription") }}',
                type: 'POST',
                data :formData,
                beforeSend: function() {
                    $('#pay_button').css('pointer-events','none');
                    $('body').css('pointer-events','none');
                    $('#pay_button').text("Loding.....");
                },
                success: function (res) {
                    if(res.status){
                        alert(res.message)
                        location.reload();
                    }else{
                        alert(res.message)
                    }
                },
                error: function (error) {
                    alert(error)
                }
            });
        }
    })

    // onchange function on input box
    $('input').on('change',function(){
        GenerateStripeToken();
        hideErrorMessage()
    })

    // card expired date
    $("#expiry_date").on('change',function(){
        var expiry_date = $(this).val();
        var date =expiry_date.split("/");
        $("#stripe_month").val(date[0])
        $("#stripe_year").val(date[1]);
        GenerateStripeToken();
    })
       
    /**
     * Generate stripe toke for payment
     *
     * @return void
     */
    // function GenerateStripeToken(){
    //     Stripe.setPublishableKey("{{ENV('STRIPE_PK')}}");
    //     var card_number = $("#cart_number").val();
    //     var exp_month   = parseInt($("#stripe_month").val());
    //     var exp_year    = parseInt($("#stripe_year").val());
    //     Stripe.createToken({
    //         number: card_number?card_number:1111,
    //         //cvc: 123,
    //         exp_month: exp_month?exp_month:11,
    //         exp_year: exp_year?exp_year:11
    //     }, stripeHandleResponse);
    // }
    
    function GenerateStripeToken(){
        Stripe.setPublishableKey('{{ENV("STRIPE_PK")}}');
        var card_number = $("#cart_number").val();
        var exp_month   = parseInt($("#stripe_month").val());
        var exp_year    = parseInt($("#stripe_year").val());
        var cvv_number  = $("#cvv_number").val();
        
        Stripe.createToken({
            number: card_number?card_number:1111,
            cvc: cvv_number?cvv_number:22,
            exp_month: exp_month?exp_month:11,
            exp_year: exp_year?exp_year:11
        }, stripeHandleResponse);
    }
    /**
     * formValidation
     *
     * @return void
     */
    function formValidation(error_id,error_message){
        $(error_id).css({"display": "inline"})
        $(error_id).text(error_message);
    }
    /**
     * hideErrorMessage
     *
     * @return void
     */
    function hideErrorMessage(){
        $( "input" ).each(function( index ) {
        if($(this).prop('required')){
                var id ='#'+$(this).prop('id')
                if($(id).val()!=''){
                    var span_error_id = id+'_error';
                    $(span_error_id).css({"display": "none"})
                    if(id=='#user_email'){
                        var pattern = /^\b[A-Z0-9._%-]+@[A-Z0-9.-]+\.[A-Z]{2,4}\b$/i;
                        if(!pattern.test($(id).val())){
                            $(span_error_id).css({"display": "inline"})
                            $(span_error_id).text('Enter valid email');
                        }
                    }else{
                        $(span_error_id).text('');
                    }
                }
            } 
        })
    }
    //stripe token generate
    $("#cart_number").on('change',function(){
        GenerateStripeToken();
    });

    /**
     * stripeHandleResponse
     *
     * @return void
     */
    function stripeHandleResponse(status, response) {
        if (response.error) {
          if(response.error.code == 'missing_payment_information'){
              formValidation("#cart_number_error",'All fields are required');
          }else if(response.error.code == 'incorrect_number'){
              formValidation("#cart_number_error",'Please enter a valid card number');
          }else if(response.error.code == 'incorrect_cvc'){
              formValidation("#cart_number_error",'Please enter valid card security code');
          }
          else if(response.error.code == 'invalid_cvc'){
              formValidation("#cart_number_error",'Please enter valid card security code');
          }
          else if(response.error.code == 'incomplete_cvc'){
              formValidation("#cart_number_error",'Please enter valid card security code');
          }
          else if(response.error.code == 'invalid_expiry_year'){
              formValidation("#cart_number_error",'Expiration year is incorrect');
          }else if(response.error.code == 'invalid_expiry_month'){
              formValidation("#cart_number_error",'Expiration month is incorrect');
          }else{
              formValidation("#cart_number_error",response.error.message);
          }
          $("#stripe_token").val('');
        } else {
            var token = response['id'];
            $("#stripe_token").val(token);
            $("#cart_number_error").css({"display": "none"})
        }
    }

    $("#checkCoupon").on("click",function(e){
        e.preventDefault();
        alert("hii");
    })
})

jQuery(function($) {
    $('.cc-number').payment('formatCardNumber');
    $('.cc-exp').payment('formatCardExpiry');
    $('.cc-cvc').payment('formatCardCVC');
});
$(".number_only").keypress(function (e) {
    //if the letter is not digit then display error and don't type anything
    if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57)) {
        return false;
    }
});

</script>

</body>
</html>