<table class="table table-striped table-bordered zero-configuration">
    <thead>
        <tr>
            <th>#</th>
            <th>Profile Image</th>
            <th>Name</th>
            <th>Email</th>
            <th>Mobile</th>
            <th>Login With</th>
            <th>OTP Verification Status</th>
            <th>Created at</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        <?php
        foreach ($getusers as $users) {
        ?>
        <tr id="dataid{{$users->id}}">
            <td>{{$users->id}}</td>
            <td><img src='{!! asset("public/images/profile/".$users->profile_image) !!}' style="width: 100px;"></td>
            <td>{{$users->name}}</td>
            <td>{{$users->email}}</td>
            <td>{{$users->mobile}}</td>
            <td>
                @if($users->login_type == "facebook")
                    Facebook
                @elseif($users->login_type == "google")
                    Google
                @else
                    Email
                @endif
            </td>
            <td>
                @if($users->is_verified == "1")
                    Verified
                @else
                    Unverified
                @endif
            </td>
            <td>{{$users->created_at}}</td>
            <td>
                @if (env('Environment') == 'sendbox')
                    <a href="#" data-toggle="tooltip" data-placement="top" onclick="myFunction()" title="" data-original-title="Block">
                        <span class="badge badge-danger">Block</span>
                    </a>
                @else
                    @if($users->is_available == '1')
                        <a class="badge badge-danger px-2" onclick="StatusUpdate('{{$users->id}}','2')" style="color: #fff;">Block</a>
                    @else
                        <a class="badge badge-primary px-2" onclick="StatusUpdate('{{$users->id}}','1')" style="color: #fff;">Unavailable</a>
                    @endif
                @endif

                <a data-toggle="tooltip" href="{{URL::to('admin/user-details/'.$users->id)}}" data-original-title="View">
                    <span class="badge badge-warning">View</span>
                </a>
            </td>
        </tr>
        <?php
        }
        ?>
    </tbody>
</table>