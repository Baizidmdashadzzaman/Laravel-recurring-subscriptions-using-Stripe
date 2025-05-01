<!DOCTYPE html>
<html>
<head>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<meta name="csrf-token" content="{{ csrf_token() }}">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<style>
* {
  box-sizing: border-box;
}

.columns {
  float: left;
  width: 50%;
  padding: 8px;
}

.price {
  list-style-type: none;
  border: 1px solid #eee;
  margin: 0;
  padding: 0;
  -webkit-transition: 0.3s;
  transition: 0.3s;
}

.price:hover {
  box-shadow: 0 8px 12px 0 rgba(0,0,0,0.2)
}

.price .header {
  background-color: #111;
  color: white;
  font-size: 25px;
}

.price li {
  border-bottom: 1px solid #eee;
  padding: 20px;
  text-align: center;
}

.price .grey {
  background-color: #eee;
  font-size: 20px;
}

.button {
  background-color: #04AA6D;
  border: none;
  color: white;
  padding: 10px 25px;
  text-align: center;
  text-decoration: none;
  font-size: 18px;
}

@media only screen and (max-width: 600px) {
  .columns {
    width: 100%;
  }
}
</style>
</head>
<body>
<h2 style="text-align:center">Update plan</h2>

<div class="columns">
  <ul class="price">
    <li class="header">Basic</li>
    <li class="grey">$ 99.00 / month</li>
    <li>10GB Storage</li>
    <li>10 Emails</li>
    <li>10 Domains</li>
    <li>1GB Bandwidth</li>
    @if($activePlanId==$planDetails[0]->id)
        <li class="grey"><a href="#" class="button">Active Plan</a></li>
    @else
    <li class="grey"><a href="javascript:void(0)"  data-plan_id="{{$planDetails[0]->id}}" class="button update_plan">Update</a></li>
    @endif
  </ul>
</div>
<div class="columns">
  <ul class="price">
    <li class="header" style="background-color:#04AA6D">Pro</li>
    <li class="grey">$ 399.00 / year</li>
    <li>25GB Storage</li>
    <li>25 Emails</li>
    <li>25 Domains</li>
    <li>2GB Bandwidth</li>

    @if($activePlanId==$planDetails[1]->id)
        <li class="grey"><a href="#" class="button">Active Plan</a></li>
    @else
        <li class="grey"><a href="javascript:void(0)" data-plan_id="{{$planDetails[1]->id}}" data-update_plan_url="{{route('update-plan')}}" class="button update_plan">Update</a></li>
    @endif
  </ul>
</div>

</body>
</html>
<script>
 $(document).on("click",".update_plan",function(){
  let text ="Are you change plan.";
  if (confirm(text) == true) {
      let plan_id= $(this).data("plan_id");
      let token = $('meta[name="csrf-token"]').attr('content');
      let URL = $(this).data("update_plan_url");
      $.ajax({
        url:URL,
        method:"POST",
        data:{plan_id:plan_id,_token:token},
        beforeSend:function(){
        },
        success:function(res){
          if(res.status){
            alert(res.message);
            window.location.href="{{route('myPlan')}}"
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