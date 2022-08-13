<table class="table table-striped table-bordered zero-configuration">
    <thead>
        <tr>
            <th>#</th>
            <th>Image</th>
            <th>Item Name</th>
            <th>Created at</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        <?php
        foreach ($getbanner as $banner) {
        ?>
        <tr id="dataid{{$banner->id}}">
            <td>{{$banner->id}}</td>
            <td><img src='{!! asset("public/images/banner/".$banner->image) !!}' class='img-fluid' style='max-height: 50px;'></td>
            <td>{{@$banner['item']->item_name}}</td>
            <td>{{$banner->created_at}}</td>
            @if (env('Environment') == 'sendbox')
                <td>
                    <span>
                        <a href="#" data-toggle="tooltip" data-placement="top" onclick="GetData('{{$banner->id}}')" title="" data-original-title="Edit">
                            <span class="badge badge-success">Edit</span>
                        </a>
                        <a href="#" data-toggle="tooltip" data-placement="top" onclick="myFunction()" title="" data-original-title="Delete">
                            <span class="badge badge-danger">Delete</span>
                        </a>
                    </span>
                </td>
            @else
                <td>
                    <span>
                        <a href="#" data-toggle="tooltip" data-placement="top" onclick="GetData('{{$banner->id}}')" title="" data-original-title="Edit">
                            <span class="badge badge-success">Edit</span>
                        </a>
                        <a href="#" data-toggle="tooltip" data-placement="top" onclick="DeleteData('{{$banner->id}}')" title="" data-original-title="Delete">
                            <span class="badge badge-danger">Delete</span>
                        </a>
                    </span>
                </td>
            @endif
        </tr>
        <?php
        }
        ?>
    </tbody>
</table>