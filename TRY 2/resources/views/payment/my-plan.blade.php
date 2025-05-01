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
 <center><h2><b>My Plan Details</b></h2></center> 
        <div class="intake_card_wrapper">
            <div class="intake_card">
                <div class="row payment-info-wrap">
                    <div class="col-md-12">
                        <ul class="list-group mb-3">
                            <li class="list-group-item d-flex justify-content-between lh-condensed">
                          <div>
                            <h4 class="my-0">Acive Plan</h4>
                            <small class="text-muted"><b>{{$user_plan}}</b></small>
                          </div>
                                      </li>
                          <li class="list-group-item d-flex justify-content-between lh-condensed">
                            <div>
                              <h4 class="my-0">Cancel Subscription:   <a href="javascript:void(0)" data-plan_id="1"  class="btn btn-danger cancel_plan">Cancel</a></h4>
                              
                            </div>
                          </li>
                            <li class="list-group-item d-flex justify-content-between lh-condensed">
                              <div>
                                <a href="{{route('logout')}}">logout</a>
                              </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

</body>
</html>
<script>
 $(document).on("click",".cancel_plan",function(){
  let text ="Are you want to cancel plan.";
  if (confirm(text) == true) {
      let token = $('meta[name="csrf-token"]').attr('content');
      $.ajax({
        url:"{{route('cancel-subscription')}}",
        method:"POST",
        data:{_token:token},
        beforeSend:function(){
        },
        success:function(res){
          if(res.status){
            alert(res.message);
            window.location.href="{{route('plan')}}"
          }else{
            alert(res.message);
          }
        },
        error:function(res){
          console.log(res)
        }
      })
  } 

 })
</script>