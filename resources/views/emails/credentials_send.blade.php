
<!DOCTYPE html>
<html lang="en">
<head>
  <title>Welcome to SWITCHFIT, the smart way to fit fitness into your life </title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <style type="text/css">
    @import url('https://fonts.googleapis.com/css?family=Montserrat:100,100i,200,200i,300,300i,400,400i,500,500i,600,600i,700,700i,800,800i,900,900i');
    @import url('https://fonts.googleapis.com/css?family=Rubik:300,300i,400,400i,500,500i,700,700i,900,900i');
    body {
        margin:0;
        background: #fff;
        font:15px/20px 'Montserrat', Arial, Helvetica, sans-serif;
        font-weight: 500;
    }
    .pdf-body strong {
        display: block;
        font-size: 18px;
        margin: 0 0 5px;
    }
    .order-detials-sec p {
        margin: 0 0 30px;
        display: block;
        padding: 0;
    }
    .border {
        border-top: 1px solid #3d3d3d4d;
        padding: 50px 25px 20px 0;
    }
    .btn-show {
        transition: all 0.5s ease;
        background: #25408F;
        font-size: 14px !important;
        color: #fff !important;
        text-decoration: none !important;
        padding: 5px 20px;
        font-style: normal !important;
        box-shadow: 0 4px 9px 0 rgba(0, 0, 0, 0.2);
        text-shadow: 1px 1px 1px rgba(0, 0, 0, 0.14);
    }
    .txt {
        min-height: 50px;
    }
    .footer{
        display: block;
        background-color:#fafafa;
        height: 70px;
        padding:10px; 
    }
    .social-icon{
        list-style: none;
        margin:20px auto 0;
        display: block;
        text-align: center;
    }
    .social-icon li{
        display: inline-block;
        margin:0 10px 0 0;
    }
</style>
</head>
<body>
    <div style="display: block;max-width: 650px;margin:0 auto;"> 
        <table style="width:100%;">

            <tr>
                <td colspan="2" style="vertical-align: top;">
                    <div class="banner" style="width: 100%;margin:0 0 15px">
                        <img src="{{ url('/') }}/assets/images/email/slide2.jpg" style="max-width: 100%;"  />
                    </div>
                </td>
            </tr>
            <tr>
                <td colspan="2" style="vertical-align: top;">
                    <div style="padding: 25px 0">
                        <strong><span class="custom-name">Dear </span>  
                        Gym Owner, </strong>    
                    </div>
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    <div class="order-detials-sec">


                        <p style="padding:0;margin:0;">Access multiple gyms, all over the UK. No inductions. No contracts. No sign-up fees. Make life work out. Life is unpredictable. We can never be sure what’s around the corner. So what we all need now is flexibility. Flexible Fitness.</p>

                        <p style="padding:0;margin:0;">You can now login to switchFit through given credantials.</p>

                        <strong>Email:</strong>{{$user['email']}}
                        <br>
                        <strong>Password:</strong>{{$password}}
                        <br>
                        <br>
                        <a href="{{ url('/') }}/login" style="width: 100px;height: 40px;line-height: 40px;background-color: #432E92;border:0;color:#fff;border-radius: 5px;font-family:'Montserrat';font-size: 16px;margin:10px 0 0 0; padding: 10px;text-decoration: none;">Login Now</a>
                        </td>
                    </tr>
                </table>
                <table style="width:100%;">
                    <tr>
                        <td >
                            <div class="footer">
                                <ul class="social-icon">
                                    <li>
                                        <a href="#"><img src="{{ url('/') }}/assets/images/email/1.png" alt="facebook"></a>
                                    </li>
                                    <li>
                                        <a href="#"><img src="{{ url('/') }}/assets/images/email/2.png" alt="facebook"></a>
                                    </li>
                                    <li>
                                        <a href="#"><img src="{{ url('/') }}/assets/images/email/3.png" alt="facebook"></a>
                                    </li>
                                </ul>
                            </div>
                        </td>
                    </tr>
                </table>
            </div>
        </body>
        </html>


