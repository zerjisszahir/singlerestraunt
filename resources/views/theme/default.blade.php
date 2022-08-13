<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Restaurant Admin</title>
    <!-- Favicon icon -->
    <link rel="icon" type="image/png" sizes="16x16" href="{!! asset('public/assets/images/favicon.png') !!}">
    <!-- Pignose Calender -->
    <link href="{!! asset('public/assets/plugins/pg-calendar/css/pignose.calendar.min.css') !!}" rel="stylesheet">
    <!-- Chartist -->
    <link rel="stylesheet" href="{!! asset('public/assets/plugins/chartist/css/chartist.min.css') !!}">
    <link rel="stylesheet" href="{!! asset('public/assets/plugins/chartist-plugin-tooltips/css/chartist-plugin-tooltip.css') !!}">

    <link href="{!! asset('public/assets/plugins/tables/css/datatable/dataTables.bootstrap4.min.css') !!}" rel="stylesheet">
    <!-- Custom Stylesheet -->
    <link href="{!! asset('public/assets/plugins/sweetalert/css/sweetalert.css') !!}" rel="stylesheet">

    <!-- Date picker plugins css -->
    <link href="{!! asset('public/assets/plugins/bootstrap-datepicker/bootstrap-datepicker.min.css') !!}" rel="stylesheet">
    <!-- Daterange picker plugins css -->
    <link href="{!! asset('public/assets/css/style.css') !!}" rel="stylesheet">

</head>
<body>

    <!--*******************
        Preloader start
    ********************-->
    <div id="preloader">
        <div class="loader">
            <img src="{!! asset('public/front/images/loader.gif') !!}" style="width: 80px;">
        </div>
    </div>
    <!--*******************
        Preloader end
    ********************-->

    <div id="main-wrapper">

        @include('theme.header')
        @include('theme.sidebar')
        <div class="content-body">
            @yield('content')
        </div>
        
        <!-- /#page-wrapper -->
        <div class="card-content collapse show">
          <div class="card-body">
            <div class="row my-2">
              <div class="col-lg-4 col-md-6 col-sm-12">
                <div class="form-group">
                  <!-- Modal Change Password-->
                  <div class="modal fade text-left" id="ChangePasswordModal" tabindex="-1" role="dialog" aria-labelledby="RditProduct"
                  aria-hidden="true">
                    <div class="modal-dialog" role="document">
                      <div class="modal-content">
                        <div class="modal-header">
                          <label class="modal-title text-text-bold-600" id="RditProduct">Change Password</label>
                          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                          </button>
                        </div>
                        <div id="errors" style="color: red;"></div>
                        
                        <form method="post" id="change_password_form">
                        {{csrf_field()}}
                          <div class="modal-body">
                            <label>Old Passwod: </label>
                            <div class="form-group">
                                <input type="password" placeholder="Enter Old Password" class="form-control" name="oldpassword" id="oldpassword">
                            </div>

                            <label>New Password: </label>
                            <div class="form-group">
                                <input type="password" placeholder="Enter New Password" class="form-control" name="newpassword" id="newpassword">
                            </div>

                            <label>Confirm Password: </label>
                            <div class="form-group">
                                <input type="password" placeholder="Enter Confirm Password" class="form-control" name="confirmpassword" id="confirmpassword">
                            </div>

                          </div>
                          <div class="modal-footer">
                            <input type="reset" class="btn btn-outline-secondary btn-lg" data-dismiss="modal"
                            value="Close">
                            @if (env('Environment') == 'sendbox')
                                <input type="button" onclick="myFunction()" class="btn btn-outline-primary btn-lg" value="Update">
                            @else
                                <input type="button" onclick="changePassword()" class="btn btn-outline-primary btn-lg" value="Update">
                            @endif
                            
                          </div>
                        </form>
                      </div>
                    </div>
                  </div>

                    <!-- Modal Settings-->
                    <div class="modal fade text-left" id="Selltings" tabindex="-1" role="dialog" aria-labelledby="RditProduct"
                    aria-hidden="true">
                      <div class="modal-dialog modal-lg" role="document">
                        <div class="modal-content">
                          <div class="modal-header">
                            <label class="modal-title text-text-bold-600" id="RditProduct">Settings</label>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                              <span aria-hidden="true">&times;</span>
                            </button>
                          </div>
                          <div id="errors" style="color: red;"></div>
                          
                          <form method="post" id="settings">
                          {{csrf_field()}}
                            <div class="modal-body">

                              <div class="container col-md-12">
                                  <div class="row">
                                      <div class="col-sm-3 col-md-6">
                                          <div class="form-group">
                                              <label>Tax (%): </label>
                                              <div class="form-group">
                                                  <input type="text" placeholder="Enter Tax in percentage (%)" value="{{{Auth::user()->tax}}}" class="form-control" name="tax" id="tax">
                                              </div>
                                          </div>
                                      </div>

                                      <div class="col-sm-3 col-md-6">
                                          <div class="form-group">
                                              <label>Delivery Charge: </label>
                                              <input type="text" placeholder="Delivery Charge" value="{{{Auth::user()->delivery_charge}}}" class="form-control" name="delivery_charge" id="delivery_charge">
                                          </div>
                                      </div>
                                  </div>
                              </div>

                              <div class="container col-md-12">
                                  <div class="row">
                                      <div class="col-sm-3 col-md-6">
                                          <div class="form-group">
                                              <label>Referral Amount: </label>
                                              <input type="text" placeholder="Referral Amount" value="{{{Auth::user()->referral_amount}}}" class="form-control" name="referral_amount" id="referral_amount">
                                          </div>
                                      </div>

                                      <div class="col-sm-3 col-md-6">
                                          <div class="form-group">
                                              <label>Currency: </label>
                                              <input type="text" placeholder="Currency" value="{{{Auth::user()->currency}}}" class="form-control" name="currency" id="currency">
                                          </div>
                                      </div>
                                  </div>
                              </div>

                              <div class="container col-md-12">
                                <div class="row">
                                  <div class="col-sm-3 col-md-12">
                                     <label>Get current Location: </label>
                                    <div class="form-group">
                                        <a href="#" class="badge badge-primary px-2" onclick="getLocation()" >
                                            Click here to get your current location
                                        </a>
                                    </div>
                                  </div>
                                </div>
                              </div>

                              <div class="container col-md-12">
                                  <div class="row">
                                      <div class="col-sm-3 col-md-6">
                                          <div class="form-group">
                                              <label>Latitude: </label>
                                              <div class="form-group">
                                                  <input type="text" class="form-control" name="lat" id="lat" value="{{{Auth::user()->lat}}}">
                                              </div>
                                          </div>
                                      </div>

                                      <div class="col-sm-3 col-md-6">
                                          <div class="form-group">
                                              <label>Longitude: </label>
                                              <input type="text" class="form-control" name="lang" id="lang" value="{{{Auth::user()->lang}}}">
                                          </div>
                                      </div>
                                  </div>
                              </div>

                              <div class="container col-md-12">
                                  <div class="row">
                                      <div class="col-sm-3 col-md-4">
                                          <div class="form-group">
                                              <label>Max. Order QTY: </label>
                                              <div class="form-group">
                                                  <input type="text" placeholder="Max. Order QTY" value="{{{Auth::user()->max_order_qty}}}" class="form-control" name="max_order_qty" id="max_order_qty">
                                              </div>
                                          </div>
                                      </div>

                                      <div class="col-sm-3 col-md-4">
                                          <div class="form-group">
                                              <label>Min. Order Amount: </label>
                                              <input type="text" placeholder="Min. Order Amount" value="{{{Auth::user()->min_order_amount}}}" class="form-control" name="min_order_amount" id="min_order_amount">
                                          </div>
                                      </div>

                                      <div class="col-sm-3 col-md-4">
                                          <div class="form-group">
                                              <label>Max. Order Amount: </label>
                                              <input type="text" placeholder="Max. Order Amount" value="{{{Auth::user()->max_order_amount}}}" class="form-control" name="max_order_amount" id="max_order_amount">
                                          </div>
                                      </div>
                                  </div>
                              </div>

                              <div class="container col-md-12">
                                  <div class="row">
                                      <div class="col-sm-3 col-md-6">
                                          <div class="form-group">
                                              <label>Firebase Key: </label>
                                              <div class="form-group">
                                                  <input type="text" class="form-control" name="firebase" id="firebase" value="{{{Auth::user()->firebase}}}">
                                              </div>
                                          </div>
                                      </div>

                                      <div class="col-sm-3 col-md-6">
                                          <div class="form-group">
                                              <label>Map Key: </label>
                                              <input type="text" class="form-control" name="map" id="map" value="{{{Auth::user()->map}}}">
                                          </div>
                                      </div>
                                  </div>
                              </div>

                              <div class="container col-md-12">
                                <div class="row">
                                  <div class="col-sm-3 col-md-12">
                                    <label>Timezone: </label>
                                    <div class="form-group">
                                        <select class="form-control selectpicker" name="timezone" id="timezone" data-live-search="true">
                                          <option value="">Select your Timezone</option>
                                          <option value="Pacific/Midway" {{Auth::user()->timezone == "Pacific/Midway"  ? 'selected' : ''}}>(GMT-11:00) Midway Island, Samoa</option>
                                          <option value="America/Adak" {{Auth::user()->timezone == "America/Adak"  ? 'selected' : ''}}>(GMT-10:00) Hawaii-Aleutian</option>
                                          <option value="Etc/GMT+10" {{Auth::user()->timezone == "Etc/GMT+10"  ? 'selected' : ''}}>(GMT-10:00) Hawaii</option>
                                          <option value="Pacific/Marquesas" {{Auth::user()->timezone == "Pacific/Marquesas"  ? 'selected' : ''}}>(GMT-09:30) Marquesas Islands</option>
                                          <option value="Pacific/Gambier" {{Auth::user()->timezone == "Pacific/Gambier"  ? 'selected' : ''}}>(GMT-09:00) Gambier Islands</option>
                                          <option value="America/Anchorage" {{Auth::user()->timezone == "America/Anchorage"  ? 'selected' : ''}}>(GMT-09:00) Alaska</option>
                                          <option value="America/Ensenada" {{Auth::user()->timezone == "America/Ensenada"  ? 'selected' : ''}}>(GMT-08:00) Tijuana, Baja California</option>
                                          <option value="Etc/GMT+8" {{Auth::user()->timezone == "Etc/GMT+8"  ? 'selected' : ''}}>(GMT-08:00) Pitcairn Islands</option>
                                          <option value="America/Los_Angeles" {{Auth::user()->timezone == "America/Los_Angeles"  ? 'selected' : ''}}>(GMT-08:00) Pacific Time (US & Canada)</option>
                                          <option value="America/Denver" {{Auth::user()->timezone == "America/Denver"  ? 'selected' : ''}}>(GMT-07:00) Mountain Time (US & Canada)</option>
                                          <option value="America/Chihuahua" {{Auth::user()->timezone == "America/Chihuahua"  ? 'selected' : ''}}>(GMT-07:00) Chihuahua, La Paz, Mazatlan</option>
                                          <option value="America/Dawson_Creek" {{Auth::user()->timezone == "America/Dawson_Creek"  ? 'selected' : ''}}>(GMT-07:00) Arizona</option>
                                          <option value="America/Belize" {{Auth::user()->timezone == "America/Belize"  ? 'selected' : ''}}>(GMT-06:00) Saskatchewan, Central America</option>
                                          <option value="America/Cancun" {{Auth::user()->timezone == "America/Cancun"  ? 'selected' : ''}}>(GMT-06:00) Guadalajara, Mexico City, Monterrey</option>
                                          <option value="Chile/EasterIsland" {{Auth::user()->timezone == "Chile/EasterIsland"  ? 'selected' : ''}}>(GMT-06:00) Easter Island</option>
                                          <option value="America/Chicago" {{Auth::user()->timezone == "America/Chicago"  ? 'selected' : ''}}>(GMT-06:00) Central Time (US & Canada)</option>
                                          <option value="America/New_York" {{Auth::user()->timezone == "America/New_York"  ? 'selected' : ''}}>(GMT-05:00) Eastern Time (US & Canada)</option>
                                          <option value="America/Havana" {{Auth::user()->timezone == "America/Havana"  ? 'selected' : ''}}>(GMT-05:00) Cuba</option>
                                          <option value="America/Bogota" {{Auth::user()->timezone == "America/Bogota"  ? 'selected' : ''}}>(GMT-05:00) Bogota, Lima, Quito, Rio Branco</option>
                                          <option value="America/Caracas" {{Auth::user()->timezone == "America/Caracas"  ? 'selected' : ''}}>(GMT-04:30) Caracas</option>
                                          <option value="America/Santiago" {{Auth::user()->timezone == "America/Santiago"  ? 'selected' : ''}}>(GMT-04:00) Santiago</option>
                                          <option value="America/La_Paz" {{Auth::user()->timezone == "America/La_Paz"  ? 'selected' : ''}}>(GMT-04:00) La Paz</option>
                                          <option value="Atlantic/Stanley" {{Auth::user()->timezone == "Atlantic/Stanley"  ? 'selected' : ''}}>(GMT-04:00) Faukland Islands</option>
                                          <option value="America/Campo_Grande" {{Auth::user()->timezone == "America/Campo_Grande"  ? 'selected' : ''}}>(GMT-04:00) Brazil</option>
                                          <option value="America/Goose_Bay" {{Auth::user()->timezone == "America/Goose_Bay"  ? 'selected' : ''}}>(GMT-04:00) Atlantic Time (Goose Bay)</option>
                                          <option value="America/Glace_Bay" {{Auth::user()->timezone == "America/Glace_Bay"  ? 'selected' : ''}}>(GMT-04:00) Atlantic Time (Canada)</option>
                                          <option value="America/St_Johns" {{Auth::user()->timezone == "America/St_Johns" ? 'selected' : ''}}>(GMT-03:30) Newfoundland</option>
                                          <option value="America/Araguaina" {{Auth::user()->timezone == "America/Araguaina"  ? 'selected' : ''}}>(GMT-03:00) UTC-3</option>
                                          <option value="America/Montevideo" {{Auth::user()->timezone == "America/Montevideo"  ? 'selected' : ''}}>(GMT-03:00) Montevideo</option>
                                          <option value="America/Miquelon" {{Auth::user()->timezone == "America/Miquelon"  ? 'selected' : ''}}>(GMT-03:00) Miquelon, St. Pierre</option>
                                          <option value="America/Godthab" {{Auth::user()->timezone == "America/Godthab"  ? 'selected' : ''}}>(GMT-03:00) Greenland</option>
                                          <option value="America/Argentina/Buenos_Aires" {{Auth::user()->timezone == "America/Argentina/Buenos_Aires"  ? 'selected' : ''}}>(GMT-03:00) Buenos Aires</option>
                                          <option value="America/Sao_Paulo" {{Auth::user()->timezone == "America/Sao_Paulo"  ? 'selected' : ''}}>(GMT-03:00) Brasilia</option>
                                          <option value="America/Noronha" {{Auth::user()->timezone == "America/Noronha"  ? 'selected' : ''}}>(GMT-02:00) Mid-Atlantic</option>
                                          <option value="Atlantic/Cape_Verde" {{Auth::user()->timezone == "Atlantic/Cape_Verde"  ? 'selected' : ''}}>(GMT-01:00) Cape Verde Is.</option>
                                          <option value="Atlantic/Azores" {{Auth::user()->timezone == "Atlantic/Azores"  ? 'selected' : ''}}>(GMT-01:00) Azores</option>
                                          <option value="Europe/Belfast" {{Auth::user()->timezone == "Europe/Belfast"  ? 'selected' : ''}}>(GMT) Greenwich Mean Time : Belfast</option>
                                          <option value="Europe/Dublin" {{Auth::user()->timezone == "Europe/Dublin"  ? 'selected' : ''}}>(GMT) Greenwich Mean Time : Dublin</option>
                                          <option value="Europe/Lisbon" {{Auth::user()->timezone == "Europe/Lisbon"  ? 'selected' : ''}}>(GMT) Greenwich Mean Time : Lisbon</option>
                                          <option value="Europe/London" {{Auth::user()->timezone == "Europe/London"  ? 'selected' : ''}}>(GMT) Greenwich Mean Time : London</option>
                                          <option value="Africa/Abidjan" {{Auth::user()->timezone == "Africa/Abidjan"  ? 'selected' : ''}}>(GMT) Monrovia, Reykjavik</option>
                                          <option value="Europe/Amsterdam" {{Auth::user()->timezone == "Europe/Amsterdam"  ? 'selected' : ''}}>(GMT+01:00) Amsterdam, Berlin, Bern, Rome, Stockholm, Vienna</option>
                                          <option value="Europe/Belgrade" {{Auth::user()->timezone == "Europe/Belgrade"  ? 'selected' : ''}}>(GMT+01:00) Belgrade, Bratislava, Budapest, Ljubljana, Prague</option>
                                          <option value="Europe/Brussels" {{Auth::user()->timezone == "Europe/Brussels"  ? 'selected' : ''}}>(GMT+01:00) Brussels, Copenhagen, Madrid, Paris</option>
                                          <option value="Africa/Algiers" {{Auth::user()->timezone == "Africa/Algiers"  ? 'selected' : ''}}>(GMT+01:00) West Central Africa</option>
                                          <option value="Africa/Windhoek" {{Auth::user()->timezone == "Africa/Windhoek"  ? 'selected' : ''}}>(GMT+01:00) Windhoek</option>
                                          <option value="Asia/Beirut" {{Auth::user()->timezone == "Asia/Beirut"  ? 'selected' : ''}}>(GMT+02:00) Beirut</option>
                                          <option value="Africa/Cairo" {{Auth::user()->timezone == "Africa/Cairo"  ? 'selected' : ''}}>(GMT+02:00) Cairo</option>
                                          <option value="Asia/Gaza" {{Auth::user()->timezone == "Asia/Gaza"  ? 'selected' : ''}}>(GMT+02:00) Gaza</option>
                                          <option value="Africa/Blantyre" {{Auth::user()->timezone == "Africa/Blantyre"  ? 'selected' : ''}}>(GMT+02:00) Harare, Pretoria</option>
                                          <option value="Asia/Jerusalem" {{Auth::user()->timezone == "Asia/Jerusalem"  ? 'selected' : ''}}>(GMT+02:00) Jerusalem</option>
                                          <option value="Europe/Minsk" {{Auth::user()->timezone == "Europe/Minsk" ? 'selected' : ''}}>(GMT+02:00) Minsk</option>
                                          <option value="Asia/Damascus" {{Auth::user()->timezone == "Asia/Damascus" ? 'selected' : ''}}>(GMT+02:00) Syria</option>
                                          <option value="Europe/Moscow" {{Auth::user()->timezone == "Europe/Moscow"  ? 'selected' : ''}}>(GMT+03:00) Moscow, St. Petersburg, Volgograd</option>
                                          <option value="Africa/Addis_Ababa" {{Auth::user()->timezone == "Africa/Addis_Ababa"  ? 'selected' : ''}}>(GMT+03:00) Nairobi</option>
                                          <option value="Asia/Tehran" {{Auth::user()->timezone == "Asia/Tehran"  ? 'selected' : ''}}>(GMT+03:30) Tehran</option>
                                          <option value="Asia/Dubai" {{Auth::user()->timezone == "Asia/Dubai"  ? 'selected' : ''}}>(GMT+04:00) Abu Dhabi, Muscat</option>
                                          <option value="Asia/Yerevan" {{Auth::user()->timezone == "Asia/Yerevan"  ? 'selected' : ''}}>(GMT+04:00) Yerevan</option>
                                          <option value="Asia/Kabul" {{Auth::user()->timezone == "Asia/Kabul"  ? 'selected' : ''}}>(GMT+04:30) Kabul</option>
                                          <option value="Asia/Yekaterinburg" {{Auth::user()->timezone == "Asia/Yekaterinburg"  ? 'selected' : ''}}>(GMT+05:00) Ekaterinburg</option>
                                          <option value="Asia/Tashkent" {{Auth::user()->timezone == "Asia/Tashkent"  ? 'selected' : ''}}>(GMT+05:00) Tashkent</option>
                                          <option value="Asia/Kolkata" {{Auth::user()->timezone == "Asia/Kolkata"  ? 'selected' : ''}}>(GMT+05:30) Chennai, Kolkata, Mumbai, New Delhi</option>
                                          <option value="Asia/Katmandu" {{Auth::user()->timezone == "Asia/Katmandu"  ? 'selected' : ''}}>(GMT+05:45) Kathmandu</option>
                                          <option value="Asia/Dhaka" {{Auth::user()->timezone == "Asia/Dhaka"  ? 'selected' : ''}}>(GMT+06:00) Astana, Dhaka</option>
                                          <option value="Asia/Novosibirsk" {{Auth::user()->timezone == "Asia/Novosibirsk"  ? 'selected' : ''}}>(GMT+06:00) Novosibirsk</option>
                                          <option value="Asia/Rangoon" {{Auth::user()->timezone == "Asia/Rangoon"  ? 'selected' : ''}}>(GMT+06:30) Yangon (Rangoon)</option>
                                          <option value="Asia/Bangkok" {{Auth::user()->timezone == "Asia/Bangkok"  ? 'selected' : ''}}>(GMT+07:00) Bangkok, Hanoi, Jakarta</option>
                                          <option value="Asia/Krasnoyarsk" {{Auth::user()->timezone == "Asia/Krasnoyarsk"  ? 'selected' : ''}}>(GMT+07:00) Krasnoyarsk</option>
                                          <option value="Asia/Hong_Kong" {{Auth::user()->timezone == "Asia/Hong_Kong"  ? 'selected' : ''}}>(GMT+08:00) Beijing, Chongqing, Hong Kong, Urumqi</option>
                                          <option value="Asia/Irkutsk" {{Auth::user()->timezone == "Asia/Irkutsk"  ? 'selected' : ''}}>(GMT+08:00) Irkutsk, Ulaan Bataar</option>
                                          <option value="Australia/Perth" {{Auth::user()->timezone == "Australia/Perth"  ? 'selected' : ''}}>(GMT+08:00) Perth</option>
                                          <option value="Australia/Eucla" {{Auth::user()->timezone == "Australia/Eucla"  ? 'selected' : ''}}>(GMT+08:45) Eucla</option>
                                          <option value="Asia/Tokyo" {{Auth::user()->timezone == "Asia/Tokyo"  ? 'selected' : ''}}>(GMT+09:00) Osaka, Sapporo, Tokyo</option>
                                          <option value="Asia/Seoul" {{Auth::user()->timezone == "Asia/Seoul"  ? 'selected' : ''}}>(GMT+09:00) Seoul</option>
                                          <option value="Asia/Yakutsk" {{Auth::user()->timezone == "Asia/Yakutsk"  ? 'selected' : ''}}>(GMT+09:00) Yakutsk</option>
                                          <option value="Australia/Adelaide" {{Auth::user()->timezone == "Australia/Adelaide"  ? 'selected' : ''}}>(GMT+09:30) Adelaide</option>
                                          <option value="Australia/Darwin" {{Auth::user()->timezone == "Australia/Darwin"  ? 'selected' : ''}}>(GMT+09:30) Darwin</option>
                                          <option value="Australia/Brisbane" {{Auth::user()->timezone == "Australia/Brisbane"  ? 'selected' : ''}}>(GMT+10:00) Brisbane</option>
                                          <option value="Australia/Hobart" {{Auth::user()->timezone == "Australia/Hobart"  ? 'selected' : ''}}>(GMT+10:00) Hobart</option>
                                          <option value="Asia/Vladivostok" {{Auth::user()->timezone == "Asia/Vladivostok"  ? 'selected' : ''}}>(GMT+10:00) Vladivostok</option>
                                          <option value="Australia/Lord_Howe" {{Auth::user()->timezone == "Australia/Lord_Howe"  ? 'selected' : ''}}>(GMT+10:30) Lord Howe Island</option>
                                          <option value="Etc/GMT-11" {{Auth::user()->timezone == "Etc/GMT-11"  ? 'selected' : ''}}>(GMT+11:00) Solomon Is., New Caledonia</option>
                                          <option value="Asia/Magadan" {{Auth::user()->timezone == "Asia/Magadan"  ? 'selected' : ''}}>(GMT+11:00) Magadan</option>
                                          <option value="Pacific/Norfolk" {{Auth::user()->timezone == "Pacific/Norfolk"  ? 'selected' : ''}}>(GMT+11:30) Norfolk Island</option>
                                          <option value="Asia/Anadyr" {{Auth::user()->timezone == "Asia/Anadyr"  ? 'selected' : ''}}>(GMT+12:00) Anadyr, Kamchatka</option>
                                          <option value="Pacific/Auckland" {{Auth::user()->timezone == "Pacific/Auckland"  ? 'selected' : ''}}>(GMT+12:00) Auckland, Wellington</option>
                                          <option value="Etc/GMT-12" {{Auth::user()->timezone == "Etc/GMT-12"  ? 'selected' : ''}}>(GMT+12:00) Fiji, Kamchatka, Marshall Is.</option>
                                          <option value="Pacific/Chatham" {{Auth::user()->timezone == "Pacific/Chatham"  ? 'selected' : ''}}>(GMT+12:45) Chatham Islands</option>
                                          <option value="Pacific/Tongatapu" {{Auth::user()->timezone == "Pacific/Tongatapu"  ? 'selected' : ''}}>(GMT+13:00) Nuku'alofa</option>
                                          <option value="Pacific/Kiritimati" {{Auth::user()->timezone == "Pacific/Kiritimati"  ? 'selected' : ''}}>(GMT+14:00) Kiritimati</option>
                                        </select>
                                    </div>
                                  </div>
                                </div>
                              </div>

                            </div>
                            @if (env('Environment') == 'sendbox')
                              <div class="modal-footer">
                                <input type="reset" class="btn btn-outline-secondary btn-lg" data-dismiss="modal"
                                value="Close">
                                <input type="button" class="btn btn-outline-primary btn-lg" onclick="myFunction()"  value="Submit">
                              </div>
                            @else
                                <div class="modal-footer">
                                  <input type="reset" class="btn btn-outline-secondary btn-lg" data-dismiss="modal"
                                  value="Close">
                                  <input type="button" class="btn btn-outline-primary btn-lg" onclick="settings()"  value="Submit">
                                </div>
                            @endif
                          </form>
                        </div>
                      </div>
                    </div>

                    <!--Modal: your-modal-->
                    <div class="modal fade" id="your-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-notify modal-info" role="document">
                            <!--Content-->
                            <div class="modal-content text-center">
                                <!--Header-->
                                <div class="modal-header d-flex justify-content-center">
                                    <p class="heading">Be always up to date</p>
                                </div>

                                <!--Body-->
                                <div class="modal-body">

                                    <i class="fa fa-bell fa-4x animated rotateIn mb-4"></i>

                                    <p>New Order Arrived..</p>

                                </div>

                                <!--Footer-->
                                <div class="modal-footer flex-center">
                                    <a role="button" class="btn btn-outline-secondary-modal waves-effect" onClick="window.location.reload();" data-dismiss="modal">Ok</a>
                                </div>
                            </div>
                            <!--/.Content-->
                        </div>
                    </div>
                    <!--Modal: modalPush-->

                </div>
              </div>
            </div>
          </div>
        </div>

        @if (env('Environment') == 'sendbox')
        <div class="alert-now"><!-- Buy now button -->
          <a href="" target="_blank" class="btn gradient-pomegranate" data-toggle="modal" data-target="#iconModal" style="color: #fff; width: 150px"><i class="ft-alert-triangle"></i> Alert</a>
        </div>

        <div class="modal fade text-left" id="iconModal" tabindex="-1" role="dialog" aria-labelledby="RditProduct"
        aria-hidden="true">
          <div class="modal-dialog" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <label class="modal-title text-text-bold-600" id="RditProduct"><h3><i class="fa fa-bell" aria-hidden="true"></i> Be aware !!!</h3></label>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>              
                <div class="modal-body">
                  <h5><i class="fa fa-exclamation-triangle" aria-hidden="true"></i> Hello, Users</h5>
                    Beware from Fraud! Some People Selling This Script High Cost. You can Contact for Enquiry :
                    <a href="https://bit.ly/39g1mjI" target="_blank" class="text-danger">Gravity Infotech</a>
                    Some Functionailty Restricted! Due to Demo, Contact Orignal Author For Purchase Email : infotechgravity@gmail.com</p>
                    <h5><i class="fa fa-shield" aria-hidden="true"></i> Purchase Users Benifits</h5>
                    <p class="mb-0"><i class="fa fa-arrow-right" aria-hidden="true"></i> Real Code with Purchase Licence code</p>
                    <p class="mb-0"><i class="fa fa-arrow-right" aria-hidden="true"></i> Get All Latest Updates</p>
                    <p class="mb-0"><i class="fa fa-arrow-right" aria-hidden="true"></i> Bugs free code</p>
                    <p><i class="fa fa-arrow-right" aria-hidden="true"></i> Excellent Pre Question Support</p>
                    <hr>
                  <h5><i class="fa fa-envelope" aria-hidden="true"></i> Contact at : <a href="mailto:infotechgravity@gmail.com">infotechgravity@gmail.com</a></h5>
                </div>
            </div>
          </div>
        </div>
        @endif

        <!--**********************************
            Footer start
        ***********************************-->
        <div class="footer">
            <div class="copyright">
                <p>Copyright &copy; Designed & Developed by <a href="https://infotechgravity.com" target="_blank">Gravity Infotech</a> 2020</p>
            </div>
        </div>
        <!--**********************************
            Footer end
        ***********************************-->
    </div>
    <!-- /#wrapper -->

    @include('theme.script')

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.1/jquery.validate.min.js"></script>
    <script type="text/javascript">
        function myFunction() {
          alert("You don't have rights in Demo Admin panel");
        }
    </script>
    <script type="text/javascript">
        function getLocation() {
          if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(showPosition);
          } else { 
            x.innerHTML = "Geolocation is not supported by this browser.";
          }
        }

        function showPosition(position) {

            $('#lat').val(position.coords.latitude);
            $('#lang').val(position.coords.longitude);
        }
    </script>
    <script type="text/javascript">
        function changePassword(){     
            var oldpassword=$("#oldpassword").val();
            var newpassword=$("#newpassword").val();
            var confirmpassword=$("#confirmpassword").val();
            var CSRF_TOKEN = $('input[name="_token"]').val();
            
            if($("#change_password_form").valid()) {
                $('#preloader').show();
                $.ajax({
                    headers: {
                        'X-CSRF-Token': CSRF_TOKEN 
                    },
                    url:"{{ url('admin/changePassword') }}",
                    method:'POST',
                    data:{'oldpassword':oldpassword,'newpassword':newpassword,'confirmpassword':confirmpassword},
                    dataType:"json",
                    success:function(data){
                      $('#preloader').hide();
                        $("#loading-image").hide();
                        if(data.error.length > 0)
                        {
                            var error_html = '';
                            for(var count = 0; count < data.error.length; count++)
                            {
                                error_html += '<div class="alert alert-danger mt-1">'+data.error[count]+'</div>';
                            }
                            $('#errors').html(error_html);
                            setTimeout(function(){
                                $('#errors').html('');
                            }, 10000);
                        }
                        else
                        {
                            location.reload();
                        }
                    },error:function(data){
                       $('#preloader').hide();
                    }
                });
            }
        }

        function settings(){     
            var currency=$("#currency").val();
            var tax=$("#tax").val();
            var delivery_charge=$("#delivery_charge").val();
            var max_order_qty=$("#max_order_qty").val();
            var min_order_amount=$("#min_order_amount").val();
            var max_order_amount=$("#max_order_amount").val();
            var lat=$("#lat").val();
            var lang=$("#lang").val();
            var map=$("#map").val();
            var firebase=$("#firebase").val();
            var referral_amount=$("#referral_amount").val();
            var timezone=$("#timezone").val();
            var CSRF_TOKEN = $('input[name="_token"]').val();
            
            if($("#settings").valid()) {
                $('#preloader').show();
                $.ajax({
                    headers: {
                        'X-CSRF-Token': CSRF_TOKEN 
                    },
                    url:"{{ url('admin/settings') }}",
                    method:'POST',
                    data:{'currency':currency,'tax':tax,'lat':lat,'lang':lang,'delivery_charge':delivery_charge,'max_order_qty':max_order_qty,'min_order_amount':min_order_amount,'max_order_amount':max_order_amount,'map':map,'firebase':firebase,'referral_amount':referral_amount,'timezone':timezone},
                    dataType:"json",
                    beforeSend: function() {
                      $('#preloader').hide();
                    },
                    success:function(data){
                        $('#preloader').hide();
                        if(data.error.length > 0)
                        {
                            var error_html = '';
                            for(var count = 0; count < data.error.length; count++)
                            {
                                error_html += '<div class="alert alert-danger mt-1">'+data.error[count]+'</div>';
                            }
                            $('#errors').html(error_html);
                            setTimeout(function(){
                                $('#errors').html('');
                            }, 10000);
                        }
                        else
                        {
                            location.reload();
                        }
                    },error:function(data){
                       $('#preloader').hide();
                    }
                });
            }
        }

        $(document).ready(function() {
            $( "#settings" ).validate({
                rules :{
                    currency:{
                        required: true
                    },
                    tax: {
                        required: true,
                    },                    
                },

            });        
        });

        $(document).ready(function() {
            $( "#change_password_form" ).validate({
                rules :{
                    oldpassword:{
                        required: true,
                        minlength:6
                    },
                    newpassword: {
                        required: true,
                        minlength:6,
                        maxlength:12,

                    },
                    confirmpassword: {
                        required: true,
                        equalTo: "#newpassword",
                        minlength:6,

                    },
                    
                },

            });        
        });
        var noticount = 0;

        (function noti() {
          var CSRF_TOKEN = $('input[name="_token"]').val();
          // $('#preloader').show();
          $.ajax({
              headers: {
                  'X-CSRF-Token': CSRF_TOKEN 
              },
              url:"{{ url('admin/getorder') }}",
              method: 'GET', //Get method,
              dataType:"json",
              success:function(response){
                // $('#preloader').hide();
                noticount = localStorage.getItem("count");

                $('#notificationcount').text(response);
                if (response != 0) {
                  if (noticount != response) {
                    localStorage.setItem("count", response);
                    jQuery("#your-modal").modal('show');

                    var audio = new Audio("{{url('/')}}/public/assets/notification/notification.mp3");
                    audio.play();
                  }
                }else{
                  localStorage.setItem("count", response);
                }
                setTimeout(noti, 5000);
              }
          });
        })();

        function clearnoti(){
            var CSRF_TOKEN = $('input[name="_token"]').val();
            $('#preloader').show();
            $.ajax({
                headers: {
                    'X-CSRF-Token': CSRF_TOKEN 
                },
                url:"{{ url('admin/clearnotification') }}",
                dataType:"json",
                success:function(response){
                  $('#preloader').hide();
                    console.log(response);
                }
            });
        }
        $('#tax').keyup(function(){
            var val = $(this).val();
            if(isNaN(val)){
                 val = val.replace(/[^0-9\.]/g,'');
                 if(val.split('.').length>2) 
                     val =val.replace(/\.+$/,"");
            }
            $(this).val(val); 
        });

        $('#delivery_charge').keyup(function(){
            var val = $(this).val();
            if(isNaN(val)){
                 val = val.replace(/[^0-9\.]/g,'');
                 if(val.split('.').length>2) 
                     val =val.replace(/\.+$/,"");
            }
            $(this).val(val); 
        });

        $('#min_order_amount').keyup(function(){
            var val = $(this).val();
            if(isNaN(val)){
                 val = val.replace(/[^0-9\.]/g,'');
                 if(val.split('.').length>2) 
                     val =val.replace(/\.+$/,"");
            }
            $(this).val(val); 
        });

        $('#max_order_qty').keyup(function(){
            var val = $(this).val();
            if(isNaN(val)){
                 val = val.replace(/[^0-9\.]/g,'');
                 if(val.split('.').length>2) 
                     val =val.replace(/\.+$/,"");
            }
            $(this).val(val); 
        });

        $('#max_order_amount').keyup(function(){
            var val = $(this).val();
            if(isNaN(val)){
                 val = val.replace(/[^0-9\.]/g,'');
                 if(val.split('.').length>2) 
                     val =val.replace(/\.+$/,"");
            }
            $(this).val(val); 
        });

        $('#lat').keyup(function(){
            var val = $(this).val();
            if(isNaN(val)){
                 val = val.replace(/[^0-9\.]/g,'');
                 if(val.split('.').length>2) 
                     val =val.replace(/\.+$/,"");
            }
            $(this).val(val); 
        });

        $('#lang').keyup(function(){
            var val = $(this).val();
            if(isNaN(val)){
                 val = val.replace(/[^0-9\.]/g,'');
                 if(val.split('.').length>2) 
                     val =val.replace(/\.+$/,"");
            }
            $(this).val(val); 
        });

        $('#referral_amount').keyup(function(){
            var val = $(this).val();
            if(isNaN(val)){
                 val = val.replace(/[^0-9\.]/g,'');
                 if(val.split('.').length>2) 
                     val =val.replace(/\.+$/,"");
            }
            $(this).val(val); 
        });
    </script>
</body>

</html>